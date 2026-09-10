@extends('layouts.panel')

@section('title', 'Kiosk PIN')

@section('content')
    <div class="pecit-page-header">
        <div>
            <h1 class="pecit-page-title">Kiosk Walk-in PIN</h1>
            <p class="pecit-page-sub">Guards use this PIN on the kiosk corner button. They cannot log in.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="pecit-alert pecit-alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
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
@endsection
