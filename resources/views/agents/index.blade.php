@extends('layouts.app')

@section('title', 'Field Executives Directory')
@section('header_title', 'Field Executives Management')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-brand-600 mb-1">
                <i class="fa-solid fa-users-gear"></i>
                <span>Human Capital & Territory Deployment</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Field Sales Executives</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage field representatives, assigned sales territories, targets, and onboarding records.</p>
        </div>

        @if(Auth::user()->canManageAgents())
            <a href="{{ route('agents.create') }}" 
               class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold shadow-md shadow-brand-600/30 hover:shadow-brand-600/50 transition-all shrink-0">
                <i class="fa-solid fa-user-plus"></i>
                <span>Onboard New Field Executive</span>
            </a>
        @endif
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-card p-4 sm:p-5">
        <form action="{{ route('agents.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div class="sm:col-span-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by agent name, email, code or territory..."
                           class="w-full pl-9 pr-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-brand-500 focus:outline-none transition-all">
                </div>
            </div>

            <div>
                <select name="territory" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-brand-500 focus:outline-none transition-all">
                    <option value="">All Territories</option>
                    @foreach($territories as $t)
                        <option value="{{ $t }}" {{ request('territory') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <select name="status" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-brand-500 focus:outline-none transition-all">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <button type="submit" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all shadow-2xs">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'territory', 'status']))
                    <a href="{{ route('agents.index') }}" class="p-2.5 border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl text-xs font-semibold" title="Reset">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Agents Grid Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($agents as $agent)
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-card hover:shadow-card-hover hover:border-brand-400 transition-all p-5 sm:p-6 flex flex-col justify-between group">
                
                <div>
                    <!-- Top Strip -->
                    <div class="flex items-start justify-between pb-3.5 border-b border-slate-100">
                        <div class="flex items-center space-x-3.5">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-600 text-white flex items-center justify-center font-bold text-base shadow-sm ring-1 ring-white/20 shrink-0">
                                {{ substr($agent->name, 0, 1) }}
                            </div>
                            <div>
                                <a href="{{ route('agents.show', $agent) }}" class="font-extrabold text-slate-900 hover:text-brand-600 text-base transition-colors">
                                    {{ $agent->name }}
                                </a>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5 flex items-center space-x-1.5">
                                    <span class="bg-slate-100 text-slate-700 px-1.5 py-0.2 rounded font-semibold">{{ $agent->agent_code }}</span>
                                    <span>&bull;</span>
                                    <span class="truncate max-w-[160px]">{{ $agent->email }}</span>
                                </div>
                            </div>
                        </div>

                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                            {{ $agent->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                            {{ $agent->status }}
                        </span>
                    </div>

                    <!-- Territory & Contact -->
                    <div class="py-3.5 space-y-2 text-xs text-slate-600">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-map-location-dot text-brand-500 w-4 text-center"></i>
                            <span class="font-bold text-slate-800">{{ $agent->territory }}</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-[11px] text-slate-500">
                            <span class="flex items-center space-x-1">
                                <i class="fa-solid fa-motorcycle text-slate-400"></i>
                                <span>{{ $agent->vehicle_type ?: 'Motorbike' }}</span>
                            </span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="flex items-center space-x-1">
                                <i class="fa-solid fa-phone text-slate-400"></i>
                                <a href="tel:{{ $agent->phone }}" class="text-brand-600 font-mono hover:underline">{{ $agent->phone }}</a>
                            </span>
                        </div>
                        <div class="flex items-center space-x-2 text-[11px] text-slate-400">
                            <i class="fa-regular fa-calendar-check text-purple-400 w-4 text-center"></i>
                            <span>Onboarded: {{ $agent->onboarded_date ? $agent->onboarded_date->format('M d, Y') : 'Pre-existing' }}</span>
                        </div>
                    </div>

                    <!-- Targets Strip -->
                    <div class="grid grid-cols-3 gap-2.5 py-3.5 border-y border-slate-100 text-center text-xs">
                        <div class="bg-slate-50/80 p-2.5 rounded-xl border border-slate-100">
                            <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider">Daily Visits</span>
                            <span class="font-extrabold text-slate-900 font-mono text-sm mt-0.5 block">{{ $agent->daily_visit_target }} / day</span>
                        </div>
                        <div class="bg-purple-50/60 p-2.5 rounded-xl border border-purple-100/60">
                            <span class="text-[10px] text-purple-600 block uppercase font-bold tracking-wider">Mo. Onboards</span>
                            <span class="font-extrabold text-purple-900 font-mono text-sm mt-0.5 block">{{ $agent->monthly_onboard_target }} stores</span>
                        </div>
                        <div class="bg-emerald-50/60 p-2.5 rounded-xl border border-emerald-100/60">
                            <span class="text-[10px] text-emerald-600 block uppercase font-bold tracking-wider">Mo. Target</span>
                            <span class="font-extrabold text-emerald-900 font-mono text-sm mt-0.5 block">₦{{ number_format($agent->monthly_revenue_target / 1000000, 1) }}M</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Stats & Buttons -->
                <div class="pt-3.5 mt-2 flex items-center justify-between text-xs">
                    <div class="text-[11px] text-slate-500 font-medium">
                        <strong class="text-slate-800 font-mono">{{ $agent->onboarded_customers_count }}</strong> clients &bull; <strong class="text-slate-800 font-mono">{{ $agent->activities_count }}</strong> visits
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('agents.edit', $agent) }}" class="p-2 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition-colors" title="Edit Profile">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <a href="{{ route('agents.show', $agent) }}" 
                           class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-xl bg-brand-50 text-brand-700 hover:bg-brand-600 hover:text-white font-bold transition-all shadow-2xs">
                            <span>Profile</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-2 bg-white rounded-2xl p-10 text-center text-slate-400 font-medium border border-slate-200/80 shadow-card">
                No field agents found matching criteria.
            </div>
        @endforelse
    </div>

    @if($agents->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-card">
            {{ $agents->links() }}
        </div>
    @endif

</div>
@endsection
