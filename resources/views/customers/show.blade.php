@extends('layouts.app')

@section('title', $customer->company_name . ' - Customer Details')
@section('header_title', 'Customer Store Profile')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Top Store Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-2xl p-6 shadow-xl border border-blue-900/40">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            
            <div class="flex items-start space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 text-white flex items-center justify-center text-2xl font-extrabold shadow-lg shadow-purple-500/30 shrink-0">
                    <i class="fa-solid fa-shop"></i>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs bg-purple-500/20 text-purple-300 px-2 py-0.5 rounded-full font-semibold border border-purple-500/30">
                            {{ $customer->getCategoryLabelAttribute() }}
                        </span>
                        <span class="text-xs text-blue-300 font-mono">{{ $customer->customer_code }}</span>
                        <span class="text-xs bg-emerald-500/20 text-emerald-300 px-2 py-0.5 rounded-full">
                            {{ ucfirst($customer->status) }}
                        </span>
                    </div>

                    <h2 class="text-2xl font-extrabold text-white mt-1">{{ $customer->company_name }}</h2>
                    
                    <div class="text-xs text-slate-300 mt-1 flex flex-wrap items-center gap-3">
                        <span><i class="fa-solid fa-user text-blue-400 mr-1"></i>Contact: <strong class="text-white">{{ $customer->contact_person }}</strong></span>
                        <span>&bull;</span>
                        <a href="tel:{{ $customer->phone }}" class="text-blue-300 hover:underline"><i class="fa-solid fa-phone mr-1"></i>{{ $customer->phone }}</a>
                        <span>&bull;</span>
                        <span><i class="fa-solid fa-map-pin text-purple-400 mr-1"></i>{{ $customer->address }}, {{ $customer->city }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('activities.create_checkin', ['customer_id' => $customer->id]) }}" 
                   class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold text-xs shadow-sm transition-all flex items-center space-x-1.5">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>Log Check-In</span>
                </a>
                <a href="{{ route('orders.create', ['customer_id' => $customer->id]) }}" 
                   class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition-all flex items-center space-x-1.5">
                    <i class="fa-solid fa-cart-plus"></i>
                    <span>Take Order</span>
                </a>
                <a href="{{ route('payments.create', ['customer_id' => $customer->id]) }}" 
                   class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition-all flex items-center space-x-1.5">
                    <i class="fa-solid fa-money-bill-wave"></i>
                    <span>Collect Payment</span>
                </a>
                <a href="{{ route('customers.edit', $customer) }}" 
                   class="p-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs border border-white/20" title="Edit">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
            </div>

        </div>
    </div>

    <!-- The User's Requirement: "and also the customer onboard by the field agent as well with their details and day they are onboarded" -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200/80 rounded-2xl p-5 shadow-xs">
        <div class="flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-blue-900 mb-3">
            <i class="fa-solid fa-id-card-clip text-blue-600"></i>
            <span>Customer Onboarding Metadata & Acquisition Audit</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
            
            <div class="bg-white p-3.5 rounded-xl border border-blue-100">
                <span class="text-[10px] font-bold uppercase text-slate-400 block">Day Onboarded</span>
                <span class="text-sm font-extrabold text-blue-900 mt-0.5 block">
                    {{ $customer->onboarded_date->format('l, F j, Y') }}
                </span>
                <span class="text-[11px] text-slate-500 font-mono">
                    {{ $customer->onboarded_date->diffForHumans() }}
                </span>
            </div>

            <div class="bg-white p-3.5 rounded-xl border border-blue-100">
                <span class="text-[10px] font-bold uppercase text-slate-400 block">Onboarded By Field Executive</span>
                @if($customer->onboardedBy)
                    <a href="{{ route('agents.show', $customer->onboardedBy) }}" class="text-sm font-extrabold text-purple-700 hover:underline mt-0.5 block">
                        {{ $customer->onboardedBy->name }}
                    </a>
                    <span class="text-[11px] text-slate-500 font-mono">
                        Code: {{ $customer->onboardedBy->agent_code }} &bull; {{ $customer->onboardedBy->territory }}
                    </span>
                @else
                    <span class="text-sm font-bold text-slate-700 mt-0.5 block">Headquarters / Admin</span>
                @endif
            </div>

            <div class="bg-white p-3.5 rounded-xl border border-blue-100">
                <span class="text-[10px] font-bold uppercase text-slate-400 block">Credit Limit & Terms</span>
                <span class="text-sm font-extrabold text-slate-900 mt-0.5 block">
                    ₦{{ number_format($customer->credit_limit) }}
                </span>
                <span class="text-[11px] text-slate-500 uppercase font-semibold">
                    {{ str_replace('_', ' ', $customer->payment_terms) }}
                </span>
            </div>

            <div class="bg-white p-3.5 rounded-xl border border-blue-100">
                <span class="text-[10px] font-bold uppercase text-slate-400 block">GPS Coordinates</span>
                @if($customer->latitude && $customer->longitude)
                    <a href="https://maps.google.com/?q={{ $customer->latitude }},{{ $customer->longitude }}" target="_blank" 
                       class="text-sm font-bold text-blue-600 hover:underline mt-0.5 block">
                        {{ $customer->latitude }}, {{ $customer->longitude }} <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    </a>
                    <span class="text-[11px] text-emerald-600 font-medium">GPS Verified</span>
                @else
                    <span class="text-sm text-slate-400 mt-0.5 block">Not recorded</span>
                @endif
            </div>

        </div>
    </div>

    <!-- 3 Financial Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Total Orders Value</span>
            <div class="text-2xl font-extrabold text-slate-900 mt-1">
                ₦{{ number_format($customer->total_orders) }}
            </div>
            <p class="text-[11px] text-slate-500 mt-1">{{ $orders->count() }} orders booked</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 block">Total Payments Received</span>
            <div class="text-2xl font-extrabold text-emerald-700 mt-1">
                ₦{{ number_format($customer->total_paid) }}
            </div>
            <p class="text-[11px] text-emerald-600/80 mt-1">{{ $payments->count() }} payments verified</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-amber-600 block">Outstanding Balance Due</span>
            <div class="text-2xl font-extrabold text-amber-700 mt-1">
                ₦{{ number_format($customer->outstanding_balance) }}
            </div>
            <p class="text-[11px] text-slate-500 mt-1">
                {{ $customer->outstanding_balance > 0 ? 'Pending collection by sales agent' : 'Account in good standing' }}
            </p>
        </div>

    </div>

    <!-- Two Columns: Field Activity Logs + Recent Orders -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Field Activities at this store -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-sm font-bold text-slate-900 flex items-center space-x-2">
                    <i class="fa-solid fa-location-dot text-blue-600"></i>
                    <span>Field Visits & Check-In History</span>
                </h3>
                <a href="{{ route('activities.create_checkin', ['customer_id' => $customer->id]) }}" class="text-xs font-semibold text-blue-600 hover:underline">
                    + Log Visit
                </a>
            </div>

            <div class="space-y-3">
                @forelse($activities as $act)
                    <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/70 text-xs">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="font-bold text-slate-900">{{ $act->user?->name }}</span>
                                <span class="text-slate-400">&bull;</span>
                                <span class="font-medium text-slate-600">{{ $act->getActivityTypeLabelAttribute() }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded font-mono text-[10px] bg-slate-200 text-slate-800">
                                {{ $act->activity_date->format('M d, Y') }} • {{ substr($act->check_in_time, 0, 5) }}
                            </span>
                        </div>
                        <p class="text-slate-700 mt-1 font-medium">{{ $act->purpose }}</p>
                        @if($act->notes)
                            <p class="text-slate-500 italic mt-1">{{ $act->notes }}</p>
                        @endif
                        <div class="mt-2 pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px]">
                            <span class="font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                {{ $act->getOutcomeLabelAttribute() }}
                            </span>
                            @if($act->next_followup_date)
                                <span class="text-slate-400">Next: {{ $act->next_followup_date->format('M d') }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center py-6 text-slate-400 text-xs">No field check-ins recorded for this store yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Orders & Commercial Invoices -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-sm font-bold text-slate-900 flex items-center space-x-2">
                    <i class="fa-solid fa-file-invoice text-emerald-600"></i>
                    <span>Sales Orders History</span>
                </h3>
                <a href="{{ route('orders.create', ['customer_id' => $customer->id]) }}" class="text-xs font-semibold text-blue-600 hover:underline">
                    + Book Order
                </a>
            </div>

            <div class="space-y-3">
                @forelse($orders as $order)
                    <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/70 text-xs flex items-center justify-between">
                        <div>
                            <a href="{{ route('orders.show', $order) }}" class="font-bold text-slate-900 hover:text-blue-600 text-sm">
                                {{ $order->order_number }}
                            </a>
                            <div class="text-slate-500 mt-0.5">
                                Date: {{ $order->order_date->format('M d, Y') }} &bull; By: {{ $order->user?->name }}
                            </div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                {{ $order->items->count() }} items &bull; Status: <span class="font-semibold text-slate-700">{{ ucfirst(str_replace('_', ' ', $order->order_status)) }}</span>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="font-extrabold text-sm text-slate-900">₦{{ number_format($order->total_amount) }}</div>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-semibold
                                {{ $order->payment_status === 'fully_paid' ? 'bg-emerald-100 text-emerald-800' : 
                                  ($order->payment_status === 'partially_paid' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-center py-6 text-slate-400 text-xs">No orders placed by this customer yet.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection

