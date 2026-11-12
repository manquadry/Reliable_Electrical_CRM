@extends('layouts.app')

@section('title', Auth::user()->isSalesManager() ? 'Field Sales Lead Dashboard' : 'Executive Dashboard')
@section('header_title', Auth::user()->isSalesManager() ? 'Field Operations & Team Leadership' : 'Executive Operations Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Top Notice Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 text-white rounded-2xl p-6 shadow-xl border border-blue-900/40 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2 text-blue-400 text-xs font-bold uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-bolt"></i>
                    <span>Reliable Electrics Nigeria Limited • {{ Auth::user()->isSalesManager() ? 'Department of Field Sales & Distribution' : 'Commercial Command Center' }}</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-white">
                    Welcome, {{ Auth::user()->name }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl">
                    {{ Auth::user()->isSalesManager() ? 'Supervise field sales executives, track territory route compliance, onboard new sales reps, and drive customer onboarding velocity across Lagos.' : 'Overview of field executive activity, territory route compliance, customer onboarding velocity, and commercial collections across Lagos.' }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('activities.daily') }}" 
                   class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-lg shadow-blue-600/30 transition-all">
                    <i class="fa-solid fa-calendar-day"></i>
                    <span>Date-by-Date Tracker</span>
                </a>
                <a href="{{ route('agents.create') }}" 
                   class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold border border-white/20 transition-all">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Onboard Agent</span>
                </a>
                <a href="{{ route('activities.report') }}" 
                   class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition-all">
                    <i class="fa-solid fa-file-excel"></i>
                    <span>Reports</span>
                </a>
            </div>
        </div>
        <!-- Decorative subtle background circle -->
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-blue-500/10 rounded-full blur-2xl"></div>
    </div>

    <!-- 4 Key Executive Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Field Agents Active -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Field Executives</p>
                <div class="flex items-baseline space-x-2 mt-1">
                    <h3 class="text-2xl font-extrabold text-slate-900">{{ $totalActiveAgents }}</h3>
                    <span class="text-xs font-medium text-emerald-600">Active</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-1">Out of {{ $totalAgents }} registered agents</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-users-gear"></i>
            </div>
        </div>

        <!-- Total Customers Onboarded -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Customers</p>
                <div class="flex items-baseline space-x-2 mt-1">
                    <h3 class="text-2xl font-extrabold text-slate-900">{{ $totalCustomers }}</h3>
                    <span class="text-xs font-bold text-blue-600">+{{ $newCustomersThisMonth }} this mo.</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-1">Stores & contractors verified</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-shop"></i>
            </div>
        </div>

        <!-- Field Visits Today -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Field Visits Today</p>
                <div class="flex items-baseline space-x-2 mt-1">
                    <h3 class="text-2xl font-extrabold text-slate-900">{{ $visitsToday }}</h3>
                    <span class="text-xs font-medium text-emerald-600">Logged</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-1">{{ Carbon\Carbon::parse($today)->format('D, M d, Y') }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-location-dot"></i>
            </div>
        </div>

        <!-- Monthly Sales Volume -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Month's Sales Orders</p>
                <div class="flex items-baseline space-x-2 mt-1">
                    <h3 class="text-2xl font-extrabold text-slate-900">₦{{ number_format($ordersThisMonth / 1000000, 1) }}M</h3>
                    <span class="text-xs font-medium text-blue-600">Total</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-1">₦{{ number_format($collectionsThisMonth) }} collected</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
        </div>

    </div>

    <!-- Field Agent Compliance & Daily Performance Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-slate-900">Field Executives Performance & Target Compliance</h3>
                <p class="text-xs text-slate-500">Live tracker of today's field visits, customer onboarding count, and monthly revenue performance.</p>
            </div>
            <a href="{{ route('activities.daily') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700">
                <span>View Full Daily Activity Tracker</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-semibold">
                        <th class="py-3 px-4">Field Agent</th>
                        <th class="py-3 px-4">Assigned Territory</th>
                        <th class="py-3 px-4 text-center">Visits Today</th>
                        <th class="py-3 px-4 text-center">Daily Target</th>
                        <th class="py-3 px-4">Today's Progress</th>
                        <th class="py-3 px-4 text-center">New Onboards</th>
                        <th class="py-3 px-4 text-right">Month's Sales</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($fieldAgents as $agent)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-xs shrink-0">
                                        {{ substr($agent->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $agent->name }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">{{ $agent->agent_code }} • {{ $agent->phone }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 font-medium">
                                <div class="flex items-center space-x-1.5">
                                    <i class="fa-solid fa-route text-slate-400 text-xs"></i>
                                    <span>{{ $agent->territory }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400">{{ $agent->vehicle_type ?: 'Motorbike' }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="font-extrabold text-sm {{ $agent->today_visits_count >= $agent->daily_visit_target ? 'text-emerald-600' : 'text-slate-900' }}">
                                    {{ $agent->today_visits_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center text-slate-500 font-medium">
                                {{ $agent->daily_visit_target }} visits/day
                            </td>
                            <td class="py-3.5 px-4 min-w-[140px]">
                                <div class="flex items-center justify-between text-[11px] mb-1">
                                    <span class="font-semibold text-slate-700">{{ $agent->visit_target_percent }}%</span>
                                    <span class="text-slate-400">{{ $agent->today_visits_count }}/{{ $agent->daily_visit_target }}</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full {{ $agent->visit_target_percent >= 100 ? 'bg-emerald-500' : ($agent->visit_target_percent >= 50 ? 'bg-blue-600' : 'bg-amber-500') }}"
                                         style="width: {{ $agent->visit_target_percent }}%"></div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                    {{ $agent->month_onboards_count }} clients
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="font-bold text-slate-900">₦{{ number_format($agent->month_revenue) }}</div>
                                <div class="text-[10px] text-slate-400">Target: ₦{{ number_format($agent->monthly_revenue_target) }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('agents.show', $agent) }}" 
                                   class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-100 font-medium text-xs transition-colors">
                                    <span>Profile</span>
                                    <i class="fa-solid fa-chevron-right text-[9px] text-slate-400"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-6 text-slate-400">No field agents registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Two Columns: Recent Field Activity Feed + Territory Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Live Field Activity Stream (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h3 class="text-base font-bold text-slate-900">Recent Field Activities & Check-Ins</h3>
                </div>
                <a href="{{ route('activities.daily') }}" class="text-xs font-semibold text-blue-600 hover:underline">
                    View by Date &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentActivities as $act)
                    <div class="p-3.5 rounded-xl border border-slate-100 hover:border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition-colors flex items-start justify-between gap-3 text-xs">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-lg mt-0.5 shrink-0 flex items-center justify-center font-bold
                                {{ $act->activity_type === 'store_visit' ? 'bg-blue-100 text-blue-700' : 
                                  ($act->activity_type === 'new_customer_onboarding' ? 'bg-purple-100 text-purple-700' : 
                                  ($act->activity_type === 'order_booking' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700')) }}">
                                <i class="fa-solid {{ $act->activity_type === 'store_visit' ? 'fa-store' : ($act->activity_type === 'new_customer_onboarding' ? 'fa-user-plus' : ($act->activity_type === 'order_booking' ? 'fa-cart-plus' : 'fa-money-bill-wave')) }} text-xs"></i>
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="font-bold text-slate-900">{{ $act->user?->name }}</span>
                                    <span class="text-slate-400">&bull;</span>
                                    <span class="font-semibold text-blue-700">{{ $act->customer?->company_name ?: 'Prospective Store' }}</span>
                                    <span class="text-[10px] bg-slate-200 text-slate-700 px-1.5 py-0.2 rounded font-medium">
                                        {{ $act->getActivityTypeLabelAttribute() }}
                                    </span>
                                </div>
                                <p class="text-slate-600 mt-1 line-clamp-2">{{ $act->purpose }}</p>
                                @if($act->location_address)
                                    <div class="flex items-center space-x-1 text-[11px] text-slate-400 mt-1">
                                        <i class="fa-solid fa-map-pin text-slate-400 text-[10px]"></i>
                                        <span>{{ $act->location_address }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold
                                {{ $act->outcome === 'order_taken' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 
                                  ($act->outcome === 'payment_received' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-slate-100 text-slate-700') }}">
                                {{ $act->getOutcomeLabelAttribute() }}
                            </span>
                            <div class="text-[10px] text-slate-400 mt-1 font-mono">
                                {{ $act->activity_date->format('M d') }} • {{ substr($act->check_in_time, 0, 5) }}
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center py-6 text-slate-400 text-xs">No activities recorded yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Right Side: Territory & Customer Distribution (1 col) -->
        <div class="space-y-6">
            
            <!-- Quick Actions Panel -->
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white rounded-2xl p-5 shadow-lg shadow-blue-500/20">
                <h4 class="font-bold text-sm mb-1">Field Actions & Quick Links</h4>
                <p class="text-xs text-blue-100 mb-4">Fast shortcuts for administrative and field operations.</p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <a href="{{ route('activities.create_checkin') }}" class="p-2.5 rounded-xl bg-white/10 hover:bg-white/20 transition-all font-semibold flex items-center space-x-2 border border-white/10">
                        <i class="fa-solid fa-location-dot text-amber-300"></i>
                        <span>New Check-In</span>
                    </a>
                    <a href="{{ route('customers.create') }}" class="p-2.5 rounded-xl bg-white/10 hover:bg-white/20 transition-all font-semibold flex items-center space-x-2 border border-white/10">
                        <i class="fa-solid fa-shop text-cyan-300"></i>
                        <span>New Customer</span>
                    </a>
                    <a href="{{ route('orders.create') }}" class="p-2.5 rounded-xl bg-white/10 hover:bg-white/20 transition-all font-semibold flex items-center space-x-2 border border-white/10">
                        <i class="fa-solid fa-cart-plus text-emerald-300"></i>
                        <span>Create Order</span>
                    </a>
                    <a href="{{ route('payments.create') }}" class="p-2.5 rounded-xl bg-white/10 hover:bg-white/20 transition-all font-semibold flex items-center space-x-2 border border-white/10">
                        <i class="fa-solid fa-receipt text-yellow-300"></i>
                        <span>Log Payment</span>
                    </a>
                </div>
            </div>

            <!-- Territory Distribution -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
                <h4 class="font-bold text-sm text-slate-900 mb-1">Customers by Territory</h4>
                <p class="text-xs text-slate-500 mb-4">Onboarded customer concentration across Lagos zones.</p>

                <div class="space-y-3">
                    @foreach($territoryStats as $t)
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-medium text-slate-700 truncate max-w-[200px]">{{ $t->territory }}</span>
                                <span class="font-bold text-slate-900">{{ $t->count }} stores</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-blue-600 h-1.5 rounded-full" 
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

