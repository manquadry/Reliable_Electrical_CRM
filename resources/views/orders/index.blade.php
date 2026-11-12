@extends('layouts.app')

@section('title', 'Sales Orders & Waybills')
@section('header_title', 'Sales Orders & Invoicing')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-brand-600 mb-1">
                <i class="fa-solid fa-cart-shopping"></i>
                <span>Commercial Distribution</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Sales Orders & Waybills</h1>
            <p class="text-xs text-slate-500 mt-0.5">Commercial wholesale and retail orders booked by field executives across Lagos.</p>
        </div>

        <a href="{{ route('orders.create') }}" 
           class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold shadow-md shadow-brand-600/30 hover:shadow-brand-600/50 transition-all shrink-0">
            <i class="fa-solid fa-cart-plus"></i>
            <span>Book New Sales Order</span>
        </a>
    </div>

    <!-- Search & Status Filter -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-card p-4 sm:p-5">
        <form action="{{ route('orders.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div class="sm:col-span-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by order number or customer name..."
                           class="w-full pl-9 pr-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-brand-500 focus:outline-none transition-all">
                </div>
            </div>

            <div>
                <select name="status" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-brand-500 focus:outline-none transition-all">
                    <option value="">All Order Statuses</option>
                    <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="dispatched" {{ request('status') == 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <select name="agent_id" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-brand-500 focus:outline-none transition-all">
                    <option value="">All Agents</option>
                    @foreach($agents as $ag)
                        <option value="{{ $ag->id }}" {{ request('agent_id') == $ag->id ? 'selected' : '' }}>{{ $ag->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all shadow-2xs">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'agent_id']))
                    <a href="{{ route('orders.index') }}" class="p-2.5 border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl text-xs font-semibold" title="Reset">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-100/70 text-slate-500 uppercase font-bold tracking-wider text-[11px] border-b border-slate-200">
                        <th class="py-3 px-4">Order #</th>
                        <th class="py-3 px-4">Customer Store</th>
                        <th class="py-3 px-4">Field Rep</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4 text-center">Items</th>
                        <th class="py-3 px-4 text-right">Total Amount</th>
                        <th class="py-3 px-4 text-center">Payment</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $o)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900 whitespace-nowrap">
                                <a href="{{ route('orders.show', $o) }}" class="text-brand-700 hover:underline">
                                    {{ $o->order_number }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <a href="{{ route('customers.show', $o->customer) }}" class="font-bold text-slate-800 hover:text-brand-600 transition-colors">
                                    {{ $o->customer?->company_name }}
                                </a>
                                <div class="text-[10px] text-slate-400 mt-0.5 flex items-center space-x-1">
                                    <i class="fa-solid fa-map-pin text-rose-500 text-[9px]"></i>
                                    <span>{{ $o->customer?->territory }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-bold text-slate-800">{{ $o->user?->name }}</span>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $o->user?->agent_code }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-600 whitespace-nowrap">
                                {{ $o->order_date->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold font-mono text-slate-700">
                                {{ $o->items->count() }}
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <span class="font-mono font-extrabold text-slate-900 text-xs">₦{{ number_format($o->total_amount) }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold
                                    {{ $o->payment_status === 'fully_paid' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 
                                      ($o->payment_status === 'partially_paid' ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-rose-50 text-rose-800 border border-rose-200') }}">
                                    {{ ucfirst(str_replace('_', ' ', $o->payment_status)) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold
                                    {{ $o->order_status === 'delivered' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 
                                      ($o->order_status === 'dispatched' ? 'bg-brand-50 text-brand-700 border border-brand-200' : 
                                      ($o->order_status === 'approved' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-slate-100 text-slate-700 border border-slate-200')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $o->order_status)) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <a href="{{ route('orders.show', $o) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition-colors">
                                    Waybill
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-slate-400 font-medium">
                                No sales orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
