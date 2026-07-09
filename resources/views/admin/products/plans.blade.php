@extends('layouts.admin')
@section('title', 'Manage Plans - ' . $product->name)
@section('content')
<div class="mb-6">
    <a href="{{ route('admin.products.index') }}" class="text-dark-400 hover:text-primary-400 text-sm transition">&larr; Back to Products</a>
</div>
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-display font-bold text-white">Plans for: {{ $product->name }}</h2>
    <button @click="showCreateForm = true" class="inline-flex items-center gap-2 px-4 py-2.5 btn-primary text-white text-sm font-medium rounded-xl">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Plan
    </button>
</div>

<div class="space-y-4" x-data="{
    showCreateForm: false,
    editingPlan: null,
    prices: [{{ $currencies->map(fn($c) => '{\'currency_code\': \'' . $c->code . '\', \'price\': \'\', \'setup_fee\': \'\'}')->join(', ') }}],
    selectedNest: '',
    eggs: [],
    init() {
        this.prices = [{{ $currencies->map(fn($c) => '{\'currency_code\': \'' . $c->code . '\', \'price\': \'\', \'setup_fee\': \'\'}')->join(', ') }}];
        this.$watch('selectedNest', async (nestId) => {
            if (!nestId) { this.eggs = []; return; }
            const res = await fetch('/admin/pterodactyl/nests/' + nestId + '/eggs');
            const data = await res.json();
            this.eggs = data;
        });
    },
    addPriceRow() {
        this.prices.push({currency_code: '{{ $currencies->first()?->code ?? 'USD' }}', price: '', setup_fee: ''});
    },
    removePriceRow(index) {
        if (this.prices.length > 1) this.prices.splice(index, 1);
    }
}">
    @forelse ($product->plans as $plan)
        <div class="glass rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 flex justify-between items-center">
                <div>
                    <span class="font-display font-semibold text-white">{{ $plan->name }}</span>
                    <span class="ml-3 text-xs text-dark-400 capitalize">{{ $plan->type }}</span>
                    @if ($plan->billing_period)
                        <span class="ml-2 text-xs text-dark-500">({{ $plan->billing_period }} {{ $plan->billing_unit }})</span>
                    @endif
                </div>
                <div class="flex gap-2">
                    <button @click="editingPlan = {{ $plan->id }}; showCreateForm = true" class="text-primary-400 hover:text-primary-300 text-sm">Edit</button>
                    <form method="POST" action="{{ route('admin.products.plans.destroy', [$product, $plan]) }}" class="inline" onsubmit="return confirm('Delete this plan?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Delete</button>
                    </form>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/5 bg-white/[0.02]">
                            <th class="text-left px-6 py-2 text-xs font-medium text-dark-400 uppercase tracking-wider">Currency</th>
                            <th class="text-left px-6 py-2 text-xs font-medium text-dark-400 uppercase tracking-wider">Price</th>
                            <th class="text-left px-6 py-2 text-xs font-medium text-dark-400 uppercase tracking-wider">Setup Fee</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($plan->prices as $price)
                            <tr class="border-b border-white/5">
                                <td class="px-6 py-2 text-dark-300">{{ $price->currency_code }}</td>
                                <td class="px-6 py-2 text-white">${{ number_format($price->price, 2) }}</td>
                                <td class="px-6 py-2 text-dark-400">${{ number_format($price->setup_fee, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="glass rounded-2xl p-12 text-center">
            <svg class="w-12 h-12 mx-auto text-dark-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            <p class="text-dark-500">No plans yet for this product.</p>
        </div>
    @endforelse

    <template x-if="showCreateForm">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" @click.self="showCreateForm = false; editingPlan = null">
            <div class="glass rounded-2xl p-6 sm:p-8 w-full max-w-2xl max-h-[90vh] overflow-y-auto m-4">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-display font-bold text-white" x-text="editingPlan ? 'Edit Plan' : 'Create Plan'"></h3>
                    <button @click="showCreateForm = false; editingPlan = null" class="text-dark-400 hover:text-white">&times;</button>
                </div>

                <form method="POST" :action="editingPlan ? '{{ route('admin.products.plans.update', [$product, 'PLACEHOLDER']) }}'.replace('PLACEHOLDER', editingPlan) : '{{ route('admin.products.plans.store', $product) }}'" class="space-y-5">
                    @csrf
                    <template x-if="editingPlan"><input type="hidden" name="_method" value="PATCH"></template>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-dark-300 mb-2">Name</label>
                            <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl input-field text-white text-sm" value="{{ old('name') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark-300 mb-2">Type</label>
                            <select name="type" required class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                                <option value="recurring">Recurring</option>
                                <option value="one-time">One-Time</option>
                                <option value="free">Free</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark-300 mb-2">Billing Period</label>
                            <input type="number" name="billing_period" min="1" value="1" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
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
                            <input type="number" name="sort" value="0" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                        </div>
                    </div>

                    <div class="border-t border-white/5 pt-4">
                        <h4 class="text-sm font-medium text-dark-300 mb-3">Pterodactyl Configuration</h4>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-dark-300 mb-2">Nest</label>
                                <select name="nest_id" x-model="selectedNest" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                                    <option value="">Select Nest</option>
                                    @foreach ($nests as $nest)
                                        <option value="{{ $nest['attributes']['id'] }}">{{ $nest['attributes']['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-dark-300 mb-2">Egg</label>
                                <select name="egg_id" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                                    <option value="">Select Nest First</option>
                                    <template x-for="egg in eggs" :key="egg.attributes.id">
                                        <option :value="egg.attributes.id" x-text="egg.attributes.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-dark-300 mb-2">Memory (MB)</label>
                                <input type="number" name="memory" min="0" value="1024" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-dark-300 mb-2">CPU (%)</label>
                                <input type="number" name="cpu" min="0" value="100" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-dark-300 mb-2">Disk (MB)</label>
                                <input type="number" name="disk" min="0" value="1024" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-dark-300 mb-2">Swap (MB)</label>
                                <input type="number" name="swap" min="0" value="0" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-dark-300 mb-2">Databases</label>
                                <input type="number" name="databases" min="0" value="0" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-dark-300 mb-2">Backups</label>
                                <input type="number" name="backups" min="0" value="0" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-dark-300 mb-2">Allocations</label>
                                <input type="number" name="allocations" min="0" value="1" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-white/5 pt-4">
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-sm font-medium text-dark-300">Prices</label>
                            <button type="button" @click="addPriceRow()" class="text-xs text-primary-400 hover:text-primary-300">+ Add Currency</button>
                        </div>
                        <template x-for="(price, index) in prices" :key="index">
                            <div class="flex gap-2 mb-2 items-end">
                                <div class="flex-1">
                                    <select :name="'prices[' + index + '][currency_code]'" x-model="price.currency_code" class="w-full px-3 py-2.5 rounded-xl input-field text-white text-sm">
                                        @foreach ($currencies as $currency)
                                            <option value="{{ $currency->code }}">{{ $currency->code }} - {{ $currency->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <input type="number" step="0.01" min="0" :name="'prices[' + index + '][price]'" x-model="price.price" placeholder="Price" class="w-full px-3 py-2.5 rounded-xl input-field text-white text-sm">
                                </div>
                                <div class="flex-1">
                                    <input type="number" step="0.01" min="0" :name="'prices[' + index + '][setup_fee]'" x-model="price.setup_fee" placeholder="Setup Fee" class="w-full px-3 py-2.5 rounded-xl input-field text-white text-sm">
                                </div>
                                <button type="button" @click="removePriceRow(index)" x-show="prices.length > 1" class="p-2 text-red-400 hover:text-red-300">&times;</button>
                            </div>
                        </template>
                    </div>

                    <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm" x-text="editingPlan ? 'Update Plan' : 'Create Plan'"></button>
                </form>
            </div>
        </div>
    </template>
</div>

<div class="mt-8">
    <h3 class="text-lg font-display font-bold text-white mb-4">Config Options</h3>
    <form method="POST" action="{{ route('admin.products.config-options.update', $product) }}" class="glass rounded-2xl p-6 sm:p-8 space-y-4 max-w-lg">
        @csrf @method('PATCH')
        @forelse ($configOptions as $option)
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="config_options[]" value="{{ $option->id }}"
                    {{ $product->configOptions->contains($option->id) ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-dark-600 bg-dark-800 text-primary-500 focus:ring-primary-500 focus:ring-offset-0">
                <span class="text-sm text-dark-300">{{ $option->name }}</span>
            </label>
        @empty
            <p class="text-sm text-dark-500">No config options available. Create them first.</p>
        @endforelse
        @if ($configOptions->isNotEmpty())
            <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm">Update Config Options</button>
        @endif
    </form>
</div>
@endsection