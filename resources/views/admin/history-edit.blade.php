@extends('layouts.panel')

@section('title', 'Edit Transaction Record')

@section('content')
    <div class="pecit-page-header">
        <div>
            <h1 class="pecit-page-title">Edit Transaction Record</h1>
            <p class="pecit-page-sub">
                {{ $record->queue_number }} — {{ $record->service_name }} — Staff: {{ $record->staff_name ?? '—' }}
            </p>
        </div>
    </div>

    <div class="pecit-card" style="max-width:32rem;">
        <div class="pecit-card-body">
            <form method="POST" action="{{ route('admin.history.update', $queue_call) }}">
                @csrf
                @method('PUT')

                <div style="margin-bottom:1rem;">
                    <label class="pecit-label">Called time</label>
                    <input type="datetime-local" name="called_time" step="1"
                           value="{{ \Carbon\Carbon::parse($record->called_time)->format('Y-m-d\TH:i:s') }}"
                           class="pecit-input">
                    @error('called_time')
                        <p class="pecit-alert pecit-alert-danger" style="margin-top:0.5rem;margin-bottom:0;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom:1.25rem;">
                    <label class="pecit-label">Finished time (optional)</label>
                    <input type="datetime-local" name="finished_time" step="1"
                           value="{{ $record->finished_time ? \Carbon\Carbon::parse($record->finished_time)->format('Y-m-d\TH:i:s') : '' }}"
                           class="pecit-input">
                    @error('finished_time')
                        <p class="pecit-alert pecit-alert-danger" style="margin-top:0.5rem;margin-bottom:0;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pecit-actions" style="margin-bottom:0;">
                    <button type="submit" class="pecit-btn pecit-btn-primary">Save</button>
                    <a href="{{ route('admin.history') }}" class="pecit-btn pecit-btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
