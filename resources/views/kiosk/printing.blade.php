@extends('layouts.app')

@section('title', 'Your Ticket')

@section('content')
    @php
        $serviceNameLower = strtolower((string) $service->service_name);
        $printServiceName = match (true) {
            str_contains($serviceNameLower, 'data management') || str_contains($serviceNameLower, 'dmo') => 'DMO',
            str_contains($serviceNameLower, 'promissory') => 'PROMISSORY',
            default => strtoupper((string) $service->service_name),
        };
    @endphp

    <div class="max-w-3xl mx-auto print-wrap">
        <div class="screen-only">
            <div class="screen-card">
                <div class="screen-head">YOUR QUEUE NUMBER</div>
                <div class="screen-body">
                    <div class="ticket-display-card">
                        <div class="ticket-number">{{ $queue->queue_number }}</div>
                        <div class="ticket-divider"></div>
                        <div class="ticket-row"><span>Service:</span> <strong>{{ strtoupper($service->service_name) }}</strong></div>
                        <div class="ticket-divider"></div>
                        <div class="ticket-message">Please wait for your number to be called.</div>
                        <div class="ticket-divider"></div>
                        <div class="ticket-cooldown">Returning in: <span id="countdown">25</span> seconds</div>
                        <div class="ticket-divider"></div>
                        <a href="{{ route('kiosk') }}" class="ticket-home-btn">Home</a>
                    </div>
                    <p class="screen-note">If ticket did not print, please ask staff assistance.</p>
                </div>
            </div>
        </div>

        <div id="thermalTicket" class="thermal-ticket" aria-hidden="true">
            <div class="thermal-title">QUEUE TICKET</div>
            <div class="thermal-number">{{ $queue->queue_number }}</div>
            <div class="thermal-line">SERVICE: {{ $printServiceName }}</div>
            <div class="thermal-line">PRIORITY: {{ strtoupper($priorityLabel) }}</div>
            <div class="thermal-line">ISSUED: <span id="issuedAtPrint">{{ $issuedAt->format('Y-m-d H:i') }}</span></div>
            <div class="thermal-footer">PLEASE WAIT FOR YOUR NUMBER</div>
        </div>
    </div>

    <style>
        .print-wrap { position: relative; }
        .screen-card {
            background: #fff;
            border-radius: 8px;
            border: 1px solid #d7dce5;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.10);
            overflow: hidden;
        }
        .screen-head {
            background: linear-gradient(90deg, #2563eb, #1d4ed8);
            color: #fff;
            font-weight: 800;
            text-align: center;
            padding: 12px 10px;
            font-size: 24px;
            letter-spacing: 0.02em;
        }
        .screen-body {
            background: #f4f6fa;
            padding: 22px 18px;
            text-align: center;
        }
        .ticket-display-card {
            background: #fff;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            padding: 20px 16px;
            max-width: 520px;
            margin: 0 auto;
        }
        .ticket-number {
            font-size: 94px;
            font-weight: 900;
            color: #0b65c6;
            line-height: 1;
            margin-bottom: 12px;
        }
        .ticket-divider {
            border-top: 1px solid #d9dee7;
            margin: 10px 0;
        }
        .ticket-row {
            font-size: 38px;
            color: #334155;
        }
        .ticket-row strong {
            color: #0f5fb8;
            font-weight: 700;
        }
        .ticket-message {
            font-size: 26px;
            color: #475569;
        }
        .ticket-cooldown {
            font-size: 36px;
            color: #0f5fb8;
            font-weight: 800;
        }
        .ticket-home-btn {
            display: inline-block;
            min-width: 230px;
            border-radius: 8px;
            background: linear-gradient(90deg, #1f6dd6, #1d4ed8);
            color: #fff;
            font-size: 34px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 10px 12px;
        }
        .screen-note {
            margin-top: 16px;
            font-size: 20px;
            color: #64748b;
            font-style: italic;
        }
        .thermal-ticket {
            display: none;
            width: 58mm;
            max-width: 58mm;
            box-sizing: border-box;
            font-family: "Courier New", monospace;
            color: #000;
            background: #fff;
            padding: 2px 3px;
            line-height: 1.1;
            text-align: center;
        }
        .thermal-title {
            display: block;
            width: 100%;
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            margin: 0 0 2px;
        }
        .thermal-number {
            display: block;
            width: 100%;
            box-sizing: border-box;
            text-align: center;
            font-size: 28px;
            font-weight: 700;
            margin: 2px 0;
            line-height: 1;
        }
        .thermal-line {
            display: block;
            width: 100%;
            text-align: center;
            font-size: 11px;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .thermal-footer {
            display: block;
            width: 100%;
            text-align: center;
            font-size: 10px;
            margin-top: 3px;
            border-top: 1px dashed #000;
            padding-top: 2px;
        }
        @media (max-width: 1024px) {
            .ticket-number { font-size: 72px; }
            .ticket-row { font-size: 30px; }
            .ticket-cooldown { font-size: 30px; }
            .ticket-home-btn { font-size: 26px; }
        }
        @media print {
            /* Remove on-screen UI from print flow (stops huge blank paper). */
            .screen-only {
                display: none !important;
            }
            html, body {
                width: 58mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
            }
            main.container,
            main {
                margin: 0 !important;
                padding: 0 !important;
                max-width: none !important;
                width: 58mm !important;
            }
            .print-wrap {
                margin: 0 !important;
                padding: 0 !important;
                max-width: none !important;
                width: 58mm !important;
            }
            #thermalTicket {
                display: block !important;
                position: static !important;
                width: 58mm !important;
                max-width: 58mm !important;
                margin: 0 !important;
                padding: 2px 3px !important;
                box-sizing: border-box !important;
                text-align: center !important;
                line-height: 1.1 !important;
            }
            #thermalTicket .thermal-title,
            #thermalTicket .thermal-number,
            #thermalTicket .thermal-line,
            #thermalTicket .thermal-footer {
                display: block !important;
                width: 100% !important;
                text-align: center !important;
            }
            #thermalTicket .thermal-title {
                font-size: 11px !important;
                margin: 0 0 2px !important;
            }
            #thermalTicket .thermal-number {
                font-size: 28px !important;
                margin: 2px 0 !important;
                line-height: 1 !important;
            }
            #thermalTicket .thermal-line {
                font-size: 11px !important;
                margin: 0 !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
            #thermalTicket .thermal-footer {
                font-size: 10px !important;
                margin-top: 3px !important;
                padding-top: 2px !important;
            }
            @page {
                margin: 0;
                size: 58mm auto;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var issuedEl = document.getElementById('issuedAtPrint');
            var issuedAt = new Date('{{ $issuedAt->toIso8601String() }}');
            if (issuedEl && !isNaN(issuedAt.getTime())) {
                issuedEl.textContent = issuedAt.toLocaleString(undefined, {
                    year: 'numeric', month: '2-digit', day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                });
            }

            var remaining = 25;
            var countdownEl = document.getElementById('countdown');
            var timer = setInterval(function () {
                remaining -= 1;
                if (countdownEl) countdownEl.textContent = String(remaining);
                if (remaining <= 0) {
                    clearInterval(timer);
                    window.location.href = '{{ route('kiosk') }}';
                }
            }, 1000);

            setTimeout(function () {
                try {
                    window.print();
                } catch (e) {}
            }, 300);
        });
    </script>
@endsection
