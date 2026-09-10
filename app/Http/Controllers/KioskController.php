<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Service;
use App\Services\KioskWalkInGate;
use App\Services\StudentQueueGuard;
use App\Services\TicketIssuer;
use App\Services\WaitTimeEstimator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class KioskController extends Controller
{
    public function __construct(
        private WaitTimeEstimator $estimator,
        private StudentQueueGuard $students,
        private TicketIssuer $issuer,
        private KioskWalkInGate $walkInGate,
    ) {
    }

    public function index(Request $request)
    {
        // Temporarily hide Promissory Notes from kiosk choices.
        $services = Service::query()
            ->whereRaw('LOWER(service_name) NOT LIKE ?', ['%promissory%'])
            ->get();

        return view('kiosk.index', [
            'services' => $services,
            'walkInReasons' => TicketIssuer::walkInReasons(),
            'walkInUnlocked' => $this->walkInGate->isUnlocked($request),
            'walkInPinLength' => $this->walkInGate->pinLength(),
        ]);
    }

    public function walkInStatus(Request $request): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'unlocked' => $this->walkInGate->isUnlocked($request),
        ]);
    }

    public function walkInUnlock(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pin' => ['required', 'string', 'max:12'],
        ]);

        $result = $this->walkInGate->unlock($request, $data['pin']);
        if (! $result['ok']) {
            $status = str_contains((string) ($result['error'] ?? ''), 'Too many') ? 429 : 422;

            return response()->json($result, $status);
        }

        return response()->json(['ok' => true]);
    }

    public function walkInStore(Request $request)
    {
        $issuerId = $this->walkInGate->issuerId($request);
        if (! $issuerId) {
            return redirect()
                ->route('kiosk')
                ->withErrors(['walkin_pin' => 'Enter the staff PIN first.']);
        }

        $validator = Validator::make($request->all(), [
            'service_id' => ['required', 'exists:services,id'],
            'priority' => ['required', 'in:regular,priority'],
            'issue_reason' => ['required', Rule::in(array_keys(TicketIssuer::walkInReasons()))],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('kiosk')
                ->withErrors($validator)
                ->withInput()
                ->with('kiosk_walkin_open', true);
        }

        $data = $validator->validated();

        $service = Service::findOrFail($data['service_id']);
        if (str_contains(strtolower((string) $service->service_name), 'promissory')) {
            return redirect()
                ->route('kiosk')
                ->withErrors(['service_id' => 'That service is not available here.'])
                ->withInput()
                ->with('kiosk_walkin_open', true);
        }

        $isPriority = $data['priority'] === 'priority';

        $queue = DB::transaction(function () use ($service, $isPriority, $data, $issuerId) {
            return $this->issuer->issueWaiting(
                $service,
                $isPriority,
                null,
                $issuerId,
                $data['issue_reason'],
            );
        });

        $issuedAt = now();
        $priorityLabel = $isPriority ? 'Priority' : 'Regular';
        $estimate = $this->estimator->snapshot((int) $service->id, $isPriority, $queue);
        $estimatedMinutes = $estimate['estimated_minutes'];
        $printHomeUrl = route('kiosk');

        return view('kiosk.printing', compact(
            'queue',
            'service',
            'issuedAt',
            'priorityLabel',
            'estimatedMinutes',
            'printHomeUrl',
        ));
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

                return $this->issuer->issueWaiting(
                    $service,
                    $data['priority'] === 'priority',
                    $studentId,
                );
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
