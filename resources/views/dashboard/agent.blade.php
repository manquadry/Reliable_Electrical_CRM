@extends('layouts.app')

@section('title', 'Field Executive Workspace')
@section('header_title', 'My Field Workspace')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Agent Welcome & Territory Card -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-950 via-slate-900 to-brand-950 p-6 sm:p-7 text-white shadow-xl border border-slate-800/80">
        <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-brand-600/15 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-brand-600 to-indigo-600 text-white flex items-center justify-center text-2xl font-extrabold shadow-glow shrink-0 ring-1 ring-white/20">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center space-x-1.5 text-xs bg-emerald-500/15 text-emerald-400 px-2.5 py-0.5 rounded-full font-bold border border-emerald-500/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Active in Field</span>
                        </span>
                        <span class="text-xs text-slate-300 font-mono font-semibold bg-slate-800/80 px-2 py-0.5 rounded-md border border-slate-700/60">
                            {{ $user->agent_code }}
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white mt-1.5 tracking-tight">{{ $user->name }}</h1>
                    <p class="text-xs text-brand-200 mt-1 flex flex-wrap items-center gap-1.5 font-medium">
                        <i class="fa-solid fa-map-location-dot text-brand-400"></i>
                        <span>{{ $user->territory }}</span>
                        @if($user->vehicle_type)
                            <span class="text-slate-500">&bull;</span>
                            <span class="flex items-center space-x-1">
                                <i class="fa-solid fa-motorcycle text-slate-400"></i>
                                <span>{{ $user->vehicle_type }}</span>
                            </span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Fast Quick Actions Row -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('activities.create_checkin') }}" 
                   class="inline-flex items-center space-x-2 px-5 py-3 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold shadow-lg shadow-brand-600/30 hover:shadow-brand-600/50 transition-all">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>Log Field Check-In</span>
                </a>
                <a href="{{ route('customers.create') }}" 
                   class="inline-flex items-center space-x-2 px-4 py-3 rounded-xl bg-white/10 hover:bg-white/15 text-white text-xs font-semibold border border-white/15 backdrop-blur-sm transition-all">
                    <i class="fa-solid fa-shop"></i>
                    <span>Onboard Customer</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 4 High Touch Field Action Cards (Optimized for Mobile Ergonomics) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
        <a href="{{ route('activities.create_checkin') }}" 
           class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 hover:border-brand-500 shadow-card hover:shadow-card-hover hover:-translate-y-0.5 transition-all flex flex-col items-center text-center group">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-colors flex items-center justify-center text-xl mb-2.5 shadow-2xs">
                <i class="fa-solid fa-location-dot"></i>
            </div>
            <span class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-brand-600 transition-colors">Store Check-In</span>
            <span class="text-[11px] text-slate-400 mt-0.5 font-medium">GPS Visit Log</span>
        </a>

        <a href="{{ route('customers.create') }}" 
           class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 hover:border-brand-500 shadow-card hover:shadow-card-hover hover:-translate-y-0.5 transition-all flex flex-col items-center text-center group">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors flex items-center justify-center text-xl mb-2.5 shadow-2xs">
                <i class="fa-solid fa-shop"></i>
            </div>
            <span class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-brand-600 transition-colors">Onboard Store</span>
            <span class="text-[11px] text-slate-400 mt-0.5 font-medium">New Retailer Reg.</span>
        </a>

        <a href="{{ route('orders.create') }}" 
           class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 hover:border-brand-500 shadow-card hover:shadow-card-hover hover:-translate-y-0.5 transition-all flex flex-col items-center text-center group">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors flex items-center justify-center text-xl mb-2.5 shadow-2xs">
                <i class="fa-solid fa-cart-plus"></i>
            </div>
            <span class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-brand-600 transition-colors">Book Order</span>
            <span class="text-[11px] text-slate-400 mt-0.5 font-medium">Wholesale / Retail</span>
        </a>

        <a href="{{ route('payments.create') }}" 
           class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 hover:border-brand-500 shadow-card hover:shadow-card-hover hover:-translate-y-0.5 transition-all flex flex-col items-center text-center group">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-colors flex items-center justify-center text-xl mb-2.5 shadow-2xs">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <span class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-brand-600 transition-colors">Collect Payment</span>
            <span class="text-[11px] text-slate-400 mt-0.5 font-medium">Bank / Cash / POS</span>
        </a>
    </div>

    <!-- Agent Target & Performance Gauges -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        
        <!-- Daily Visit Target Meter -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-card">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Today's Visits Target</span>
                <span class="text-xs font-bold font-mono {{ $myTodayVisits->count() >= $user->daily_visit_target ? 'text-emerald-600' : 'text-brand-600' }}">
                    {{ $myTodayVisits->count() }} of {{ $user->daily_visit_target }} visits
                </span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden ring-1 ring-slate-200/50 mb-2">
                @php
                    $visitPct = $user->daily_visit_target > 0 ? min(100, round(($myTodayVisits->count() / $user->daily_visit_target) * 100)) : 0;
                @endphp
                <div class="h-2.5 rounded-full transition-all duration-500 {{ $visitPct >= 100 ? 'bg-emerald-500' : 'bg-brand-600' }}" style="width: {{ $visitPct }}%"></div>
            </div>
            <p class="text-[11px] text-slate-500 font-medium">
                {{ $visitPct >= 100 ? '🎉 Daily target completed!' : ($user->daily_visit_target - $myTodayVisits->count()) . ' more visits needed today.' }}
            </p>
        </div>

        <!-- Monthly Customer Onboarding Target -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-card">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Month's Onboarding Goal</span>
                <span class="text-xs font-bold font-mono text-purple-600">
                    {{ $myMonthOnboardedCount }} / {{ $user->monthly_onboard_target }} stores
                </span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden ring-1 ring-slate-200/50 mb-2">
                @php
                    $onboardPct = $user->monthly_onboard_target > 0 ? min(100, round(($myMonthOnboardedCount / $user->monthly_onboard_target) * 100)) : 0;
                @endphp
                <div class="h-2.5 rounded-full bg-purple-600 transition-all duration-500" style="width: {{ $onboardPct }}%"></div>
            </div>
            <p class="text-[11px] text-slate-500 font-medium">
                Total portfolio: <strong class="text-slate-800 font-mono">{{ $myTotalCustomers }} active clients</strong>.
            </p>
        </div>

        <!-- Monthly Sales Revenue Target -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-card">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Month's Revenue Target</span>
                <span class="text-xs font-bold font-mono text-emerald-600">
                    ₦{{ number_format($myMonthRevenue) }}
                </span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden ring-1 ring-slate-200/50 mb-2">
                @php
                    $revPct = $user->monthly_revenue_target > 0 ? min(100, round(($myMonthRevenue / $user->monthly_revenue_target) * 100)) : 0;
                @endphp
                <div class="h-2.5 rounded-full bg-emerald-500 transition-all duration-500" style="width: {{ $revPct }}%"></div>
            </div>
            <p class="text-[11px] text-slate-500 font-medium">
                Target: <span class="font-mono">₦{{ number_format($user->monthly_revenue_target) }}</span> ({{ $revPct }}% achieved).
            </p>
        </div>

    </div>

    <!-- Two Columns: Today's Logged Activities + My Recently Onboarded Customers -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Left: Today's Field Visits Stream -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-card p-5">
            <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-list-check text-brand-600"></i>
                    <h3 class="text-sm font-bold text-slate-900">My Activities Today ({{ Carbon\Carbon::parse($today)->format('M d, Y') }})</h3>
                </div>
                <a href="{{ route('activities.create_checkin') }}" class="text-xs text-brand-600 font-bold hover:text-brand-700 transition-colors">
                    + Log Visit
                </a>
            </div>

            @if($myTodayVisits->count() > 0)
                <div class="space-y-3">
                    @foreach($myTodayVisits as $visit)
                        <div class="p-3.5 rounded-xl border border-slate-200/70 hover:border-brand-300 bg-slate-50/40 hover:bg-brand-50/20 transition-all text-xs">
                            <div class="flex items-start justify-between">
                                <div class="font-bold text-slate-900 text-sm">
                                    {{ $visit->customer?->company_name ?: 'Prospective Store' }}
                                </div>
                                <span class="px-2 py-0.5 rounded-md font-mono text-[10px] bg-brand-50 text-brand-800 font-bold border border-brand-200/60">
                                    {{ substr($visit->check_in_time, 0, 5) }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-500 mt-1 flex flex-wrap items-center gap-1.5">
                                <span class="font-semibold text-slate-700">{{ $visit->getActivityTypeLabelAttribute() }}</span>
                                @if($visit->customer?->address)
                                    <span class="text-slate-300">&bull;</span>
                                    <span class="text-slate-400">{{ $visit->customer->address }}</span>
                                @endif
                            </div>
                            <p class="text-slate-600 mt-2 leading-relaxed">{{ $visit->purpose }}</p>
                            @if($visit->location_address)
                                <div class="flex items-center space-x-1.5 text-[11px] text-slate-500 mt-2 bg-slate-100/80 px-2 py-0.5 rounded-md w-fit">
                                    <i class="fa-solid fa-location-dot text-rose-500 text-[10px]"></i>
                                    <span class="font-medium truncate max-w-sm">{{ $visit->location_address }}</span>
                                </div>
                            @endif
                            <div class="mt-2.5 pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px]">
                                <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                    Outcome: {{ $visit->getOutcomeLabelAttribute() }}
                                </span>
                                @if($visit->next_followup_date)
                                    <span class="text-slate-400 font-medium">Next: {{ $visit->next_followup_date->format('M d') }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 px-4">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-lg">
                        <i class="fa-solid fa-person-walking"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-700">No visits logged yet today</p>
                    <p class="text-[11px] text-slate-400 mt-1 max-w-xs mx-auto">Start your territory route and log your check-ins at retailers, wholesalers and lighting showrooms.</p>
                    <a href="{{ route('activities.create_checkin') }}" class="inline-flex items-center space-x-2 mt-4 px-4 py-2.5 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl text-xs shadow-md shadow-brand-600/30 transition-all">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>Check In Now</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- Right: Customers Onboarded by Me -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-card p-5">
            <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-shop text-purple-600"></i>
                    <h3 class="text-sm font-bold text-slate-900">Recently Onboarded Customers</h3>
                </div>
                <a href="{{ route('customers.index') }}" class="text-xs text-brand-600 font-bold hover:text-brand-700 transition-colors">
                    View All ({{ $myTotalCustomers }}) &rarr;
                </a>
            </div>

            @if($myRecentOnboards->count() > 0)
                <div class="space-y-3">
                    @foreach($myRecentOnboards as $cust)
                        <div class="p-3.5 rounded-xl border border-slate-200/70 hover:border-brand-300 bg-slate-50/40 hover:bg-brand-50/20 transition-all text-xs flex items-start justify-between">
                            <div class="min-w-0">
                                <a href="{{ route('customers.show', $cust) }}" class="font-bold text-slate-900 hover:text-brand-600 text-sm transition-colors">
                                    {{ $cust->company_name }}
                                </a>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $cust->contact_person }} &bull; <a href="tel:{{ $cust->phone }}" class="text-brand-600 hover:underline font-medium">{{ $cust->phone }}</a>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-1 truncate max-w-sm">
                                    {{ $cust->address }}, {{ $cust->city }}
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                    {{ $cust->getCategoryLabelAttribute() }}
                                </span>
                                <div class="text-[10px] text-slate-400 mt-1 font-mono">
                                    {{ $cust->onboarded_date->format('M d, Y') }}
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
