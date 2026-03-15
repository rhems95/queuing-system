@extends('layouts.app')

@section('title', 'Kiosk')

@section('content')
    <div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4 text-center">Get Queue Ticket</h1>

        @if ($errors->any())
            <div class="mb-4 text-red-600 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('kiosk.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Student Name</label>
                <input type="text" name="student_name" value="{{ old('student_name') }}" required
                       class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Student ID</label>
                <input type="text" name="student_id" value="{{ old('student_id') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Service</label>
                <select name="service_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                    <option value="">Select service</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}">{{ $service->service_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Priority</label>
                <select name="priority" class="w-full border border-gray-300 rounded px-3 py-2" required>
                    <option value="regular">Regular</option>
                    <option value="parent">Parent</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Generate Ticket
            </button>
        </form>
    </div>
@endsection

