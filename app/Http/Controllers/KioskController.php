<?php

namespace App\Http\Controllers;

use App\Models\DailyQueueCounter;
use App\Models\Queue;
use App\Models\Service;
use App\Services\WaitTimeEstimator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KioskController extends Controller
{
    public function __construct(private WaitTimeEstimator $estimator)
    {
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'priority' => ['required', 'in:regular,priority'],
        ]);

        $service = Service::findOrFail($data['service_id']);
        $today = Carbon::today()->toDateString();
        $hasStudentNameColumn = Schema::hasColumn('queues', 'student_name');
        $hasStudentIdColumn = Schema::hasColumn('queues', 'student_id');

        $queue = DB::transaction(function () use ($data, $service, $today, $hasStudentNameColumn, $hasStudentIdColumn) {
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
                'priority' => $data['priority'] === 'priority' ? 1 : 0,
                'status' => 'waiting',
                'queue_date' => $today,
            ];

            // Keep kiosk compatible before/after student column removal migration.
            if ($hasStudentNameColumn) {
                $payload['student_name'] = '-';
            }
            if ($hasStudentIdColumn) {
                $payload['student_id'] = null;
            }
            if (Schema::hasColumn('queues', 'created_at')) {
                $payload['created_at'] = now();
            }
            if (Schema::hasColumn('queues', 'updated_at')) {
                $payload['updated_at'] = now();
            }

            $queueId = DB::table('queues')->insertGetId($payload);

            return Queue::findOrFail($queueId);
        });

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
