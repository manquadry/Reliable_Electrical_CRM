@extends('layouts.app')

@section('title', 'Sales Orders & Waybills')
@section('header_title', 'Sales Orders & Invoicing')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Commercial Sales Orders</h2>
            <p class="text-xs text-slate-500">Orders taken by field sales executives and direct wholesale purchase bookings.</p>
        </div>

        <a href="{{ route('orders.create') }}" 
           class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-600/30 transition-all">
            <i class="fa-solid fa-cart-plus"></i>
            <span>Book New Sales Order</span>
        </a>
    </div>

    <!-- Search & Status Filter -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-4">
        <form action="{{ route('orders.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by order number or customer name..."
                       class="w-full px-3.5 py-2 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <select name="status" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">All Order Statuses</option>
                    <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="dispatched" {{ request('status') == 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <select name="agent_id" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">All Agents</option>
                    @foreach($agents as $ag)
                        <option value="{{ $ag->id }}" {{ request('agent_id') == $ag->id ? 'selected' : '' }}>{{ $ag->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
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
                        <tr class="hover:bg-slate-50">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('orders.show', $o) }}" class="text-blue-700 hover:underline">
                                    {{ $o->order_number }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <a href="{{ route('customers.show', $o->customer) }}" class="font-bold text-slate-800 hover:text-blue-600">
                                    {{ $o->customer?->company_name }}
                                </a>
                                <div class="text-[10px] text-slate-400">{{ $o->customer?->territory }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-medium text-slate-700">{{ $o->user?->name }}</span>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $o->user?->agent_code }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-600 whitespace-nowrap">
                                {{ $o->order_date->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-semibold text-slate-700">
                                {{ $o->items->count() }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-extrabold text-slate-900 whitespace-nowrap">
                                ₦{{ number_format($o->total_amount) }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold
                                    {{ $o->payment_status === 'fully_paid' ? 'bg-emerald-100 text-emerald-800' : 
                                      ($o->payment_status === 'partially_paid' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst(str_replace('_', ' ', $o->payment_status)) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold
                                    {{ $o->order_status === 'delivered' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 
                                      ($o->order_status === 'dispatched' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 
                                      ($o->order_status === 'approved' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-slate-100 text-slate-700')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $o->order_status)) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <a href="{{ route('orders.show', $o) }}" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px]">
                                    Waybill
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-slate-400">
                                No sales orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

