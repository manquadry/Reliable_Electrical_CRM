@extends('layouts.app')

@section('title', 'Electrical Appliances & Inventory')
@section('header_title', 'Products & Inventory Catalog')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Electrical Appliances & Wholesale Catalog</h2>
            <p class="text-xs text-slate-500">Commercial floodlights, modern chandeliers, changeovers, distribution boards, pure copper cables and appliances.</p>
        </div>

        @if(Auth::user()->isAdmin())
            <a href="{{ route('products.create') }}" 
               class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-600/30 transition-all">
                <i class="fa-solid fa-plus"></i>
                <span>Add New Product</span>
            </a>
        @endif
    </div>

    <!-- Search & Category Filters -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-4">
        <form action="{{ route('products.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by product name, SKU or description..."
                       class="w-full px-3.5 py-2 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <select name="category" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $cat)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-colors">
                    Filter Catalog
                </button>
            </div>
        </form>
    </div>

    <!-- Product Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($products as $prod)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs hover:border-blue-400 hover:shadow-md transition-all p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between pb-3 border-b border-slate-100">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700">
                            {{ $prod->getCategoryLabelAttribute() }}
                        </span>
                        <span class="font-mono text-[11px] text-slate-400">{{ $prod->sku }}</span>
                    </div>

                    <h3 class="font-extrabold text-slate-900 text-sm mt-3 leading-snug">
                        {{ $prod->name }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $prod->description }}</p>

                    <!-- Price Matrix -->
                    <div class="grid grid-cols-2 gap-2 mt-4 p-3 bg-slate-50 rounded-xl text-xs">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Wholesale Price</span>
                            <span class="font-extrabold text-emerald-700 text-sm">₦{{ number_format($prod->wholesale_price) }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Retail Price</span>
                            <span class="font-bold text-slate-700 text-sm">₦{{ number_format($prod->unit_price) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Stock & Actions -->
                <div class="pt-4 mt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <div class="flex items-center space-x-1.5">
                        <span class="w-2 h-2 rounded-full {{ $prod->stock_quantity > 20 ? 'bg-emerald-500' : ($prod->stock_quantity > 0 ? 'bg-amber-500' : 'bg-red-500') }}"></span>
                        <span class="font-bold text-slate-800">{{ $prod->stock_quantity }} {{ $prod->unit_measure }}</span>
                        <span class="text-slate-400 text-[11px]">in stock</span>
                    </div>

                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('products.edit', $prod) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-xs flex items-center space-x-1">
                            <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                            <span>Edit</span>
                        </a>
                    @else
                        <span class="text-[11px] text-slate-400 font-mono">{{ $prod->sku }}</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white rounded-2xl p-8 text-center text-slate-400">
                No electrical products found in catalog.
            </div>
        @endforelse
    </div>

    @if($products->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-200">
            {{ $products->links() }}
        </div>
    @endif

</div>
@endsection

