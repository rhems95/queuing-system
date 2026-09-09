<?php

namespace App\Http\Controllers;

use App\Models\DailyQueueCounter;
use App\Models\Queue;
use App\Models\Service;
use App\Services\StudentQueueGuard;
use App\Services\WaitTimeEstimator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KioskController extends Controller
{
    public function __construct(
        private WaitTimeEstimator $estimator,
        private StudentQueueGuard $students,
    ) {
    }

    public function index()
    {
        // Temporarily hide Promissory Notes from kiosk choices.
        $services = Service::query()
            ->whereRaw('LOWER(service_name) NOT LIKE ?', ['%promissory%'])
            ->get();

        return view('kiosk.index', compact('services'));
    }

    /**
     * Live queue snapshot for confirm step (waiting counts + ETA).
     */
    public function estimate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'priority' => ['required', 'in:regular,priority'],
        ]);

        $snapshot = $this->estimator->snapshot(
            (int) $data['service_id'],
            $data['priority'] === 'priority'
        );

        return response()->json($snapshot);
    }

    /**
     * Confirm-step lookup: student must exist and must not already have an open ticket today.
     */
    public function lookupStudent(Request $request): JsonResponse
    {
        $raw = $this->students->normalize($request->query('student_id'));

        if ($raw === '') {
            return response()->json([
                'ok' => false,
                'error' => 'Enter your student ID.',
            ], 422);
        }

        $student = $this->students->findStudent($raw);
        if (! $student) {
            return response()->json([
                'ok' => false,
                'error' => 'Student ID was not found.',
            ], 422);
        }

        $open = $this->students->openTicketToday($raw);
        if ($open) {
            return response()->json([
                'ok' => false,
                'error' => 'This student already has an active ticket ('.$open->queue_number.').',
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'student_id' => $student->student_id,
            'name' => $student->name,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'priority' => ['required', 'in:regular,priority'],
            'student_id' => ['required', 'string', 'max:50'],
        ]);

        $studentId = $this->students->normalize($data['student_id']);
        if ($studentId === '') {
            return back()
                ->withErrors(['student_id' => 'Enter your student ID.'])
                ->withInput();
        }

        $service = Service::findOrFail($data['service_id']);
        $today = Carbon::today()->toDateString();

        try {
            $queue = DB::transaction(function () use ($data, $service, $today, $studentId) {
                $student = DB::table('students')
                    ->where('student_id', $studentId)
                    ->lockForUpdate()
                    ->first();

                if (! $student) {
                    throw new \RuntimeException('not_found');
                }

                $open = Queue::query()
                    ->where('student_id', $studentId)
                    ->whereDate('queue_date', $today)
                    ->whereIn('status', StudentQueueGuard::OPEN_STATUSES)
                    ->lockForUpdate()
                    ->first();

                if ($open) {
                    throw new \RuntimeException('already_queued:'.$open->queue_number);
                }

                $counter = DailyQueueCounter::where('service_id', $service->id)
                    ->whereDate('queue_date', $today)
                    ->lockForUpdate()
                    ->first();

                if (! $counter) {
                    $counter = DailyQueueCounter::create([
                        'service_id' => $service->id,
                        'queue_date' => $today,
                        'last_number' => 0,
                    ]);
                }

                $next = $counter->last_number + 1;
                $counter->last_number = $next;
                $counter->save();

                $numberStr = str_pad((string) $next, 3, '0', STR_PAD_LEFT);
                $queueNumber = $service->prefix.$numberStr;

                $payload = [
                    'queue_number' => $queueNumber,
                    'service_id' => $service->id,
                    'student_id' => $studentId,
                    'priority' => $data['priority'] === 'priority' ? 1 : 0,
                    'status' => 'waiting',
                    'queue_date' => $today,
                ];

                if (Schema::hasColumn('queues', 'created_at')) {
                    $payload['created_at'] = now();
                }
                if (Schema::hasColumn('queues', 'updated_at')) {
                    $payload['updated_at'] = now();
                }

                $queueId = DB::table('queues')->insertGetId($payload);

                return Queue::findOrFail($queueId);
            });
        } catch (\RuntimeException $e) {
            $msg = $e->getMessage();
            if ($msg === 'not_found') {
                return back()
                    ->withErrors(['student_id' => 'Student ID was not found.'])
                    ->withInput();
            }
            if (str_starts_with($msg, 'already_queued:')) {
                $number = substr($msg, strlen('already_queued:'));

                return back()
                    ->withErrors(['student_id' => 'This student already has an active ticket ('.$number.').'])
                    ->withInput();
            }
            throw $e;
        }

        $issuedAt = now();
        $priorityLabel = $data['priority'] === 'priority' ? 'Priority' : 'Regular';
        $estimate = $this->estimator->snapshot(
            (int) $service->id,
            $data['priority'] === 'priority',
            $queue
        );
        $estimatedMinutes = $estimate['estimated_minutes'];

        return view('kiosk.printing', compact(
            'queue',
            'service',
            'issuedAt',
            'priorityLabel',
            'estimatedMinutes'
        ));
    }
}
