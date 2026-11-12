@extends('layouts.app')

@section('title', 'Add Electrical Product')
@section('header_title', 'New Catalog Product')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        
        <div class="p-6 bg-slate-900 text-white">
            <h2 class="text-xl font-extrabold text-white">Add Electrical Product to Catalog</h2>
            <p class="text-xs text-slate-300 mt-1">Register lighting, switchgears, cables, solar, or home appliances with wholesale and retail price points.</p>
        </div>

        <form action="{{ route('products.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Product Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="e.g. Reliable 200W Commercial LED High-Bay Light"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Product SKU Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="sku" required value="{{ old('sku') }}" placeholder="e.g. REL-HB-200W"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white font-mono text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="lighting">Lighting & Fixtures</option>
                        <option value="switchgear_protection">Switchgear & Protection</option>
                        <option value="cables_wiring">Cables & Wiring</option>
                        <option value="solar_power">Solar & Inverters</option>
                        <option value="home_appliances">Home Appliances & Fans</option>
                        <option value="switches_sockets">Switches & Sockets</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Wholesale / B2B Price (₦) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="wholesale_price" required min="0" step="50" value="{{ old('wholesale_price') }}" placeholder="e.g. 25000"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm font-bold focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Retail Price (₦) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="unit_price" required min="0" step="50" value="{{ old('unit_price') }}" placeholder="e.g. 32000"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm font-bold focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Stock Quantity Available <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="stock_quantity" required min="0" value="{{ old('stock_quantity', 100) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Unit of Measure <span class="text-red-500">*</span>
                    </label>
                    <select name="unit_measure" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="pcs">Pieces (pcs)</option>
                        <option value="carton">Carton</option>
                        <option value="roll">Roll (100m)</option>
                        <option value="set">Set</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Minimum Order Quantity (MOQ) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="minimum_order_quantity" required min="1" value="{{ old('minimum_order_quantity', 1) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Technical Description & Specifications
                    </label>
                    <textarea name="description" rows="3" placeholder="Wattage, lumens, IP waterproof rating, voltage certification, copper purity..."
                              class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('products.index') }}" class="text-xs font-bold text-slate-600 hover:underline">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition-all">
                    Add Product to Catalog
                </button>
            </div>
        </form>

    </div>

</div>
@endsection

