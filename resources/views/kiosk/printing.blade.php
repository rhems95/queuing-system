<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Ticket</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $serviceNameLower = strtolower((string) $service->service_name);
        $printServiceName = match (true) {
            str_contains($serviceNameLower, 'data management') || str_contains($serviceNameLower, 'dmo') => 'DMO',
            str_contains($serviceNameLower, 'promissory') => 'PROMISSORY',
            default => strtoupper((string) $service->service_name),
        };
    @endphp
    <style>
        /* X: more negative = move left. Y: more negative = move up (less top blank). */
        :root {
            /*--thermal-nudge-x: -12mm;
            --thermal-nudge-y: 0mm;*/
        }

        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            background: #f3f4f6;
            font-family: Arial, sans-serif;
        }

        .screen-only {
            max-width: 720px;
            margin: 12px auto;
            padding: 0 10px;
        }
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
        .ticket-row { font-size: 38px; color: #334155; }
        .ticket-row strong { color: #0f5fb8; font-weight: 700; }
        .ticket-message { font-size: 26px; color: #475569; }
        .ticket-cooldown { font-size: 36px; color: #0f5fb8; font-weight: 800; }
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
            text-decoration: none;
        }
        .screen-note {
            margin-top: 16px;
            font-size: 20px;
            color: #64748b;
            font-style: italic;
        }

        #thermalTicket {
            display: none;
        }

        @media (max-width: 1024px) {
            .ticket-number { font-size: 72px; }
            .ticket-row { font-size: 30px; }
            .ticket-cooldown { font-size: 30px; }
            .ticket-home-btn { font-size: 26px; }
        }

        @media print {
            @page {
                size: 80mm auto;
                margin: 0 !important;
            }

            html, body {
                width: 80mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                color: #000 !important;
            }

            .screen-only {
                display: none !important;
            }

            #thermalTicket {
                display: block !important;
                width: 80mm !important;
                margin: 0 !important;
                padding: 1mm 0 0 0 !important;
                transform: translate(var(--thermal-nudge-x), var(--thermal-nudge-y)) !important;
                color: #000 !important;
            }

            #thermalTicket .thermal-sheet {
                width: 80mm !important;
                margin: 0 !important;
                padding: 0 !important;
                border-collapse: collapse !important;
                border-spacing: 0 !important;
            }

            #thermalTicket .thermal-cell {
                width: 80mm !important;
                padding: 0 !important;
                margin: 0 !important;
                text-align: center !important;
                vertical-align: top !important;
                font-family: "Courier New", Courier, monospace !important;
                color: #000 !important;
            }

            #thermalTicket .thermal-title,
            #thermalTicket .thermal-number,
            #thermalTicket .thermal-line,
            #thermalTicket .thermal-footer {
                display: block !important;
                width: 100% !important;
                text-align: center !important;
                margin: 0 !important;
                padding: 0 !important;
                float: none !important;
                color: #000 !important;
                visibility: visible !important;
                opacity: 1 !important;
            }

            #thermalTicket .thermal-title {
                font-size: 11px !important;
                font-weight: 700 !important;
                line-height: 1.2 !important;
            }
            #thermalTicket .thermal-number {
                font-size: 28px !important;
                font-weight: 700 !important;
                line-height: 1.25 !important;
                min-height: 28px !important;
                overflow: visible !important;
            }
            #thermalTicket .thermal-line {
                font-size: 11px !important;
                line-height: 1.2 !important;
                white-space: nowrap !important;
            }
            #thermalTicket .thermal-footer {
                font-size: 10px !important;
                line-height: 1.2 !important;
                border-top: 1px dashed #000 !important;
                padding-top: 1px !important;
                margin-top: 1px !important;
            }
        }
    </style>
</head>
<body>
    <div id="thermalTicket">
        <table class="thermal-sheet">
            <tr>
                <td class="thermal-cell" align="center">
                    <div class="thermal-title">QUEUE TICKET</div>
                    <div class="thermal-number">{{ $queue->queue_number }}</div>
                    <div class="thermal-line">SERVICE: {{ $printServiceName }}</div>
                    <div class="thermal-line">PRIORITY: {{ strtoupper($priorityLabel) }}</div>
                    <div class="thermal-line">ISSUED: <span id="issuedAtPrint">{{ $issuedAt->format('Y-m-d H:i') }}</span></div>
                    <div class="thermal-footer">PLEASE WAIT FOR YOUR NUMBER</div>
                </td>
            </tr>
        </table>
    </div>

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
                try { window.print(); } catch (e) {}
            }, 400);
        });
    </script>
</body>
</html>
