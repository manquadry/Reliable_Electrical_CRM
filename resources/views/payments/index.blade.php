@extends('layouts.app')

@section('title', 'Collections & Receipts')
@section('header_title', 'Payments & Collections')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Collections & Payment Receipts</h2>
            <p class="text-xs text-slate-500">Record customer deposits, bank transfers, POS card payments, and cash collections.</p>
        </div>

        <a href="{{ route('payments.create') }}" 
           class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/30 transition-all">
            <i class="fa-solid fa-money-bill-wave"></i>
            <span>Record New Payment</span>
        </a>
    </div>

    <!-- Total Collected Banner -->
    <div class="bg-gradient-to-r from-emerald-800 to-teal-900 text-white p-5 rounded-2xl border border-emerald-700/60 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-200">Total Confirmed Collections</span>
            <div class="text-2xl font-black mt-0.5">₦{{ number_format($totalCollected) }}</div>
            <p class="text-[11px] text-emerald-100 mt-0.5">Recorded across all sales territories in Lagos</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center text-xl text-emerald-200">
            <i class="fa-solid fa-vault"></i>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                        <th class="py-3 px-4">Receipt Ref #</th>
                        <th class="py-3 px-4">Customer Store</th>
                        <th class="py-3 px-4">Order Link</th>
                        <th class="py-3 px-4">Collected By Rep</th>
                        <th class="py-3 px-4">Method</th>
                        <th class="py-3 px-4">Payment Date</th>
                        <th class="py-3 px-4 text-right">Amount Collected</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payments as $p)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                {{ $p->payment_reference }}
                            </td>
                            <td class="py-3.5 px-4">
                                <a href="{{ route('customers.show', $p->customer) }}" class="font-bold text-slate-800 hover:text-blue-600">
                                    {{ $p->customer?->company_name }}
                                </a>
                                <div class="text-[10px] text-slate-400">{{ $p->customer?->territory }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-mono">
                                @if($p->order)
                                    <a href="{{ route('orders.show', $p->order) }}" class="text-blue-600 hover:underline">
                                        {{ $p->order->order_number }}
                                    </a>
                                @else
                                    <span class="text-slate-400">Account Deposit</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-medium text-slate-700">{{ $p->user?->name }}</span>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $p->user?->agent_code }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                    {{ $p->getMethodLabelAttribute() }}
                                </span>
                                @if($p->reference_note)
                                    <div class="text-[10px] text-slate-400 font-mono truncate max-w-[120px]">{{ $p->reference_note }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-600 whitespace-nowrap">
                                {{ $p->payment_date->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-emerald-700 text-sm whitespace-nowrap">
                                ₦{{ number_format($p->amount) }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400">
                                No payments recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

