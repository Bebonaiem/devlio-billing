@extends('layouts.dashboard')
@section('title', 'Create Ticket')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <a href="{{ route('dashboard.tickets') }}" class="text-primary-400 hover:text-primary-300 text-sm flex items-center gap-1.5 transition mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Tickets
        </a>
        <h1 class="text-2xl font-display font-bold text-white">Create Ticket</h1>
        <p class="text-dark-400 mt-1">Submit a support request for assistance.</p>
    </div>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('dashboard.tickets.store') }}" class="glass rounded-2xl p-6 sm:p-8 space-y-5">
            @csrf

            <div>
                <label for="subject" class="block text-sm font-medium text-dark-300 mb-2">Subject</label>
                <input type="text" name="subject" id="subject" required class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('subject') }}" placeholder="Brief description of the issue">
            </div>

            <div>
                <label for="service_id" class="block text-sm font-medium text-dark-300 mb-2">Service (optional)</label>
                <select name="service_id" id="service_id" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                    <option value="">— Select a service —</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->label ?? $service->product->name ?? 'Service #' . $service->id }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="priority" class="block text-sm font-medium text-dark-300 mb-2">Priority</label>
                <select name="priority" id="priority" required class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority') === 'medium' || !old('priority') ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                </select>
            </div>

            <div>
                <label for="department" class="block text-sm font-medium text-dark-300 mb-2">Department (optional)</label>
                <input type="text" name="department" id="department" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('department') }}" placeholder="e.g. Billing, Technical">
            </div>

            <div>
                <label for="message" class="block text-sm font-medium text-dark-300 mb-2">Message</label>
                <textarea name="message" id="message" rows="6" required class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" placeholder="Describe your issue in detail...">{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm">Submit Ticket</button>
        </form>
    </div>
</div>
@endsection