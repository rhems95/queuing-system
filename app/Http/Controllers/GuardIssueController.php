<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Service;
use App\Services\TicketIssuer;
use App\Services\WaitTimeEstimator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class GuardIssueController extends Controller
{
    public function __construct(
        private TicketIssuer $issuer,
        private WaitTimeEstimator $estimator,
    ) {
    }

    public function index()
    {
        $services = Service::query()
            ->whereRaw('LOWER(service_name) NOT LIKE ?', ['%promissory%'])
            ->orderBy('id')
            ->get();

        $today = Carbon::today()->toDateString();
        $recent = collect();
        if (Schema::hasColumn('queues', 'issued_by')) {
            $recent = Queue::query()
                ->with('service')
                ->where('issued_by', Auth::id())
                ->whereDate('queue_date', $today)
                ->orderByDesc('id')
                ->limit(15)
                ->get();
        }

        return view('guard.issue', [
            'services' => $services,
            'reasons' => TicketIssuer::walkInReasons(),
            'recent' => $recent,
            'guardId' => Auth::id(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'priority' => ['required', 'in:regular,priority'],
            'issue_reason' => ['required', Rule::in(array_keys(TicketIssuer::walkInReasons()))],
        ]);

        $service = Service::findOrFail($data['service_id']);
        if (str_contains(strtolower((string) $service->service_name), 'promissory')) {
            return back()->withErrors(['service_id' => 'That service is not available here.'])->withInput();
        }

        $isPriority = $data['priority'] === 'priority';

        $queue = DB::transaction(function () use ($service, $isPriority, $data) {
            return $this->issuer->issueWaiting(
                $service,
                $isPriority,
                null,
                (int) Auth::id(),
                $data['issue_reason'],
            );
        });

        $issuedAt = now();
        $priorityLabel = $isPriority ? 'Priority' : 'Regular';
        $estimate = $this->estimator->snapshot((int) $service->id, $isPriority, $queue);
        $estimatedMinutes = $estimate['estimated_minutes'];
        $printHomeUrl = route('guard.issue');

        return view('kiosk.printing', compact(
            'queue',
            'service',
            'issuedAt',
            'priorityLabel',
            'estimatedMinutes',
            'printHomeUrl',
        ));
    }
}
