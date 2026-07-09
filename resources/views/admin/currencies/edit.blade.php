@extends('layouts.admin')
@section('title', 'Edit Currency')
@section('content')
<div class="max-w-lg">
    <form method="POST" action="{{ route('admin.currencies.update', $currency) }}" class="glass rounded-2xl p-6 sm:p-8 space-y-5">
        @csrf @method('PATCH')
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Currency Code</label>
            <input type="text" disabled class="w-full px-4 py-3 rounded-xl input-field text-dark-400 text-sm" value="{{ $currency->code }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Name</label>
            <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('name', $currency->name) }}">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Prefix</label>
                <input type="text" name="prefix" maxlength="10" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('prefix', $currency->prefix) }}" placeholder="e.g. $">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Suffix</label>
                <input type="text" name="suffix" maxlength="10" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('suffix', $currency->suffix) }}" placeholder="e.g. USD">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-dark-300 mb-2">Format</label>
            <input type="text" name="format" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('format', $currency->format) }}" placeholder="e.g. 1,000.00">
        </div>
        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="enabled" value="1" {{ $currency->enabled ? 'checked' : '' }} class="w-4 h-4 rounded border-dark-600 bg-dark-800 text-primary-500 focus:ring-primary-500 focus:ring-offset-0">
                <span class="text-sm text-dark-300">Enabled</span>
            </label>
        </div>
        <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm">Update Currency</button>
    </form>
</div>
@endsection