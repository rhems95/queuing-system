@extends('layouts.panel')

@section('title', isset($student) ? 'Edit Student' : 'Add Student')

@section('content')
    <div class="pecit-page-header">
        <div>
            <h1 class="pecit-page-title">{{ isset($student) ? 'Edit Student' : 'Add Student' }}</h1>
            <p class="pecit-page-sub">This ID and optional card serial can confirm a kiosk ticket</p>
        </div>
    </div>

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
            <form method="POST" action="{{ isset($student) ? route('admin.students.update', $student) : route('admin.students.store') }}">
                @csrf
                @if (isset($student))
                    @method('PUT')
                @endif

                <div style="margin-bottom:1rem;">
                    <label class="pecit-label">Student ID</label>
                    <input type="text" name="student_id" value="{{ old('student_id', $student->student_id ?? '') }}" required maxlength="50" class="pecit-input" placeholder="2024-0006" autocomplete="off">
                </div>

                <div style="margin-bottom:1rem;">
                    <label class="pecit-label">Name</label>
                    <input type="text" name="name" value="{{ old('name', $student->name ?? '') }}" required maxlength="100" class="pecit-input" placeholder="Full name">
                </div>

                <div style="margin-bottom:1rem;">
                    <label class="pecit-label">ID card serial (UID)</label>
                    <input type="text" name="card_uid" value="{{ old('card_uid', $student->card_uid ?? '') }}" maxlength="32" class="pecit-input" placeholder="Optional — tap later or paste hex" autocomplete="off" style="text-transform:uppercase;">
                </div>

                <div style="margin-bottom:1.25rem;">
                    <label class="pecit-label">Mobile</label>
                    <input type="text" name="mobile" value="{{ old('mobile', $student->mobile ?? '') }}" maxlength="20" class="pecit-input" placeholder="Optional — 09XXXXXXXXX" autocomplete="off">
                </div>

                <div class="pecit-actions" style="margin-bottom:0;justify-content:flex-end;">
                    <a href="{{ route('admin.students.index') }}" class="pecit-btn pecit-btn-outline">Cancel</a>
                    <button type="submit" class="pecit-btn pecit-btn-primary">{{ isset($student) ? 'Save' : 'Add' }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
