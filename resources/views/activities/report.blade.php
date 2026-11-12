@extends('layouts.app')

@section('title', 'Field Activity & Performance Report')
@section('header_title', 'Activity Reports & Audit')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Filter Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 no-print">
        <form action="{{ route('activities.report') }}" method="GET" class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 gap-2">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 flex items-center space-x-2">
                        <i class="fa-solid fa-filter text-blue-600"></i>
                        <span>Filter Activity & Performance Records</span>
                    </h3>
                    <p class="text-xs text-slate-500">Filter across dates, field reps, customer visit types and outcomes.</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="submit" name="export" value="csv" 
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg border border-emerald-600 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-bold transition-colors">
                        <i class="fa-solid fa-file-csv text-sm"></i>
                        <span>Export CSV</span>
                    </button>
                    <button type="button" onclick="window.print()" 
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-slate-700 hover:bg-slate-100 text-xs font-semibold transition-colors">
                        <i class="fa-solid fa-print"></i>
                        <span>Print</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                           class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-600 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                           class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Field Executive</label>
                    <select name="agent_id" class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">All Field Agents</option>
                        @foreach($allAgents as $ag)
                            <option value="{{ $ag->id }}" {{ $agentId == $ag->id ? 'selected' : '' }}>
                                {{ $ag->name }} ({{ $ag->agent_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Activity Type</label>
                    <select name="activity_type" class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">All Types</option>
                        <option value="store_visit" {{ $activityType == 'store_visit' ? 'selected' : '' }}>Store Visit</option>
                        <option value="new_customer_onboarding" {{ $activityType == 'new_customer_onboarding' ? 'selected' : '' }}>New Customer Onboarding</option>
                        <option value="order_booking" {{ $activityType == 'order_booking' ? 'selected' : '' }}>Order Booking</option>
                        <option value="payment_collection" {{ $activityType == 'payment_collection' ? 'selected' : '' }}>Payment Collection</option>
                        <option value="product_demo" {{ $activityType == 'product_demo' ? 'selected' : '' }}>Product Demo</option>
                    </select>
                </div>

                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 py-2 px-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-xs transition-colors">
                        Apply Filters
                    </button>
                    <a href="{{ route('activities.report') }}" class="p-2 border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl text-xs font-semibold" title="Reset">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Print Report Header -->
    <div class="hidden print-only mb-6">
        <h2 class="text-xl font-bold">Reliable Electrics Nigeria Limited - Field Operations Report</h2>
        <p class="text-xs text-gray-600">Period: {{ $startDate }} to {{ $endDate }}</p>
    </div>

    <!-- Period KPI Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Activities</span>
            <span class="text-xl font-extrabold text-slate-900 mt-1 block">{{ $stats['total_activities'] }}</span>
            <span class="text-[10px] text-slate-500">In selected period</span>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 block">Store Visits</span>
            <span class="text-xl font-extrabold text-blue-900 mt-1 block">{{ $stats['store_visits'] }}</span>
            <span class="text-[10px] text-blue-600/80">In-person checks</span>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-purple-600 block">New Onboardings</span>
            <span class="text-xl font-extrabold text-purple-900 mt-1 block">{{ $stats['new_onboardings'] }}</span>
            <span class="text-[10px] text-purple-600/80">Customers added</span>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block">Orders Booked</span>
            <span class="text-xl font-extrabold text-emerald-900 mt-1 block">{{ $stats['orders_count'] }}</span>
            <span class="text-[10px] text-emerald-600/80">Total orders</span>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Period Sales</span>
            <span class="text-xl font-extrabold text-slate-900 mt-1 block">₦{{ number_format($stats['orders_total_amount'] / 1000000, 1) }}M</span>
            <span class="text-[10px] text-slate-500">₦{{ number_format($stats['orders_total_amount']) }}</span>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 block">Period Collections</span>
            <span class="text-xl font-extrabold text-amber-900 mt-1 block">₦{{ number_format($stats['collections_total'] / 1000000, 1) }}M</span>
            <span class="text-[10px] text-amber-600/80">Cash/Bank collected</span>
        </div>
    </div>

    <!-- Agent Comparison Scorecard for this Period -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h4 class="text-sm font-bold text-slate-900">Field Agent Performance Scorecard ({{ Carbon\Carbon::parse($startDate)->format('M d') }} - {{ Carbon\Carbon::parse($endDate)->format('M d, Y') }})</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                        <th class="py-2.5 px-4">Field Executive</th>
                        <th class="py-2.5 px-4">Territory</th>
                        <th class="py-2.5 px-4 text-center">Visits Completed</th>
                        <th class="py-2.5 px-4 text-center">Customers Onboarded</th>
                        <th class="py-2.5 px-4 text-center">Orders Count</th>
                        <th class="py-2.5 px-4 text-right">Revenue Secured</th>
                        <th class="py-2.5 px-4 text-right">Collections Done</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($agentScorecards as $sc)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-bold text-slate-900">
                                <a href="{{ route('agents.show', $sc) }}" class="hover:text-blue-600">
                                    {{ $sc->name }}
                                </a>
                                <span class="text-[10px] text-slate-400 font-mono ml-1">({{ $sc->agent_code }})</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $sc->territory }}</td>
                            <td class="py-3 px-4 text-center font-extrabold text-slate-900">{{ $sc->period_visits_count }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                    {{ $sc->period_onboard_count }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center font-semibold">{{ $sc->period_orders_count }}</td>
                            <td class="py-3 px-4 text-right font-extrabold text-emerald-600">₦{{ number_format($sc->period_revenue) }}</td>
                            <td class="py-3 px-4 text-right font-extrabold text-amber-600">₦{{ number_format($sc->period_collections) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detailed Activity Logs Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h4 class="text-sm font-bold text-slate-900">Detailed Activity Records Log ({{ $activities->total() }} entries)</h4>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                        <th class="py-3 px-4">Date & Time</th>
                        <th class="py-3 px-4">Field Rep</th>
                        <th class="py-3 px-4">Customer Store</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4">Purpose & Notes</th>
                        <th class="py-3 px-4">Outcome</th>
                        <th class="py-3 px-4">Location</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($activities as $act)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono text-slate-700 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $act->activity_date->format('M d, Y') }}</div>
                                <div class="text-[10px] text-slate-400">{{ substr($act->check_in_time, 0, 5) }}</div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <a href="{{ route('agents.show', $act->user) }}" class="font-bold text-slate-900 hover:text-blue-600">
                                    {{ $act->user?->name }}
                                </a>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $act->user?->agent_code }}</div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($act->customer)
                                    <a href="{{ route('customers.show', $act->customer) }}" class="font-bold text-blue-700 hover:underline">
                                        {{ $act->customer->company_name }}
                                    </a>
                                    <div class="text-[10px] text-slate-400">{{ $act->customer->contact_person }}</div>
                                @else
                                    <span class="text-slate-500 italic">Prospective Store</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700">
                                    {{ $act->getActivityTypeLabelAttribute() }}
                                </span>
                            </td>
                            <td class="py-3 px-4 max-w-xs">
                                <div class="font-medium text-slate-800 line-clamp-2">{{ $act->purpose }}</div>
                                @if($act->notes)
                                    <div class="text-[11px] text-slate-500 italic line-clamp-1 mt-0.5">{{ $act->notes }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold
                                    {{ $act->outcome === 'order_taken' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 
                                      ($act->outcome === 'payment_received' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-blue-50 text-blue-700') }}">
                                    {{ $act->getOutcomeLabelAttribute() }}
                                </span>
                            </td>
                            <td class="py-3 px-4 max-w-xs truncate text-[11px] text-slate-500">
                                {{ $act->location_address ?: ($act->customer?->address ?: '-') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-400">
                                No activity records match your filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $activities->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

