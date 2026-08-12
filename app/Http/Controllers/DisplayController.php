<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Window;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class DisplayController extends Controller
{
    public function __construct(private QueueService $queueService)
    {
    }

    /**
     * Data used for both the HTML view and the JSON poll.
     */
    private function getDisplayData(): array
    {
        $today = Carbon::today()->toDateString();

        $windows = Window::query()
            ->orderBy('id')
            ->get();

        $grouped = $windows
            ->groupBy(fn ($w) => trim((string) ($w->group_name ?? '')));

        $preferredGroupOrder = ['Window 1', 'Window 2', 'Window 3', 'Window 4'];
        $orderedGroupNames = collect($preferredGroupOrder)
            ->merge($grouped->keys()->all())
            ->unique()
            ->values()
            ->all();

        $nowServingGroups = [];
        foreach ($orderedGroupNames as $groupName) {
            if (! $grouped->has($groupName)) {
                continue;
            }
            $groupWindows = $grouped->get($groupName);
            $label = $groupName !== '' ? $groupName : 'Counters';
            // Keep a stable left-to-right order (by window id) so the display does not reshuffle on poll.
            $sortedWindows = $groupWindows->sortBy('id')->values();
            $items = [];
            foreach ($sortedWindows as $window) {
                $call = $this->queueService->latestCallForWindow($window->id, $today);
                $queueNumber = $this->queueService->queueNumberFromCall($call);

                $items[] = [
                    'window_name'  => $window->window_name,
                    'queue_number' => $queueNumber,
                    'call_token'   => $call ? ($call->id.'|'.($call->called_time ?? '')) : null,
                ];
            }

            $nowServingGroups[] = [
                'group_name' => $label,
                'windows'    => $items,
            ];
        }

        $waitingQueues = Queue::query()
            ->join('services', 'queues.service_id', '=', 'services.id')
            ->whereDate('queues.queue_date', $today)
            ->where('queues.status', 'waiting')
            ->orderByDesc('queues.priority')
            ->orderBy('queues.queue_number')
            ->orderBy('queues.id')
            ->get([
                'queues.queue_number',
                'queues.priority',
                'services.service_name',
            ]);

        $waitingColumns = [
            'Cashier'   => [],
            'N/A'       => [],
            'DMO'       => [],
            'Registrar' => [],
        ];

        foreach ($waitingQueues as $q) {
            $column = $this->waitingColumnForService((string) $q->service_name);
            if ($column === null) {
                continue;
            }

            $priorityLabel = $q->priority ? 'priority' : 'regular';
            $waitingColumns[$column][] = $q->queue_number.'('.$priorityLabel.')';
        }

        // Keep each service column to a readable on-screen length.
        foreach ($waitingColumns as $header => $tickets) {
            $waitingColumns[$header] = array_slice($tickets, 0, 10);
        }

        return [
            'now_serving_groups' => $nowServingGroups,
            'waiting_columns'    => $waitingColumns,
        ];
    }

    /**
     * Map a service name to a waiting-list column header.
     */
    private function waitingColumnForService(string $serviceName): ?string
    {
        $name = strtolower($serviceName);

        if (str_contains($name, 'cash')) {
            return 'Cashier';
        }
        if (str_contains($name, 'promissory')) {
            return 'N/A';
        }
        if (str_contains($name, 'dmo') || str_contains($name, 'data management')) {
            return 'DMO';
        }
        if (str_contains($name, 'registrar')) {
            return 'Registrar';
        }

        return null;
    }

    public function index()
    {
        $data = $this->getDisplayData();

        return view('display.index', [
            'nowServingGroups' => $data['now_serving_groups'],
            'waitingColumns'   => $data['waiting_columns'],
        ]);
    }

    /**
     * JSON endpoint for display page auto-refresh (polling).
     */
    public function data(): JsonResponse
    {
        return response()->json($this->getDisplayData());
    }
}
