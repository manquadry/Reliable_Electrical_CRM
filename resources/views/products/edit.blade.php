@extends('layouts.app')

@section('title', 'Edit ' . $product->name)
@section('header_title', 'Edit Product')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        
        <div class="p-6 bg-slate-900 text-white">
            <h2 class="text-xl font-extrabold text-white">Edit {{ $product->name }}</h2>
            <p class="text-xs text-slate-300 mt-1">SKU: {{ $product->sku }} &bull; Update pricing, stock levels, or active status.</p>
        </div>

        <form action="{{ route('products.update', $product) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Product Name</label>
                    <input type="text" name="name" required value="{{ old('name', $product->name) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Category</label>
                    <select name="category" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="lighting" {{ $product->category === 'lighting' ? 'selected' : '' }}>Lighting & Fixtures</option>
                        <option value="switchgear_protection" {{ $product->category === 'switchgear_protection' ? 'selected' : '' }}>Switchgear & Protection</option>
                        <option value="cables_wiring" {{ $product->category === 'cables_wiring' ? 'selected' : '' }}>Cables & Wiring</option>
                        <option value="solar_power" {{ $product->category === 'solar_power' ? 'selected' : '' }}>Solar & Inverters</option>
                        <option value="home_appliances" {{ $product->category === 'home_appliances' ? 'selected' : '' }}>Home Appliances & Fans</option>
                        <option value="switches_sockets" {{ $product->category === 'switches_sockets' ? 'selected' : '' }}>Switches & Sockets</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Unit of Measure</label>
                    <select name="unit_measure" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="pcs" {{ $product->unit_measure === 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                        <option value="carton" {{ $product->unit_measure === 'carton' ? 'selected' : '' }}>Carton</option>
                        <option value="roll" {{ $product->unit_measure === 'roll' ? 'selected' : '' }}>Roll</option>
                        <option value="set" {{ $product->unit_measure === 'set' ? 'selected' : '' }}>Set</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Wholesale Price (₦)</label>
                    <input type="number" name="wholesale_price" required min="0" step="50" value="{{ old('wholesale_price', $product->wholesale_price) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm font-bold focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Retail Price (₦)</label>
                    <input type="number" name="unit_price" required min="0" step="50" value="{{ old('unit_price', $product->unit_price) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm font-bold focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Stock Quantity</label>
                    <input type="number" name="stock_quantity" required min="0" value="{{ old('stock_quantity', $product->stock_quantity) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Minimum Order Qty (MOQ)</label>
                    <input type="number" name="minimum_order_quantity" required min="1" value="{{ old('minimum_order_quantity', $product->minimum_order_quantity) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="flex items-center space-x-2 text-xs font-bold text-slate-800">
                        <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span>Product is Active and Available for Field Sales Executives to Book</span>
                    </label>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('products.index') }}" class="text-xs font-bold text-slate-600 hover:underline">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition-all">
                    Update Product
                </button>
            </div>
        </form>

    </div>

</div>
@endsection

