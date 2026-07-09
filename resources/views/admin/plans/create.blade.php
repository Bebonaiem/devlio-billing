@extends('layouts.admin')
@section('title', 'Create Plan')
@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.plans.store') }}" class="glass rounded-2xl p-6 sm:p-8 space-y-5" x-data="{
        selectedNest: '{{ old('nest_id') }}',
        eggs: [],
        init() {
            this.$watch('selectedNest', async (nestId) => {
                if (!nestId) { this.eggs = []; return; }
                const res = await fetch('/admin/pterodactyl/nests/' + nestId + '/eggs');
                const data = await res.json();
                this.eggs = data;
            });
        }
    }">
        @csrf
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Product</label>
                <select name="product_id" required class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="priceable_type" value="App\Models\Product">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Name</label>
                <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm" value="{{ old('name') }}">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-dark-300 mb-2">Description</label>
                <textarea name="description" rows="2" class="w-full px-4 py-3 rounded-xl input-field text-white placeholder-dark-500 text-sm">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Type</label>
                <select name="type" required class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                    <option value="recurring" {{ old('type') === 'recurring' ? 'selected' : '' }}>Recurring</option>
                    <option value="one-time" {{ old('type') === 'one-time' ? 'selected' : '' }}>One-Time</option>
                    <option value="free" {{ old('type') === 'free' ? 'selected' : '' }}>Free</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Billing Period</label>
                <input type="number" name="billing_period" min="0" value="{{ old('billing_period', 1) }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Billing Unit</label>
                <select name="billing_unit" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                    <option value="day">Day</option>
                    <option value="week">Week</option>
                    <option value="month" selected>Month</option>
                    <option value="year">Year</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-dark-300 mb-2">Sort</label>
                <input type="number" name="sort" value="{{ old('sort', 0) }}" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
            </div>
        </div>
        <div class="border-t border-white/5 pt-4">
            <h4 class="text-sm font-semibold text-dark-300 mb-3">Pterodactyl Resources</h4>
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-dark-400 mb-1">Memory (MB)</label>
                    <input type="number" name="memory" min="0" class="w-full px-3 py-2.5 rounded-xl input-field text-white text-sm" value="{{ old('memory', 1024) }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-dark-400 mb-1">CPU (%)</label>
                    <input type="number" name="cpu" min="0" class="w-full px-3 py-2.5 rounded-xl input-field text-white text-sm" value="{{ old('cpu', 100) }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-dark-400 mb-1">Disk (MB)</label>
                    <input type="number" name="disk" min="0" class="w-full px-3 py-2.5 rounded-xl input-field text-white text-sm" value="{{ old('disk', 1024) }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-dark-400 mb-1">Swap (MB)</label>
                    <input type="number" name="swap" min="0" class="w-full px-3 py-2.5 rounded-xl input-field text-white text-sm" value="{{ old('swap', 0) }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-dark-400 mb-1">Databases</label>
                    <input type="number" name="databases" min="0" class="w-full px-3 py-2.5 rounded-xl input-field text-white text-sm" value="{{ old('databases', 0) }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-dark-400 mb-1">Backups</label>
                    <input type="number" name="backups" min="0" class="w-full px-3 py-2.5 rounded-xl input-field text-white text-sm" value="{{ old('backups', 0) }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-dark-400 mb-1">Allocations</label>
                    <input type="number" name="allocations" min="1" class="w-full px-3 py-2.5 rounded-xl input-field text-white text-sm" value="{{ old('allocations', 1) }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-dark-400 mb-1">Nest</label>
                    <select name="nest_id" x-model="selectedNest" class="w-full px-3 py-2.5 rounded-xl input-field text-white text-sm">
                        <option value="">Select Nest</option>
                        @foreach ($nests as $nest)
                            <option value="{{ $nest['attributes']['id'] }}" {{ old('nest_id') == $nest['attributes']['id'] ? 'selected' : '' }}>{{ $nest['attributes']['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-dark-400 mb-1">Egg</label>
                    <select name="egg_id" class="w-full px-3 py-2.5 rounded-xl input-field text-white text-sm">
                        <option value="">Select Nest First</option>
                        <template x-for="egg in eggs" :key="egg.attributes.id">
                            <option :value="egg.attributes.id" x-text="egg.attributes.name" :selected="egg.attributes.id == {{ old('egg_id') ?? 'null' }}"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>
        <p class="text-xs text-dark-500">Prices are managed per currency in the prices section.</p>
        <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm">Create Plan</button>
    </form>
</div>
@endsection