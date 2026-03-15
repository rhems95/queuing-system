<?php

namespace App\Http\Controllers;

use App\Models\DailyQueueCounter;
use App\Models\Queue;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class KioskController extends Controller
{
    public function index()
    {
        $services = Service::all();

        return view('kiosk.index', compact('services'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_name' => ['required', 'string', 'max:100'],
            'student_id'   => ['nullable', 'string', 'max:50'],
            'service_id'   => ['required', 'exists:services,id'],
            'priority'     => ['required', 'in:regular,parent'],
        ]);

        $service = Service::findOrFail($data['service_id']);
        $today   = Carbon::today()->toDateString();

        $queue = DB::transaction(function () use ($data, $service, $today) {
            $counter = DailyQueueCounter::where('service_id', $service->id)
                ->whereDate('queue_date', $today)
                ->lockForUpdate()
                ->first();

            if (! $counter) {
                $counter = DailyQueueCounter::create([
                    'service_id'  => $service->id,
                    'queue_date'  => $today,
                    'last_number' => 0,
                ]);
            }

            $next = $counter->last_number + 1;
            $counter->last_number = $next;
            $counter->save();

            $numberStr   = str_pad((string) $next, 3, '0', STR_PAD_LEFT);
            $queueNumber = $service->prefix.$numberStr;

            return Queue::create([
                'student_name' => $data['student_name'],
                'student_id'   => $data['student_id'] ?? null,
                'queue_number' => $queueNumber,
                'service_id'   => $service->id,
                'priority'     => $data['priority'] === 'parent' ? 1 : 0,
                'status'       => 'waiting',
                'queue_date'   => $today,
            ]);
        });

        return view('kiosk.ticket', compact('queue'));
    }
}

