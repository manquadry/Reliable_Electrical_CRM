@extends('layouts.app')

@section('title', 'Field Agent Dashboard')
@section('header_title', 'My Field Workspace')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Agent Welcome & Territory Card -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white rounded-2xl p-5 sm:p-6 shadow-xl border border-blue-800/40">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-500 to-indigo-400 text-white flex items-center justify-center text-xl font-bold shadow-lg shadow-blue-500/30">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs bg-emerald-500/20 text-emerald-300 px-2 py-0.5 rounded-full font-semibold border border-emerald-500/30">
                            Active in Field
                        </span>
                        <span class="text-xs text-slate-300 font-mono">{{ $user->agent_code }}</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-white mt-1">{{ $user->name }}</h2>
                    <p class="text-xs text-blue-200 mt-0.5 flex items-center space-x-1">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <span>{{ $user->territory }}</span>
                        @if($user->vehicle_type)
                            <span>&bull; {{ $user->vehicle_type }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Fast Quick Actions Row -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('activities.create_checkin') }}" 
                   class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-lg shadow-blue-600/30 transition-all">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>Log Field Check-In</span>
                </a>
                <a href="{{ route('customers.create') }}" 
                   class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold border border-white/20 transition-all">
                    <i class="fa-solid fa-shop"></i>
                    <span>Onboard Customer</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 4 High Touch Field Action Cards (Optimized for Mobile) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('activities.create_checkin') }}" 
           class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-blue-500 shadow-xs hover:shadow-md transition-all flex flex-col items-center text-center group">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-colors flex items-center justify-center text-lg mb-2">
                <i class="fa-solid fa-location-dot"></i>
            </div>
            <span class="text-xs font-bold text-slate-900 group-hover:text-blue-600">Store Check-In</span>
            <span class="text-[10px] text-slate-400 mt-0.5">GPS Visit Log</span>
        </a>

        <a href="{{ route('customers.create') }}" 
           class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-blue-500 shadow-xs hover:shadow-md transition-all flex flex-col items-center text-center group">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors flex items-center justify-center text-lg mb-2">
                <i class="fa-solid fa-shop"></i>
            </div>
            <span class="text-xs font-bold text-slate-900 group-hover:text-blue-600">Onboard Store</span>
            <span class="text-[10px] text-slate-400 mt-0.5">New Customer Reg.</span>
        </a>

        <a href="{{ route('orders.create') }}" 
           class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-blue-500 shadow-xs hover:shadow-md transition-all flex flex-col items-center text-center group">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors flex items-center justify-center text-lg mb-2">
                <i class="fa-solid fa-cart-plus"></i>
            </div>
            <span class="text-xs font-bold text-slate-900 group-hover:text-blue-600">Book Order</span>
            <span class="text-[10px] text-slate-400 mt-0.5">Wholesale / Retail</span>
        </a>

        <a href="{{ route('payments.create') }}" 
           class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-blue-500 shadow-xs hover:shadow-md transition-all flex flex-col items-center text-center group">
            <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors flex items-center justify-center text-lg mb-2">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <span class="text-xs font-bold text-slate-900 group-hover:text-blue-600">Collect Payment</span>
            <span class="text-[10px] text-slate-400 mt-0.5">Bank Trf / Cash / POS</span>
        </a>
    </div>

    <!-- Agent Target & Performance Gauges -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        
        <!-- Daily Visit Target Meter -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Today's Visits Target</span>
                <span class="text-xs font-bold {{ $myTodayVisits->count() >= $user->daily_visit_target ? 'text-emerald-600' : 'text-blue-600' }}">
                    {{ $myTodayVisits->count() }} of {{ $user->daily_visit_target }} visits
                </span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden mb-2">
                @php
                    $visitPct = $user->daily_visit_target > 0 ? min(100, round(($myTodayVisits->count() / $user->daily_visit_target) * 100)) : 0;
                @endphp
                <div class="h-3 rounded-full {{ $visitPct >= 100 ? 'bg-emerald-500' : 'bg-blue-600' }}" style="width: {{ $visitPct }}%"></div>
            </div>
            <p class="text-[11px] text-slate-500">
                {{ $visitPct >= 100 ? '🎉 Daily target completed!' : ($user->daily_visit_target - $myTodayVisits->count()) . ' more visits needed today.' }}
            </p>
        </div>

        <!-- Monthly Customer Onboarding Target -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Month's Onboarding Goal</span>
                <span class="text-xs font-bold text-purple-600">
                    {{ $myMonthOnboardedCount }} / {{ $user->monthly_onboard_target }} stores
                </span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden mb-2">
                @php
                    $onboardPct = $user->monthly_onboard_target > 0 ? min(100, round(($myMonthOnboardedCount / $user->monthly_onboard_target) * 100)) : 0;
                @endphp
                <div class="h-3 rounded-full bg-purple-600" style="width: {{ $onboardPct }}%"></div>
            </div>
            <p class="text-[11px] text-slate-500">
                Total onboarded to date: <strong class="text-slate-800">{{ $myTotalCustomers }} active clients</strong>.
            </p>
        </div>

        <!-- Monthly Sales Revenue Target -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Month's Revenue Target</span>
                <span class="text-xs font-bold text-emerald-600">
                    ₦{{ number_format($myMonthRevenue) }}
                </span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden mb-2">
                @php
                    $revPct = $user->monthly_revenue_target > 0 ? min(100, round(($myMonthRevenue / $user->monthly_revenue_target) * 100)) : 0;
                @endphp
                <div class="h-3 rounded-full bg-emerald-500" style="width: {{ $revPct }}%"></div>
            </div>
            <p class="text-[11px] text-slate-500">
                Monthly Target: ₦{{ number_format($user->monthly_revenue_target) }} ({{ $revPct }}% achieved).
            </p>
        </div>

    </div>

    <!-- Two Columns: Today's Logged Activities + My Recently Onboarded Customers -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Left: Today's Field Visits Stream -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-list-check text-blue-600"></i>
                    <h3 class="text-sm font-bold text-slate-900">My Activities Today ({{ Carbon\Carbon::parse($today)->format('M d, Y') }})</h3>
                </div>
                <a href="{{ route('activities.create_checkin') }}" class="text-xs text-blue-600 font-semibold hover:underline">
                    + Log Visit
                </a>
            </div>

            @if($myTodayVisits->count() > 0)
                <div class="space-y-3">
                    @foreach($myTodayVisits as $visit)
                        <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/70 text-xs">
                            <div class="flex items-start justify-between">
                                <div class="font-bold text-slate-900 text-sm">
                                    {{ $visit->customer?->company_name ?: 'Prospective Store' }}
                                </div>
                                <span class="px-2 py-0.5 rounded font-mono text-[10px] bg-blue-100 text-blue-800 font-semibold">
                                    {{ substr($visit->check_in_time, 0, 5) }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                <span class="font-medium text-slate-700">{{ $visit->getActivityTypeLabelAttribute() }}</span>
                                @if($visit->customer?->address)
                                    &bull; {{ $visit->customer->address }}
                                @endif
                            </div>
                            <p class="text-slate-600 mt-2">{{ $visit->purpose }}</p>
                            <div class="mt-2.5 pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px]">
                                <span class="font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                    Outcome: {{ $visit->getOutcomeLabelAttribute() }}
                                </span>
                                @if($visit->next_followup_date)
                                    <span class="text-slate-400">Next: {{ $visit->next_followup_date->format('M d') }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 px-4">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2 text-lg">
                        <i class="fa-solid fa-person-walking"></i>
                    </div>
                    <p class="text-xs font-semibold text-slate-700">No visits logged yet today</p>
                    <p class="text-[11px] text-slate-400 mt-1 max-w-xs mx-auto">Start your route and log your check-ins at retailers, wholesalers and showrooms.</p>
                    <a href="{{ route('activities.create_checkin') }}" class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white font-bold rounded-xl text-xs shadow-sm hover:bg-blue-700">
                        Check In Now
                    </a>
                </div>
            @endif
        </div>

        <!-- Right: Customers Onboarded by Me -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-shop text-purple-600"></i>
                    <h3 class="text-sm font-bold text-slate-900">Recently Onboarded Customers</h3>
                </div>
                <a href="{{ route('customers.index') }}" class="text-xs text-blue-600 font-semibold hover:underline">
                    View All ({{ $myTotalCustomers }}) &rarr;
                </a>
            </div>

            @if($myRecentOnboards->count() > 0)
                <div class="space-y-3">
                    @foreach($myRecentOnboards as $cust)
                        <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/70 hover:bg-slate-50 transition-colors text-xs flex items-start justify-between">
                            <div>
                                <a href="{{ route('customers.show', $cust) }}" class="font-bold text-slate-900 hover:text-blue-600 text-sm">
                                    {{ $cust->company_name }}
                                </a>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $cust->contact_person }} &bull; <a href="tel:{{ $cust->phone }}" class="text-blue-600 hover:underline">{{ $cust->phone }}</a>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-1">
                                    {{ $cust->address }}, {{ $cust->city }}
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                    {{ $cust->getCategoryLabelAttribute() }}
                                </span>
                                <div class="text-[10px] text-slate-400 mt-1">
                                    Onboarded: {{ $cust->onboarded_date->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center py-8 text-slate-400 text-xs">You haven't onboarded any customers yet.</p>
            @endif
        </div>

    </div>

</div>
@endsection

