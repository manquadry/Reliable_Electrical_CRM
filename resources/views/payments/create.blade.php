@extends('layouts.app')

@section('title', 'Record Payment Collection')
@section('header_title', 'Record Payment')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        
        <div class="p-6 bg-gradient-to-r from-emerald-900 to-slate-900 text-white">
            <div class="flex items-center space-x-2 text-emerald-400 text-xs font-bold uppercase tracking-wider mb-1">
                <i class="fa-solid fa-money-bill-transfer"></i>
                <span>Financial Collections</span>
            </div>
            <h2 class="text-xl font-extrabold text-white">Record Customer Payment / Collection</h2>
            <p class="text-xs text-slate-300 mt-1">
                Log funds collected from stores against pending orders or credit balances. Updates order status and automatically records an activity check-in.
            </p>
        </div>

        <form action="{{ route('payments.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Receipt Reference # <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="payment_reference" required value="{{ old('payment_reference', $suggestedReference) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-slate-50 font-mono text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Payment Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="payment_date" required value="{{ old('payment_date', Carbon\Carbon::today()->toDateString()) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Customer Store <span class="text-red-500">*</span>
                    </label>
                    <select name="customer_id" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">-- Select Customer --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ (old('customer_id') == $c->id || (isset($selectedCustomer) && $selectedCustomer->id == $c->id)) ? 'selected' : '' }}>
                                {{ $c->company_name }} ({{ $c->contact_person }} - {{ $c->territory }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Link to Pending Sales Order (Optional)
                    </label>
                    <select name="order_id" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">-- General Account Balance Deposit (No Specific Order) --</option>
                        @foreach($orders as $ord)
                            <option value="{{ $ord->id }}" {{ (old('order_id') == $ord->id || (isset($selectedOrder) && $selectedOrder->id == $ord->id)) ? 'selected' : '' }}>
                                {{ $ord->order_number }} - {{ $ord->customer?->company_name }} (Total: ₦{{ number_format($ord->total_amount) }} • Due: ₦{{ number_format($ord->balance_due) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Amount Collected (₦) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="amount" required min="100" step="500" value="{{ old('amount', isset($selectedOrder) ? $selectedOrder->balance_due : '') }}" placeholder="e.g. 500000"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm font-bold focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Payment Method <span class="text-red-500">*</span>
                    </label>
                    <select name="payment_method" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="bank_transfer">Direct Bank Transfer</option>
                        <option value="pos_card">POS / Card Terminal</option>
                        <option value="cash">Cash Received in Field</option>
                        <option value="cheque">Bank Cheque</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Bank Reference / POS Receipt # / Transaction Hash
                    </label>
                    <input type="text" name="reference_note" value="{{ old('reference_note') }}" placeholder="e.g. GTB-TRF-9021948 or Moniepoint POS #4881"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Remarks / Notes
                    </label>
                    <textarea name="notes" rows="2" placeholder="e.g. Cleared 50% deposit, balance upon final waybill delivery..."
                              class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('payments.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition-all flex items-center space-x-2">
                    <i class="fa-solid fa-receipt"></i>
                    <span>Confirm & Record Collection</span>
                </button>
            </div>

        </form>

    </div>

</div>
@endsection

