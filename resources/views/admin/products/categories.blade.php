@extends('layouts.admin')
@section('title', 'Product Categories')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-display font-bold text-white">Product Categories</h2>
    <button @click="showForm = true; editing = null" class="inline-flex items-center gap-2 px-4 py-2.5 btn-primary text-white text-sm font-medium rounded-xl">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Category
    </button>
</div>

<div x-data="{ showForm: false, editing: null }">
    <div class="glass rounded-2xl overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Name</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Slug</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Products</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Status</th>
                        <th class="text-right px-6 py-3 text-xs font-medium text-dark-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr class="border-b border-white/5 hover:bg-white/[0.02] transition">
                            <td class="px-6 py-4 font-medium text-white text-sm">{{ $category->name }}</td>
                            <td class="px-6 py-4 text-sm text-dark-400">{{ $category->slug }}</td>
                            <td class="px-6 py-4 text-sm text-dark-400">{{ $category->products_count }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium {{ $category->enabled ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-dark-500/10 text-dark-400 border border-dark-500/20' }}">
                                    {{ $category->enabled ? 'Enabled' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="showForm = true; editing = {{ $category->id }}" class="text-primary-400 hover:text-primary-300 text-sm transition">Edit</button>
                                    <form method="POST" action="{{ route('admin.products.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Delete this category?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 text-sm transition">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto text-dark-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    <p class="text-dark-500">No categories found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <template x-if="showForm">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" @click.self="showForm = false; editing = null">
            <div class="glass rounded-2xl p-6 sm:p-8 w-full max-w-lg m-4">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-display font-bold text-white" x-text="editing ? 'Edit Category' : 'Create Category'"></h3>
                    <button @click="showForm = false; editing = null" class="text-dark-400 hover:text-white">&times;</button>
                </div>

                <form method="POST" :action="editing ? '{{ route('admin.products.categories.update', 'PLACEHOLDER') }}'.replace('PLACEHOLDER', editing) : '{{ route('admin.products.categories.store') }}'" class="space-y-4">
                    @csrf
                    <template x-if="editing"><input type="hidden" name="_method" value="PATCH"></template>
                    <div>
                        <label class="block text-sm font-medium text-dark-300 mb-2">Name</label>
                        <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark-300 mb-2">Slug (leave blank to auto-generate)</label>
                        <input type="text" name="slug" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark-300 mb-2">Description</label>
                        <textarea name="description" rows="2" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm"></textarea>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-dark-300 mb-2">Order</label>
                            <input type="number" name="order" value="0" class="w-full px-4 py-3 rounded-xl input-field text-white text-sm">
                        </div>
                        <div class="flex items-center pt-7">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="enabled" value="1" checked class="w-4 h-4 rounded border-dark-600 bg-dark-800 text-primary-500 focus:ring-primary-500 focus:ring-offset-0">
                                <span class="text-sm text-dark-300">Enabled</span>
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="w-full py-3 px-4 btn-primary text-white font-medium rounded-xl text-sm" x-text="editing ? 'Update Category' : 'Create Category'"></button>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection