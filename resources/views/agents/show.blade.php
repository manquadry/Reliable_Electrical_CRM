@extends('layouts.app')

@section('title', $agent->name . ' - Field Executive Profile')
@section('header_title', 'Field Executive Profile')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Agent Header Card -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-2xl p-6 shadow-xl border border-blue-900/40">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 text-white flex items-center justify-center text-2xl font-extrabold shadow-lg shadow-blue-500/30">
                    {{ substr($agent->name, 0, 1) }}
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs bg-emerald-500/20 text-emerald-300 px-2 py-0.5 rounded-full font-semibold border border-emerald-500/30">
                            {{ ucfirst($agent->status) }}
                        </span>
                        <span class="text-xs text-blue-300 font-mono">{{ $agent->agent_code }}</span>
                    </div>
                    <h2 class="text-2xl font-extrabold text-white mt-1">{{ $agent->name }}</h2>
                    <div class="text-xs text-slate-300 mt-1 flex flex-wrap items-center gap-3">
                        <span><i class="fa-solid fa-map-pin text-blue-400 mr-1"></i>{{ $agent->territory }}</span>
                        <span>&bull;</span>
                        <span><i class="fa-solid fa-motorcycle text-slate-400 mr-1"></i>{{ $agent->vehicle_type ?: 'Motorbike' }}</span>
                        <span>&bull;</span>
                        <a href="tel:{{ $agent->phone }}" class="text-blue-300 hover:underline"><i class="fa-solid fa-phone mr-1"></i>{{ $agent->phone }}</a>
                        <span>&bull;</span>
                        <span>Onboarded: {{ $agent->onboarded_date ? $agent->onboarded_date->format('M d, Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                @if(Auth::user()->canManageAgents())
                    <a href="{{ route('agents.edit', $agent) }}" 
                       class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold border border-white/20 transition-all">
                        <i class="fa-solid fa-pen-to-square mr-1.5"></i>Edit Profile
                    </a>
                @endif
                <a href="{{ route('activities.daily', ['date' => $today]) }}" 
                   class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-md shadow-blue-600/30 transition-all">
                    <i class="fa-solid fa-calendar-day mr-1.5"></i>Daily Tracker
                </a>
            </div>

        </div>
    </div>

    <!-- 4 Performance Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Today's Visits -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Today's Visits</span>
            <div class="flex items-baseline space-x-2 mt-1">
                <span class="text-2xl font-extrabold text-slate-900">{{ $todayVisitsCount }}</span>
                <span class="text-xs text-slate-400">/ {{ $agent->daily_visit_target }} target</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2 mt-2">
                @php
                    $vPct = $agent->daily_visit_target > 0 ? min(100, round(($todayVisitsCount / $agent->daily_visit_target) * 100)) : 0;
                @endphp
                <div class="h-2 rounded-full {{ $vPct >= 100 ? 'bg-emerald-500' : 'bg-blue-600' }}" style="width: {{ $vPct }}%"></div>
            </div>
        </div>

        <!-- Monthly Onboards -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-purple-600 block">Month's Onboards</span>
            <div class="flex items-baseline space-x-2 mt-1">
                <span class="text-2xl font-extrabold text-purple-900">{{ $monthOnboardCount }}</span>
                <span class="text-xs text-slate-400">/ {{ $agent->monthly_onboard_target }} stores</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2 mt-2">
                <div class="h-2 rounded-full bg-purple-600" style="width: {{ $onboardPercent }}%"></div>
            </div>
        </div>

        <!-- Month Revenue -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 block">Month's Revenue</span>
            <div class="flex items-baseline space-x-2 mt-1">
                <span class="text-2xl font-extrabold text-emerald-900">₦{{ number_format($monthRevenue / 1000000, 1) }}M</span>
                <span class="text-xs text-slate-400">{{ $revenuePercent }}% quota</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2 mt-2">
                <div class="h-2 rounded-full bg-emerald-500" style="width: {{ $revenuePercent }}%"></div>
            </div>
        </div>

        <!-- All-Time Customers -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Clients Onboarded</span>
            <div class="flex items-baseline space-x-2 mt-1">
                <span class="text-2xl font-extrabold text-slate-900">{{ $customers->total() }}</span>
                <span class="text-xs text-slate-400">stores total</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">In {{ $agent->territory }}</p>
        </div>

    </div>

    <!-- Section 1: Customers Onboarded by this Field Rep (The User's specific prompt requirement) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center space-x-2">
                    <i class="fa-solid fa-shop text-purple-600"></i>
                    <span>Customers Onboarded by {{ $agent->name }}</span>
                </h3>
                <p class="text-xs text-slate-500">Retailers, electrical contractors, and lighting showrooms registered by this executive.</p>
            </div>
            <a href="{{ route('customers.create') }}" class="inline-flex items-center space-x-1.5 text-xs font-bold px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fa-solid fa-plus"></i>
                <span>Onboard Another Customer</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                        <th class="py-3 px-4">Customer / Store</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Contact Person</th>
                        <th class="py-3 px-4">Address / Territory</th>
                        <th class="py-3 px-4 text-center">Day Onboarded</th>
                        <th class="py-3 px-4 text-right">Credit Limit</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customers as $c)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-bold text-slate-900">
                                <a href="{{ route('customers.show', $c) }}" class="hover:text-blue-600">
                                    {{ $c->company_name }}
                                </a>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $c->customer_code }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                    {{ $c->getCategoryLabelAttribute() }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-800">{{ $c->contact_person }}</div>
                                <div class="text-[10px] text-slate-400">{{ $c->phone }}</div>
                            </td>
                            <td class="py-3 px-4 max-w-xs truncate text-slate-600">
                                {{ $c->address }}, {{ $c->city }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-blue-700 whitespace-nowrap">
                                {{ $c->onboarded_date->format('M d, Y') }}
                            </td>
                            <td class="py-3 px-4 text-right font-extrabold text-slate-900">
                                ₦{{ number_format($c->credit_limit) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('customers.show', $c) }}" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px]">
                                    Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-slate-400">
                                No customers onboarded by this field executive yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

    <!-- Section 2: Recent Field Activities by this Agent -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
        <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center space-x-2">
            <i class="fa-solid fa-calendar-check text-blue-600"></i>
            <span>Activity History & Visits Logged by {{ $agent->name }}</span>
        </h3>

        <div class="space-y-3">
            @forelse($activities as $act)
                <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/70 hover:bg-slate-50 transition-colors text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold shrink-0 mt-0.5">
                            <i class="fa-solid fa-location-dot text-xs"></i>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="font-extrabold text-slate-900">
                                    {{ $act->customer?->company_name ?: 'General Prospecting' }}
                                </span>
                                <span class="text-slate-400">&bull;</span>
                                <span class="text-slate-500 font-medium">{{ $act->getActivityTypeLabelAttribute() }}</span>
                            </div>
                            <p class="text-slate-700 mt-0.5">{{ $act->purpose }}</p>
                            @if($act->location_address)
                                <div class="text-[11px] text-slate-400 mt-1">
                                    <i class="fa-solid fa-map-pin mr-1"></i>{{ $act->location_address }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold
                            {{ $act->outcome === 'order_taken' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 
                              ($act->outcome === 'payment_received' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-700') }}">
                            {{ $act->getOutcomeLabelAttribute() }}
                        </span>
                        <div class="text-[11px] text-slate-400 mt-1 font-mono">
                            {{ $act->activity_date->format('M d, Y') }} • {{ substr($act->check_in_time, 0, 5) }}
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center py-6 text-slate-400 text-xs">No activity logs recorded yet.</p>
            @endforelse
        </div>

        @if($activities->hasPages())
            <div class="mt-4 pt-4 border-t border-slate-100">
                {{ $activities->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

