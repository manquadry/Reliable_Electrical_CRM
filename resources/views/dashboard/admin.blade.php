@extends('layouts.app')

@section('title', Auth::user()->isSalesManager() ? 'Field Sales Lead Dashboard' : 'Executive Dashboard')
@section('header_title', Auth::user()->isSalesManager() ? 'Field Operations & Team Leadership' : 'Executive Operations Command')

@section('content')
<div class="space-y-6">

    <!-- Top Executive Command Hero Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-950 via-slate-900 to-brand-950 p-6 sm:p-7 text-white shadow-xl border border-slate-800/80">
        <!-- Background Ambient Glow -->
        <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-brand-600/15 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 h-64 w-64 rounded-full bg-indigo-600/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Lagos Operations Live</span>
                    </span>
                    <span class="text-xs text-slate-400 font-medium">
                        Reliable Electrics Nigeria Limited • {{ Auth::user()->isSalesManager() ? 'Department of Field Sales & Distribution' : 'Commercial Command HQ' }}
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                    Welcome back, {{ Auth::user()->name }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                    {{ Auth::user()->isSalesManager() ? 'Supervise field sales executives, monitor live territory check-in compliance, onboard new sales reps, and drive retail distribution across Lagos.' : 'Consolidated view of Lagos territory route compliance, daily field check-ins, customer onboarding velocity, and commercial revenue performance.' }}
                </p>
            </div>

            <!-- Header Action Button Group -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('activities.daily') }}" 
                   class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold shadow-lg shadow-brand-600/30 hover:shadow-brand-600/50 transition-all">
                    <i class="fa-solid fa-calendar-day"></i>
                    <span>Date-by-Date Tracker</span>
                </a>
                <a href="{{ route('agents.create') }}" 
                   class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 text-white text-xs font-semibold border border-white/15 backdrop-blur-sm transition-all">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Onboard Agent</span>
                </a>
                <a href="{{ route('activities.report') }}" 
                   class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition-all">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Activity Audit</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 4 Key Executive Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Field Agents Active -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Field Executives</p>
                    <div class="flex items-baseline space-x-2 mt-1.5">
                        <h3 class="text-3xl font-extrabold text-slate-900 font-mono tracking-tight">{{ $totalActiveAgents }}</h3>
                        <span class="inline-flex items-center space-x-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Active</span>
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Out of {{ $totalAgents }} registered agents</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl shrink-0 group-hover:bg-brand-600 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-users-gear"></i>
                </div>
            </div>
        </div>

        <!-- Total Customers Onboarded -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Retail & Wholesale Clients</p>
                    <div class="flex items-baseline space-x-2 mt-1.5">
                        <h3 class="text-3xl font-extrabold text-slate-900 font-mono tracking-tight">{{ $totalCustomers }}</h3>
                        <span class="text-[11px] font-bold text-brand-600 bg-brand-50 px-2 py-0.5 rounded-full border border-brand-100">
                            +{{ $newCustomersThisMonth }} this mo.
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Verified electrical retail stores</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-shop"></i>
                </div>
            </div>
        </div>

        <!-- Field Visits Today -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Field Visits Today</p>
                    <div class="flex items-baseline space-x-2 mt-1.5">
                        <h3 class="text-3xl font-extrabold text-slate-900 font-mono tracking-tight">{{ $visitsToday }}</h3>
                        <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                            GPS Verified
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1 font-medium">{{ Carbon\Carbon::parse($today)->format('D, M d, Y') }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
            </div>
        </div>

        <!-- Monthly Sales Volume -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-card hover:shadow-card-hover transition-all group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Month's Sales Volume</p>
                    <div class="flex items-baseline space-x-2 mt-1.5">
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tracking-tight">₦{{ number_format($ordersThisMonth / 1000000, 1) }}M</h3>
                        <span class="text-[11px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-100">
                            Orders
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1 font-mono">₦{{ number_format($collectionsThisMonth) }} collected</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- Field Agent Compliance & Daily Performance Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-card overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
            <div>
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-clipboard-check text-brand-600"></i>
                    <h2 class="text-base font-bold text-slate-900">Field Executives Performance & Target Compliance</h2>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Real-time tracking of daily field visits, store customer onboarding count, and monthly sales quota compliance.</p>
            </div>
            <a href="{{ route('activities.daily') }}" class="inline-flex items-center space-x-1.5 text-xs font-bold text-brand-600 hover:text-brand-700 transition-colors">
                <span>View Full Daily Activity Tracker</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-100/70 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider text-[11px]">
                        <th class="py-3 px-4">Field Executive</th>
                        <th class="py-3 px-4">Assigned Territory</th>
                        <th class="py-3 px-4 text-center">Visits Today</th>
                        <th class="py-3 px-4 text-center">Daily Quota</th>
                        <th class="py-3 px-4">Daily Progress</th>
                        <th class="py-3 px-4 text-center">New Onboards</th>
                        <th class="py-3 px-4 text-right">Month's Sales</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-sans">
                    @forelse($fieldAgents as $agent)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-600 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-sm">
                                        {{ substr($agent->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm">{{ $agent->name }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono flex items-center space-x-1.5">
                                            <span class="bg-slate-100 text-slate-600 px-1.5 py-0.2 rounded font-semibold">{{ $agent->agent_code }}</span>
                                            <span>&bull;</span>
                                            <span>{{ $agent->phone }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700">
                                <div class="flex items-center space-x-1.5 font-semibold">
                                    <i class="fa-solid fa-route text-brand-500 text-xs"></i>
                                    <span>{{ $agent->territory }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5 flex items-center space-x-1">
                                    <i class="fa-solid fa-motorcycle text-[9px]"></i>
                                    <span>{{ $agent->vehicle_type ?: 'Motorbike' }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="font-mono font-extrabold text-base {{ $agent->today_visits_count >= $agent->daily_visit_target ? 'text-emerald-600' : 'text-slate-900' }}">
                                    {{ $agent->today_visits_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center text-slate-500 font-medium">
                                <span class="font-mono font-semibold text-slate-700">{{ $agent->daily_visit_target }}</span> visits/day
                            </td>
                            <td class="py-3.5 px-4 min-w-[150px]">
                                <div class="flex items-center justify-between text-[11px] mb-1">
                                    <span class="font-bold font-mono {{ $agent->visit_target_percent >= 100 ? 'text-emerald-600' : 'text-slate-700' }}">
                                        {{ $agent->visit_target_percent }}%
                                    </span>
                                    <span class="text-slate-400 font-mono">{{ $agent->today_visits_count }}/{{ $agent->daily_visit_target }}</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden ring-1 ring-slate-200/50">
                                    <div class="h-2 rounded-full transition-all duration-500 {{ $agent->visit_target_percent >= 100 ? 'bg-emerald-500' : ($agent->visit_target_percent >= 50 ? 'bg-brand-600' : 'bg-amber-500') }}"
                                         style="width: {{ min(100, $agent->visit_target_percent) }}%"></div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                    {{ $agent->month_onboards_count }} clients
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="font-bold font-mono text-slate-900 text-xs">₦{{ number_format($agent->month_revenue) }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">Target: ₦{{ number_format($agent->monthly_revenue_target) }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('agents.show', $agent) }}" 
                                   class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-brand-50 hover:text-brand-700 hover:border-brand-200 font-semibold text-xs transition-colors shadow-2xs">
                                    <span>Profile</span>
                                    <i class="fa-solid fa-chevron-right text-[9px] text-slate-400"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400 font-medium">No field agents registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Two Columns: Recent Field Activity Feed + Territory Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Live Field Activity Stream (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-card p-5">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h3 class="text-base font-bold text-slate-900">Live Field Activities & Check-In Feed</h3>
                </div>
                <a href="{{ route('activities.daily') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700 flex items-center space-x-1">
                    <span>View by Date</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentActivities as $act)
                    <div class="p-3.5 rounded-xl border border-slate-200/70 hover:border-brand-300 bg-slate-50/40 hover:bg-brand-50/20 transition-all flex items-start justify-between gap-3 text-xs">
                        <div class="flex items-start space-x-3 min-w-0">
                            <div class="w-9 h-9 rounded-xl mt-0.5 shrink-0 flex items-center justify-center font-bold shadow-2xs
                                {{ $act->activity_type === 'store_visit' ? 'bg-blue-100 text-blue-700' : 
                                  ($act->activity_type === 'new_customer_onboarding' ? 'bg-purple-100 text-purple-700' : 
                                  ($act->activity_type === 'order_booking' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700')) }}">
                                <i class="fa-solid {{ $act->activity_type === 'store_visit' ? 'fa-store' : ($act->activity_type === 'new_customer_onboarding' ? 'fa-user-plus' : ($act->activity_type === 'order_booking' ? 'fa-cart-plus' : 'fa-money-bill-wave')) }} text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="font-bold text-slate-900">{{ $act->user?->name }}</span>
                                    <span class="text-slate-300">&bull;</span>
                                    <span class="font-semibold text-brand-700 truncate max-w-[200px]">{{ $act->customer?->company_name ?: 'Prospective Store' }}</span>
                                    <span class="text-[10px] bg-slate-200/80 text-slate-700 px-2 py-0.5 rounded-md font-medium">
                                        {{ $act->getActivityTypeLabelAttribute() }}
                                    </span>
                                </div>
                                <p class="text-slate-600 mt-1 line-clamp-2 leading-relaxed">{{ $act->purpose }}</p>
                                @if($act->location_address)
                                    <div class="flex items-center space-x-1.5 text-[11px] text-slate-500 mt-1.5 bg-slate-100/80 px-2 py-0.5 rounded-md w-fit">
                                        <i class="fa-solid fa-location-dot text-rose-500 text-[10px]"></i>
                                        <span class="font-medium truncate max-w-sm">{{ $act->location_address }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="inline-block px-2.5 py-1 rounded-md text-[11px] font-bold
                                {{ $act->outcome === 'order_taken' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 
                                  ($act->outcome === 'payment_received' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                                {{ $act->getOutcomeLabelAttribute() }}
                            </span>
                            <div class="text-[10px] text-slate-400 mt-1.5 font-mono">
                                {{ $act->activity_date->format('M d') }} • {{ substr($act->check_in_time, 0, 5) }}
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center py-8 text-slate-400 text-xs">No field activities recorded yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Right Side: Territory & Customer Distribution (1 col) -->
        <div class="space-y-6">
            
            <!-- Quick Actions Panel -->
            <div class="bg-gradient-to-br from-brand-600 via-indigo-600 to-brand-800 text-white rounded-2xl p-5 shadow-lg shadow-brand-600/20">
                <h3 class="font-bold text-sm mb-1 flex items-center space-x-2">
                    <i class="fa-solid fa-bolt"></i>
                    <span>Quick Field Actions</span>
                </h3>
                <p class="text-xs text-brand-100 mb-4">Fast administrative and commercial operations shortcuts.</p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <a href="{{ route('activities.create_checkin') }}" class="p-3 rounded-xl bg-white/10 hover:bg-white/20 transition-all font-semibold flex items-center space-x-2 border border-white/10">
                        <i class="fa-solid fa-location-dot text-amber-300"></i>
                        <span>New Check-In</span>
                    </a>
                    <a href="{{ route('customers.create') }}" class="p-3 rounded-xl bg-white/10 hover:bg-white/20 transition-all font-semibold flex items-center space-x-2 border border-white/10">
                        <i class="fa-solid fa-shop text-cyan-300"></i>
                        <span>New Customer</span>
                    </a>
                    <a href="{{ route('orders.create') }}" class="p-3 rounded-xl bg-white/10 hover:bg-white/20 transition-all font-semibold flex items-center space-x-2 border border-white/10">
                        <i class="fa-solid fa-cart-plus text-emerald-300"></i>
                        <span>Create Order</span>
                    </a>
                    <a href="{{ route('payments.create') }}" class="p-3 rounded-xl bg-white/10 hover:bg-white/20 transition-all font-semibold flex items-center space-x-2 border border-white/10">
                        <i class="fa-solid fa-receipt text-yellow-300"></i>
                        <span>Log Payment</span>
                    </a>
                </div>
            </div>

            <!-- Territory Distribution -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-card p-5">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="font-bold text-sm text-slate-900">Customers by Territory</h3>
                    <span class="text-[11px] font-mono font-bold text-brand-600 bg-brand-50 px-2 py-0.5 rounded-full">{{ $totalCustomers }} Total</span>
                </div>
                <p class="text-xs text-slate-500 mb-4">Onboarded customer concentration across Lagos zones.</p>

                <div class="space-y-3.5">
                    @foreach($territoryStats as $t)
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-semibold text-slate-700 truncate max-w-[180px]">{{ $t->territory }}</span>
                                <span class="font-mono font-bold text-slate-900">{{ $t->count }} stores</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden ring-1 ring-slate-200/50">
                                <div class="bg-gradient-to-r from-brand-600 to-indigo-600 h-2 rounded-full transition-all duration-500" 
                                     style="width: {{ $totalCustomers > 0 ? ($t->count / $totalCustomers) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
