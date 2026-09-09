@extends('layouts.panel')

@section('title', 'Add Student')

@section('content')
    <div class="pecit-page-header">
        <div>
            <h1 class="pecit-page-title">Add Student</h1>
            <p class="pecit-page-sub">This ID can confirm a kiosk ticket</p>
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
            <form method="POST" action="{{ route('admin.students.store') }}">
                @csrf

                <div style="margin-bottom:1rem;">
                    <label class="pecit-label">Student ID</label>
                    <input type="text" name="student_id" value="{{ old('student_id') }}" required maxlength="50" class="pecit-input" placeholder="2024-0006" autocomplete="off">
                </div>

                <div style="margin-bottom:1.25rem;">
                    <label class="pecit-label">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required maxlength="100" class="pecit-input" placeholder="Full name">
                </div>

                <div class="pecit-actions" style="margin-bottom:0;justify-content:flex-end;">
                    <a href="{{ route('admin.students.index') }}" class="pecit-btn pecit-btn-outline">Cancel</a>
                    <button type="submit" class="pecit-btn pecit-btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
@endsection
