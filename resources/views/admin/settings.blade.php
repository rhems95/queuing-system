@extends('layouts.panel')

@section('title', 'Kiosk settings')

@section('content')
    <div class="pecit-page-header">
        <div>
            <h1 class="pecit-page-title">Kiosk settings</h1>
            <p class="pecit-page-sub">Walk-in PIN, and staff numbers that get a text when the printer runs out of paper.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="pecit-alert pecit-alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any() && ! $errors->getBag('paper')->any())
        <div class="pecit-alert pecit-alert-danger">
            <ul class="list-disc list-inside m-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="pecit-card" style="max-width:36rem;">
        <div class="pecit-card-body">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                @method('PUT')

                <div style="margin-bottom:1.25rem;">
                    <label class="pecit-label">Walk-in PIN</label>
                    <input type="text"
                           name="walkin_pin"
                           value="{{ old('walkin_pin', $walkinPin) }}"
                           required
                           inputmode="numeric"
                           pattern="[0-9]{4,6}"
                           maxlength="6"
                           autocomplete="off"
                           class="pecit-input">
                    <p class="pecit-page-sub" style="margin-top:0.4rem;">4 to 6 digits. Stored in the database, not in .env.</p>
                </div>

                <div class="pecit-actions" style="margin-bottom:0;justify-content:flex-end;">
                    <button type="submit" class="pecit-btn pecit-btn-primary">Save PIN</button>
                </div>
            </form>
        </div>
    </div>

    <div class="pecit-card" style="max-width:46rem;margin-top:1.15rem;">
        <div class="pecit-card-head">
            <div>
                <h2>Printer paper SMS</h2>
                <p>Staff or assistant numbers. Check <strong>Send SMS</strong> to text that phone when the kiosk printer runs out of paper. Uncheck to keep the number without sending. One text goes out when the paper first runs out, and again only after a new roll is loaded and it runs out again.</p>
            </div>
        </div>
        <div class="pecit-card-body">
            @if ($errors->getBag('paper')->any())
                <div class="pecit-alert pecit-alert-danger">
                    <ul class="list-disc list-inside m-0">
                        @foreach ($errors->getBag('paper')->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.settings.paper-sms') }}">
                @csrf
                @method('PUT')

                <div class="pecit-table-wrap">
                    <table class="pecit-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Send SMS</th>
                                <th>Remove</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($paperRows as $i => $row)
                                <tr>
                                    <td>
                                        <input type="text" name="contacts[{{ $i }}][name]" value="{{ $row['name'] }}" maxlength="80" class="pecit-input" placeholder="Assistant">
                                    </td>
                                    <td>
                                        <input type="text" name="contacts[{{ $i }}][mobile]" value="{{ $row['mobile'] }}" inputmode="numeric" maxlength="13" autocomplete="off" class="pecit-input" placeholder="09XXXXXXXXX">
                                    </td>
                                    <td>
                                        <input type="hidden" name="contacts[{{ $i }}][enabled]" value="0">
                                        <label class="pecit-label" style="display:flex;align-items:center;gap:0.4rem;margin:0;">
                                            <input type="checkbox" name="contacts[{{ $i }}][enabled]" value="1" @checked(($row['enabled'] ?? '0') === '1')>
                                            On
                                        </label>
                                    </td>
                                    <td>
                                        <label class="pecit-label" style="display:flex;align-items:center;gap:0.4rem;margin:0;">
                                            <input type="checkbox" name="contacts[{{ $i }}][remove]" value="1">
                                            Remove
                                        </label>
                                    </td>
                                </tr>
                            @endforeach
                            @php $add = count($paperRows); @endphp
                            <tr>
                                <td>
                                    <input type="text" name="contacts[{{ $add }}][name]" value="" maxlength="80" class="pecit-input" placeholder="Add a name">
                                </td>
                                <td>
                                    <input type="text" name="contacts[{{ $add }}][mobile]" value="" inputmode="numeric" maxlength="13" autocomplete="off" class="pecit-input" placeholder="09XXXXXXXXX">
                                </td>
                                <td>
                                    <input type="hidden" name="contacts[{{ $add }}][enabled]" value="0">
                                    <label class="pecit-label" style="display:flex;align-items:center;gap:0.4rem;margin:0;">
                                        <input type="checkbox" name="contacts[{{ $add }}][enabled]" value="1" checked>
                                        On
                                    </label>
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p class="pecit-page-sub" style="margin-top:0.75rem;">Leave the last row blank if you are not adding a number. Up to 8 numbers. The text is number-only and does not wait on the modem.</p>

                <div class="pecit-actions" style="margin-bottom:0;justify-content:flex-end;">
                    <button type="submit" class="pecit-btn pecit-btn-primary">Save numbers</button>
                </div>
            </form>
        </div>
    </div>
@endsection
