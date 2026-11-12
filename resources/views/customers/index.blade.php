@extends('layouts.app')

@section('title', 'Customers Directory')
@section('header_title', 'Customers & Electrical Retailers')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Top Action Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Onboarded Customers & Stores</h2>
            <p class="text-xs text-slate-500">Directory of retail shops, electrical contractors, lighting showrooms, and wholesale distributors.</p>
        </div>

        <a href="{{ route('customers.create') }}" 
           class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-600/30 transition-all">
            <i class="fa-solid fa-plus"></i>
            <span>Onboard New Customer</span>
        </a>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-4">
        <form action="{{ route('customers.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <div class="lg:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search company, contact person, phone or address..."
                       class="w-full px-3.5 py-2 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <select name="category" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">All Categories</option>
                    <option value="retailer" {{ request('category') == 'retailer' ? 'selected' : '' }}>Retail Shop</option>
                    <option value="wholesaler" {{ request('category') == 'wholesaler' ? 'selected' : '' }}>Wholesale Distributor</option>
                    <option value="electrical_contractor" {{ request('category') == 'electrical_contractor' ? 'selected' : '' }}>Electrical Contractor</option>
                    <option value="lighting_showroom" {{ request('category') == 'lighting_showroom' ? 'selected' : '' }}>Lighting Showroom</option>
                    <option value="real_estate_developer" {{ request('category') == 'real_estate_developer' ? 'selected' : '' }}>Estate Developer</option>
                </select>
            </div>

            <div>
                <select name="agent_id" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Onboarded By (Any)</option>
                    @foreach($agents as $ag)
                        <option value="{{ $ag->id }}" {{ request('agent_id') == $ag->id ? 'selected' : '' }}>{{ $ag->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <input type="date" name="onboarded_date" value="{{ request('onboarded_date') }}" title="Filter by Onboarded Day"
                       class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <button type="submit" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                        <th class="py-3 px-4">Company / Store Name</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Contact Person</th>
                        <th class="py-3 px-4">Address / Territory</th>
                        <th class="py-3 px-4">Onboarded By Rep</th>
                        <th class="py-3 px-4 text-center">Day Onboarded</th>
                        <th class="py-3 px-4 text-right">Credit Limit</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customers as $c)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                <a href="{{ route('customers.show', $c) }}" class="text-blue-700 hover:underline text-sm font-extrabold block">
                                    {{ $c->company_name }}
                                </a>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $c->customer_code }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-50 text-purple-700 border border-purple-200 whitespace-nowrap">
                                    {{ $c->getCategoryLabelAttribute() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800">{{ $c->contact_person }}</div>
                                <div class="text-[11px] text-slate-500">
                                    <a href="tel:{{ $c->phone }}" class="text-blue-600 hover:underline">{{ $c->phone }}</a>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <div class="truncate text-slate-700 font-medium">{{ $c->address }}</div>
                                <div class="text-[10px] text-slate-400">{{ $c->territory }} &bull; {{ $c->city }}</div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($c->onboardedBy)
                                    <a href="{{ route('agents.show', $c->onboardedBy) }}" class="font-bold text-slate-800 hover:text-blue-600">
                                        {{ $c->onboardedBy->name }}
                                    </a>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $c->onboardedBy->agent_code }}</div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-800 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded bg-slate-100 font-mono text-[11px]">
                                    {{ $c->onboarded_date->format('M d, Y') }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-extrabold text-slate-900 whitespace-nowrap">
                                ₦{{ number_format($c->credit_limit) }}
                                <div class="text-[10px] text-slate-400 uppercase">{{ str_replace('_', ' ', $c->payment_terms) }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <a href="{{ route('activities.create_checkin', ['customer_id' => $c->id]) }}" 
                                       title="Log Check-in" class="p-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </a>
                                    <a href="{{ route('orders.create', ['customer_id' => $c->id]) }}" 
                                       title="Take Order" class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </a>
                                    <a href="{{ route('customers.show', $c) }}" 
                                       class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px]">
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400">
                                No customers found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

