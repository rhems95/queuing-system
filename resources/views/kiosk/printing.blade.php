@extends('layouts.app')

@section('title', 'Printing Ticket')

@section('content')
    <div class="max-w-md mx-auto print-wrap">
        <div class="step-pill step4a-pill">STEP 4A</div>
        <div class="print-card">
            <div class="print-head">PRINTING TICKET</div>

            <div class="print-body">
                <div id="ticketCard">
                    <div class="print-icon-circle">🖨️</div>
                    <div class="print-message">
                        Please wait while your ticket<br>is being printed...
                    </div>
                    <div class="print-dots">
                        <span></span><span></span><span class="active"></span>
                    </div>
                </div>

                <div class="print-note">
                    <span class="print-note-i">i</span>
                    <span>Do not leave the kiosk<br>until your ticket is released.</span>
                </div>
            </div>
        </div>
    </div>

    <style>
        .print-wrap {
            position: relative;
        }
        .step-pill {
            position: absolute;
            top: -18px;
            left: 50%;
            transform: translateX(-50%);
            color: #fff;
            font-weight: 800;
            border-radius: 999px;
            padding: 7px 28px;
            border: 2px solid rgba(255, 255, 255, 0.6);
            letter-spacing: 0.03em;
            z-index: 3;
        }
        .step4a-pill {
            background: linear-gradient(135deg, #f97316, #d9480f);
        }
        .print-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #f2c9a9;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
            overflow: hidden;
        }
        .print-head {
            background: linear-gradient(90deg, #f97316, #ea580c);
            color: #fff;
            font-weight: 800;
            text-align: center;
            padding: 20px 12px 16px;
            font-size: 34px;
        }
        .print-body {
            background: #fff8f1;
            padding: 24px 22px;
            text-align: center;
        }
        .print-icon-circle {
            width: 140px;
            height: 140px;
            margin: 0 auto 16px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 70px;
            background: #ffe0bb;
            color: #1d4ed8;
        }
        .print-message {
            color: #334155;
            font-size: 33px;
            font-weight: 700;
            line-height: 1.25;
        }
        .print-dots {
            margin-top: 12px;
        }
        .print-dots span {
            display: inline-block;
            width: 14px;
            height: 14px;
            border-radius: 999px;
            background: #f8c78e;
            margin: 0 5px;
        }
        .print-dots span.active {
            background: #4f83e2;
        }
        .print-note {
            margin-top: 18px;
            background: #fff1e0;
            border: 1px solid #f4d5b2;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 29px;
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-align: left;
        }
        .print-note-i {
            width: 24px;
            height: 24px;
            border-radius: 999px;
            background: #2563eb;
            color: #fff;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            flex-shrink: 0;
        }
        @media (max-width: 768px) {
            .print-head { font-size: 26px; }
            .print-message { font-size: 24px; }
            .print-note { font-size: 19px; }
        }
        @media print {
            body * {
                visibility: hidden;
            }
            #ticketCard, #ticketCard * {
                visibility: visible;
            }
            #ticketCard {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                border: 0;
                margin: 0;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var issuedEl = document.getElementById('issuedAtText');
            var issuedAt = new Date('{{ $issuedAt->toIso8601String() }}');
            if (issuedEl && !isNaN(issuedAt.getTime())) {
                issuedEl.textContent = issuedAt.toLocaleString(undefined, {
                    year: 'numeric',
                    month: 'short',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
            }

            setTimeout(function () {
                try {
                    window.print();
                } catch (e) {}
            }, 500);

            setTimeout(function () {
                window.location.href = '{{ route('kiosk') }}';
            }, 4000);
        });
    </script>
@endsection

