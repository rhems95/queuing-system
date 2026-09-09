@extends('layouts.panel')

@section('title', 'Students')

@section('content')
    <div class="pecit-page-header">
        <div>
            <h1 class="pecit-page-title">Students</h1>
            <p class="pecit-page-sub">IDs allowed on the kiosk confirm step</p>
        </div>
        <a href="{{ route('admin.students.create') }}" class="pecit-btn pecit-btn-primary">+ Add Student</a>
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
    @if (session('import_errors'))
        <div class="pecit-alert pecit-alert-warning">
            <ul class="list-disc list-inside m-0">
                @foreach (session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" class="pecit-filter-bar">
        <input type="text" name="q" value="{{ $q }}" class="pecit-input" placeholder="Search ID or name" maxlength="100">
        <button type="submit" class="pecit-btn pecit-btn-secondary">Search</button>
        @if ($q !== '')
            <a href="{{ route('admin.students.index') }}" class="pecit-btn pecit-btn-outline">Clear</a>
        @endif
    </form>

    <div class="pecit-card" style="margin-bottom:1.15rem;">
        <div class="pecit-card-head">
            <div>
                <h2>Bulk add from CSV</h2>
                <p>Columns: <code>student_id,name</code> — existing IDs are skipped</p>
            </div>
            <a href="{{ route('admin.students.sample') }}" class="pecit-btn pecit-btn-outline">Download sample</a>
        </div>
        <div class="pecit-card-body">
            <form method="POST" action="{{ route('admin.students.import') }}" enctype="multipart/form-data" class="pecit-filter-bar" style="margin:0;">
                @csrf
                <input type="file" name="csv" accept=".csv,.txt,text/csv" required class="pecit-input">
                <button type="submit" class="pecit-btn pecit-btn-primary">Import CSV</button>
            </form>
        </div>
    </div>

    <div class="pecit-card">
        <div class="pecit-table-wrap">
            <table class="pecit-table">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $row)
                        <tr>
                            <td style="font-weight:700;letter-spacing:0.03em;">{{ $row->student_id }}</td>
                            <td>{{ $row->name }}</td>
                            <td style="text-align:right;white-space:nowrap;">
                                <form method="POST" action="{{ route('admin.students.destroy', $row) }}"
                                      style="display:inline;"
                                      onsubmit="return confirm(@json('Delete '.$row->student_id.' ('.$row->name.')?'));">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="pecit-link-danger" style="background:none;border:none;cursor:pointer;padding:0;font:inherit;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="empty">No students found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-top:1rem;">
        {{ $records->links() }}
    </div>
@endsection
