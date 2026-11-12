@extends('layouts.app')

@section('title', 'Order ' . $order->order_number)
@section('header_title', 'Order & Delivery Waybill')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Action Toolbar (Hidden when printing) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 no-print">
        <a href="{{ route('orders.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Orders</span>
        </a>

        <div class="flex items-center space-x-2">
            @if(Auth::user()->isAdmin())
                <!-- Admin Only Status Update Form -->
                <form action="{{ route('orders.update_status', $order) }}" method="POST" class="flex items-center space-x-1.5">
                    @csrf
                    @method('PATCH')
                    <span class="text-[11px] font-semibold text-slate-500 mr-1">Status:</span>
                    <select name="order_status" onchange="this.form.submit()" class="text-xs font-bold px-3 py-1.5 border border-slate-300 rounded-lg bg-white text-slate-800 shadow-2xs">
                        <option value="draft" {{ $order->order_status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending_approval" {{ $order->order_status === 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                        <option value="approved" {{ $order->order_status === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="dispatched" {{ $order->order_status === 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                        <option value="delivered" {{ $order->order_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </form>
            @else
                <!-- Non-Admin Read-Only Status Display -->
                <div class="flex items-center space-x-1.5">
                    <span class="text-[11px] font-semibold text-slate-500">Status:</span>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $order->order_status === 'delivered' ? 'bg-emerald-100 text-emerald-800' : ($order->order_status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                        {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                    </span>
                </div>
            @endif

            @if($order->payment_status !== 'fully_paid')
                <a href="{{ route('payments.create', ['order_id' => $order->id, 'customer_id' => $order->customer_id]) }}" 
                   class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm flex items-center space-x-1.5">
                    <i class="fa-solid fa-money-bill-wave"></i>
                    <span>Record Payment</span>
                </a>
            @endif

            <button type="button" onclick="window.print()" class="px-3.5 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 font-semibold text-xs shadow-2xs flex items-center space-x-1.5">
                <i class="fa-solid fa-print"></i>
                <span>Print Waybill</span>
            </button>
        </div>
    </div>

    <!-- Official Printable Invoice & Waybill Sheet -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-10 text-slate-800">
        
        <!-- Waybill Header -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between pb-6 border-b border-slate-200 gap-4">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg font-bold">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">Reliable Electrics Nigeria Ltd</h1>
                        <p class="text-xs text-blue-600 font-bold uppercase tracking-wider">Commercial Appliances & Lighting Distribution</p>
                    </div>
                </div>
                <div class="text-xs text-slate-500 space-y-0.5">
                    <p>Phase 1, 32B Oguntona Crescent, Gbagada, Lagos 333010</p>
                    <p>Phone: +234 803 555 0192 &bull; Email: orders@reliableelectrics.ng</p>
                </div>
            </div>

            <div class="sm:text-right">
                <div class="inline-block px-3 py-1 rounded-lg bg-slate-100 text-slate-800 font-mono font-bold text-sm">
                    {{ $order->order_number }}
                </div>
                <div class="text-xs text-slate-500 mt-2 space-y-0.5">
                    <p>Order Date: <strong class="text-slate-800">{{ $order->order_date->format('M d, Y') }}</strong></p>
                    <p>Status: <span class="font-bold text-blue-600 uppercase">{{ ucfirst(str_replace('_', ' ', $order->order_status)) }}</span></p>
                    <p>Payment: <span class="font-bold {{ $order->payment_status === 'fully_paid' ? 'text-emerald-600' : 'text-amber-600' }}">{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</span></p>
                </div>
            </div>
        </div>

        <!-- Bill To & Ship To -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-6 border-b border-slate-200 text-xs">
            <div>
                <div class="flex items-center space-x-2 mb-2">
                    <h3 class="font-bold uppercase tracking-wider text-slate-400">Customer (Bill To):</h3>
                    @if($order->customer?->isWalkIn())
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-cyan-100 text-cyan-800 border border-cyan-200 uppercase">
                            ⚡ Walk-in Counter Sale
                        </span>
                    @endif
                </div>
                <div class="font-extrabold text-slate-900 text-sm">{{ $order->customer?->company_name }}</div>
                <div class="text-slate-600 mt-1 space-y-0.5">
                    @if($order->notes && str_contains($order->notes, 'Walk-in Counter Buyer:'))
                        <p class="font-bold text-slate-900 bg-cyan-50/80 p-1.5 rounded border border-cyan-200 text-[11px]">
                            <i class="fa-solid fa-user-tag text-cyan-700 mr-1"></i>
                            {{ explode(' | ', $order->notes)[0] }}
                        </p>
                    @else
                        <p>Contact: {{ $order->customer?->contact_person }}</p>
                        <p>Phone: {{ $order->customer?->phone }}</p>
                    @endif
                    <p>{{ $order->customer?->address }}, {{ $order->customer?->city }}</p>
                    <p>Territory: {{ $order->customer?->territory }}</p>
                </div>
            </div>

            <div class="sm:text-right">
                <h3 class="font-bold uppercase tracking-wider text-slate-400 mb-2">Delivery Details & Representative:</h3>
                <p class="font-bold text-slate-800">Field Executive: {{ $order->user?->name }} ({{ $order->user?->agent_code }})</p>
                <div class="text-slate-600 mt-1 space-y-0.5">
                    <p>Delivery To: {{ $order->delivery_address ?: $order->customer?->address }}</p>
                    <p>Payment Terms: {{ str_replace('_', ' ', $order->customer?->payment_terms) }}</p>
                </div>
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="py-6">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 uppercase font-bold border-b border-slate-200">
                        <th class="py-2.5 px-3">#</th>
                        <th class="py-2.5 px-3">SKU</th>
                        <th class="py-2.5 px-3">Electrical Item Description</th>
                        <th class="py-2.5 px-3 text-center">Qty</th>
                        <th class="py-2.5 px-3 text-right">Unit Price</th>
                        <th class="py-2.5 px-3 text-right">Line Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($order->items as $idx => $item)
                        <tr>
                            <td class="py-3 px-3 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                            <td class="py-3 px-3 font-mono text-slate-600">{{ $item->product?->sku }}</td>
                            <td class="py-3 px-3">
                                <div class="font-bold text-slate-900">{{ $item->product?->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $item->product?->getCategoryLabelAttribute() }}</div>
                            </td>
                            <td class="py-3 px-3 text-center font-bold text-slate-900">{{ $item->quantity }}</td>
                            <td class="py-3 px-3 text-right font-medium text-slate-700">₦{{ number_format($item->unit_price) }}</td>
                            <td class="py-3 px-3 text-right font-extrabold text-slate-900">₦{{ number_format($item->total_price) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals Strip -->
        <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row justify-between gap-4 text-xs">
            <div class="max-w-xs text-slate-500">
                @if($order->notes)
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="font-bold uppercase text-[10px] text-slate-400 block mb-1">Dispatch Notes:</span>
                        <p class="italic text-slate-700">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>

            <div class="sm:w-64 space-y-1.5 text-right">
                <div class="flex justify-between text-slate-500">
                    <span>Subtotal:</span>
                    <span class="font-bold text-slate-800">₦{{ number_format($order->subtotal) }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="flex justify-between text-emerald-600 font-semibold">
                        <span>Discount:</span>
                        <span>-₦{{ number_format($order->discount_amount) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-base font-black text-slate-900 pt-2 border-t border-slate-200">
                    <span>Total Amount:</span>
                    <span class="text-blue-700">₦{{ number_format($order->total_amount) }}</span>
                </div>
                <div class="flex justify-between text-xs text-slate-500 pt-1">
                    <span>Amount Paid:</span>
                    <span class="font-bold text-emerald-600">₦{{ number_format($order->paid_amount) }}</span>
                </div>
                <div class="flex justify-between text-xs font-bold text-amber-700">
                    <span>Balance Due:</span>
                    <span>₦{{ number_format($order->balance_due) }}</span>
                </div>
            </div>
        </div>

        <!-- Waybill Signatures Footer -->
        <div class="mt-12 pt-8 border-t border-slate-200 grid grid-cols-3 gap-6 text-center text-xs text-slate-500">
            <div>
                <div class="border-b border-slate-300 pb-8 mb-2"></div>
                <p class="font-semibold text-slate-700">Prepared By (Sales Rep)</p>
                <p class="text-[10px] text-slate-400">{{ $order->user?->name }}</p>
            </div>
            <div>
                <div class="border-b border-slate-300 pb-8 mb-2"></div>
                <p class="font-semibold text-slate-700">Warehouse Dispatcher</p>
                <p class="text-[10px] text-slate-400">Gbagada Central Store</p>
            </div>
            <div>
                <div class="border-b border-slate-300 pb-8 mb-2"></div>
                <p class="font-semibold text-slate-700">Customer Received By</p>
                <p class="text-[10px] text-slate-400">Sign & Stamp</p>
            </div>
        </div>

    </div>

</div>
@endsection

