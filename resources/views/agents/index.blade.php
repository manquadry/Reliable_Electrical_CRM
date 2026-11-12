@extends('layouts.app')

@section('title', 'Field Executives Directory')
@section('header_title', 'Field Executives Management')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Field Sales Executives</h2>
            <p class="text-xs text-slate-500">Manage field representatives, assigned sales territories, targets, and onboarding records.</p>
        </div>

        <a href="{{ route('agents.create') }}" 
           class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-600/30 transition-all">
            <i class="fa-solid fa-user-plus"></i>
            <span>Onboard New Field Executive</span>
        </a>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-4">
        <form action="{{ route('agents.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by agent name, email, code or territory..."
                       class="w-full px-3.5 py-2 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <select name="territory" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">All Territories</option>
                    @foreach($territories as $t)
                        <option value="{{ $t }}" {{ request('territory') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <select name="status" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <button type="submit" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Agents Grid Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
        @forelse($agents as $agent)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs hover:border-blue-400 hover:shadow-md transition-all p-5 flex flex-col justify-between">
                
                <div>
                    <!-- Top Strip -->
                    <div class="flex items-start justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold text-base shadow-sm">
                                {{ substr($agent->name, 0, 1) }}
                            </div>
                            <div>
                                <a href="{{ route('agents.show', $agent) }}" class="font-extrabold text-slate-900 hover:text-blue-600 text-sm">
                                    {{ $agent->name }}
                                </a>
                                <div class="text-[11px] text-slate-400 font-mono">
                                    {{ $agent->agent_code }} &bull; {{ $agent->email }}
                                </div>
                            </div>
                        </div>

                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                            {{ $agent->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                            {{ $agent->status }}
                        </span>
                    </div>

                    <!-- Territory & Contact -->
                    <div class="py-3 space-y-1.5 text-xs text-slate-600">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-map-location-dot text-blue-600 w-4 text-center"></i>
                            <span class="font-semibold text-slate-800">{{ $agent->territory }}</span>
                        </div>
                        <div class="flex items-center space-x-2 text-[11px] text-slate-500">
                            <i class="fa-solid fa-motorcycle text-slate-400 w-4 text-center"></i>
                            <span>{{ $agent->vehicle_type ?: 'Motorbike' }}</span>
                            <span>&bull;</span>
                            <i class="fa-solid fa-phone text-slate-400 w-3 text-center"></i>
                            <a href="tel:{{ $agent->phone }}" class="text-blue-600 hover:underline">{{ $agent->phone }}</a>
                        </div>
                        <div class="flex items-center space-x-2 text-[11px] text-slate-400">
                            <i class="fa-regular fa-calendar-check text-purple-400 w-4 text-center"></i>
                            <span>Onboarded: {{ $agent->onboarded_date ? $agent->onboarded_date->format('M d, Y') : 'Pre-existing' }}</span>
                        </div>
                    </div>

                    <!-- Targets Strip -->
                    <div class="grid grid-cols-3 gap-2 py-3 border-y border-slate-100 text-center text-xs">
                        <div class="bg-slate-50 p-2 rounded-xl">
                            <span class="text-[10px] text-slate-400 block uppercase font-bold">Daily Visits</span>
                            <span class="font-extrabold text-slate-900">{{ $agent->daily_visit_target }} / day</span>
                        </div>
                        <div class="bg-purple-50 p-2 rounded-xl">
                            <span class="text-[10px] text-purple-600 block uppercase font-bold">Mo. Onboards</span>
                            <span class="font-extrabold text-purple-900">{{ $agent->monthly_onboard_target }} stores</span>
                        </div>
                        <div class="bg-emerald-50 p-2 rounded-xl">
                            <span class="text-[10px] text-emerald-600 block uppercase font-bold">Mo. Target</span>
                            <span class="font-extrabold text-emerald-900">₦{{ number_format($agent->monthly_revenue_target / 1000000, 1) }}M</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Stats & Buttons -->
                <div class="pt-3 mt-2 flex items-center justify-between text-xs">
                    <div class="text-[11px] text-slate-500">
                        <strong class="text-slate-800">{{ $agent->onboarded_customers_count }}</strong> customers &bull; <strong class="text-slate-800">{{ $agent->activities_count }}</strong> visits
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('agents.edit', $agent) }}" class="p-1.5 text-slate-400 hover:text-slate-700" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <a href="{{ route('agents.show', $agent) }}" 
                           class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold transition-colors">
                            View Profile &rarr;
                        </a>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-2 bg-white rounded-2xl p-8 text-center text-slate-400">
                No field agents found.
            </div>
        @endforelse
    </div>

    @if($agents->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-200">
            {{ $agents->links() }}
        </div>
    @endif

</div>
@endsection

