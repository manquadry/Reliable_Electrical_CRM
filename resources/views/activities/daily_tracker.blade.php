@extends('layouts.app')

@section('title', 'Daily Field Activity Tracker')
@section('header_title', 'Admin Date-by-Date Field Activity Tracker')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Top Date Selection & Filter Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            <div>
                <div class="flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-blue-600 mb-1">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>Field Operations Date Audit</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900">
                    {{ $carbonDate->format('l, F j, Y') }}
                    @if($carbonDate->isToday())
                        <span class="ml-2 text-xs font-semibold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full">Today</span>
                    @elseif($carbonDate->isYesterday())
                        <span class="ml-2 text-xs font-semibold bg-slate-100 text-slate-700 px-2 py-0.5 rounded-full">Yesterday</span>
                    @endif
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Select any date to see exact field logs, visits, new customer onboardings, orders and collections for each field agent.
                </p>
            </div>

            <!-- Date Picker & Quick Selectors -->
            <div class="flex flex-wrap items-center gap-2">
                
                <!-- Previous Day Arrow -->
                <a href="{{ route('activities.daily', ['date' => $carbonDate->copy()->subDay()->toDateString()]) }}" 
                   title="Previous Day"
                   class="p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold transition-colors">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>

                <!-- Interactive Date Input Form -->
                <form action="{{ route('activities.daily') }}" method="GET" class="flex items-center">
                    <div class="relative">
                        <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()"
                               class="text-xs font-semibold px-3 py-2 border border-slate-300 rounded-xl bg-white text-slate-800 shadow-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </form>

                <!-- Next Day Arrow -->
                <a href="{{ route('activities.daily', ['date' => $carbonDate->copy()->addDay()->toDateString()]) }}" 
                   title="Next Day"
                   class="p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold transition-colors">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>

                <!-- Quick Date Pills -->
                <div class="flex items-center space-x-1 pl-2 border-l border-slate-200">
                    <a href="{{ route('activities.daily', ['date' => Carbon\Carbon::today()->toDateString()]) }}"
                       class="text-xs px-2.5 py-1.5 rounded-lg font-medium transition-colors {{ $selectedDate === Carbon\Carbon::today()->toDateString() ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Today
                    </a>
                    <a href="{{ route('activities.daily', ['date' => Carbon\Carbon::yesterday()->toDateString()]) }}"
                       class="text-xs px-2.5 py-1.5 rounded-lg font-medium transition-colors {{ $selectedDate === Carbon\Carbon::yesterday()->toDateString() ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Yesterday
                    </a>
                    <a href="{{ route('activities.daily', ['date' => Carbon\Carbon::today()->subDays(2)->toDateString()]) }}"
                       class="text-xs px-2.5 py-1.5 rounded-lg font-medium transition-colors {{ $selectedDate === Carbon\Carbon::today()->subDays(2)->toDateString() ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        2d Ago
                    </a>
                </div>

            </div>

        </div>

        <!-- 5 Summary Metrics for the Selected Date -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mt-5 pt-5 border-t border-slate-100">
            <div class="bg-slate-50 rounded-xl p-3 border border-slate-200/70">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Active Agents</div>
                <div class="text-xl font-extrabold text-slate-900 mt-0.5">
                    {{ $activeAgentCount }} <span class="text-xs text-slate-400 font-normal">/ {{ $allAgents->count() }}</span>
                </div>
                <div class="text-[10px] text-slate-500 mt-0.5">Checked in on this date</div>
            </div>

            <div class="bg-blue-50/60 rounded-xl p-3 border border-blue-200/60">
                <div class="text-[11px] font-bold uppercase tracking-wider text-blue-600">Total Field Visits</div>
                <div class="text-xl font-extrabold text-blue-900 mt-0.5">{{ $totalVisits }}</div>
                <div class="text-[10px] text-blue-600/80 mt-0.5">Store visits & meetings</div>
            </div>

            <div class="bg-purple-50/60 rounded-xl p-3 border border-purple-200/60">
                <div class="text-[11px] font-bold uppercase tracking-wider text-purple-600">New Onboards</div>
                <div class="text-xl font-extrabold text-purple-900 mt-0.5">{{ $totalNewCustomers }}</div>
                <div class="text-[10px] text-purple-600/80 mt-0.5">Customers onboarded today</div>
            </div>

            <div class="bg-emerald-50/60 rounded-xl p-3 border border-emerald-200/60">
                <div class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Orders Booked</div>
                <div class="text-xl font-extrabold text-emerald-900 mt-0.5">₦{{ number_format($totalOrdersAmount) }}</div>
                <div class="text-[10px] text-emerald-600/80 mt-0.5">Sales value secured</div>
            </div>

            <div class="bg-amber-50/60 rounded-xl p-3 border border-amber-200/60">
                <div class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Collections</div>
                <div class="text-xl font-extrabold text-amber-900 mt-0.5">₦{{ number_format($totalCollectionsAmount) }}</div>
                <div class="text-[10px] text-amber-600/80 mt-0.5">Payments retrieved</div>
            </div>
        </div>

    </div>

    <!-- Active Field Agents Section -->
    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900 flex items-center space-x-2">
                <span>Field Executives Active on {{ $carbonDate->format('M d, Y') }}</span>
                <span class="text-xs bg-blue-100 text-blue-800 font-bold px-2.5 py-0.5 rounded-full">
                    {{ $activeAgentsList->count() }} active
                </span>
            </h3>
            <span class="text-xs text-slate-400">Detailed chronological audit of each agent's movement and results</span>
        </div>

        @forelse($activeAgentsList as $summary)
            @php
                $agent = $summary['agent'];
                $acts = $summary['activities'];
                $onboards = $summary['onboarded_customers'];
                $orders = $summary['orders'];
                $payments = $summary['payments'];
            @endphp
            
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden" x-data="{ expanded: true }">
                
                <!-- Agent Card Header Strip -->
                <div class="p-4 sm:p-5 bg-gradient-to-r from-slate-900 to-slate-800 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center font-extrabold text-base shadow-md">
                            {{ substr($agent->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="font-extrabold text-white text-base">{{ $agent->name }}</span>
                                <span class="text-xs bg-slate-700 text-blue-300 font-mono px-2 py-0.5 rounded">
                                    {{ $agent->agent_code }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-300 mt-0.5 flex flex-wrap items-center gap-2">
                                <span><i class="fa-solid fa-map-pin text-blue-400 mr-1"></i>{{ $agent->territory }}</span>
                                <span>&bull;</span>
                                <span><i class="fa-solid fa-motorcycle text-slate-400 mr-1"></i>{{ $agent->vehicle_type ?: 'Motorbike' }}</span>
                                <span>&bull;</span>
                                <a href="tel:{{ $agent->phone }}" class="text-blue-300 hover:underline">{{ $agent->phone }}</a>
                            </div>
                        </div>
                    </div>

                    <!-- Agent Summary Indicators & Toggle -->
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <div class="text-xs font-bold text-white">
                                {{ $summary['visits_count'] }} of {{ $summary['target'] }} visits
                            </div>
                            <div class="text-[11px] text-slate-300 mt-0.5">
                                @if($summary['first_checkin'])
                                    Field Hours: {{ substr($summary['first_checkin'], 0, 5) }} - {{ substr($summary['last_checkin'], 0, 5) }}
                                @endif
                            </div>
                        </div>

                        <!-- Progress Bar Mini -->
                        <div class="w-20 hidden md:block">
                            <div class="w-full bg-slate-700 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full {{ $summary['compliance_percent'] >= 100 ? 'bg-emerald-400' : 'bg-blue-400' }}"
                                     style="width: {{ $summary['compliance_percent'] }}%"></div>
                            </div>
                            <div class="text-[10px] text-right text-slate-400 mt-0.5">{{ $summary['compliance_percent'] }}% target</div>
                        </div>

                        <!-- Expand / Collapse Button -->
                        <button @click="expanded = !expanded" class="p-2 rounded-lg bg-slate-700/60 hover:bg-slate-700 text-slate-300 transition-colors">
                            <i class="fa-solid" :class="expanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        </button>
                    </div>

                </div>

                <!-- Expanded Details Container -->
                <div x-show="expanded" x-collapse class="divide-y divide-slate-100">
                    
                    <!-- Day Highlights Badge Row -->
                    <div class="px-5 py-3 bg-slate-50 flex flex-wrap items-center gap-3 text-xs">
                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-lg bg-blue-100/70 text-blue-800 font-medium">
                            <i class="fa-solid fa-location-dot text-blue-600"></i>
                            <span>{{ $summary['visits_count'] }} Visits Logged</span>
                        </span>

                        @if($onboards->count() > 0)
                            <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-lg bg-purple-100 text-purple-800 font-bold">
                                <i class="fa-solid fa-user-plus text-purple-600"></i>
                                <span>{{ $onboards->count() }} New Customer Onboarded Today!</span>
                            </span>
                        @endif

                        @if($orders->count() > 0)
                            <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-bold">
                                <i class="fa-solid fa-file-invoice text-emerald-600"></i>
                                <span>{{ $orders->count() }} Orders Booked (₦{{ number_format($summary['orders_total']) }})</span>
                            </span>
                        @endif

                        @if($payments->count() > 0)
                            <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-lg bg-amber-100 text-amber-800 font-bold">
                                <i class="fa-solid fa-money-bill-wave text-amber-600"></i>
                                <span>₦{{ number_format($summary['payments_total']) }} Collected</span>
                            </span>
                        @endif
                    </div>

                    <!-- Customers Onboarded Today by this Agent (if any) -->
                    @if($onboards->count() > 0)
                        <div class="p-5 bg-purple-50/30">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-purple-900 mb-3 flex items-center space-x-2">
                                <i class="fa-solid fa-building-circle-check text-purple-600"></i>
                                <span>Customers Onboarded by {{ $agent->name }} on this Date</span>
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($onboards as $onboardCust)
                                    <div class="p-3.5 rounded-xl bg-white border border-purple-200 shadow-xs flex items-start justify-between text-xs">
                                        <div>
                                            <a href="{{ route('customers.show', $onboardCust) }}" class="font-bold text-slate-900 hover:text-blue-600 text-sm">
                                                {{ $onboardCust->company_name }}
                                            </a>
                                            <div class="text-slate-500 mt-0.5">
                                                Contact: <strong class="text-slate-700">{{ $onboardCust->contact_person }}</strong> &bull; {{ $onboardCust->phone }}
                                            </div>
                                            <div class="text-[11px] text-slate-400 mt-1">
                                                <i class="fa-solid fa-map-pin mr-1"></i>{{ $onboardCust->address }}, {{ $onboardCust->city }}
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-100 text-purple-800">
                                                {{ $onboardCust->getCategoryLabelAttribute() }}
                                            </span>
                                            <div class="text-[10px] text-emerald-600 font-semibold mt-1">
                                                Credit: ₦{{ number_format($onboardCust->credit_limit) }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Chronological Visit & Activity Timeline for this Agent -->
                    <div class="p-5 space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                            Field Activity Timeline
                        </h4>

                        <div class="relative pl-6 space-y-6 before:content-[''] before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                            @foreach($acts as $act)
                                <div class="relative group">
                                    <!-- Timeline Node Circle -->
                                    <div class="absolute -left-6 top-1 w-5 h-5 rounded-full border-2 border-white flex items-center justify-center text-[9px] text-white shadow-sm
                                        {{ $act->outcome === 'order_taken' ? 'bg-emerald-500' : ($act->outcome === 'payment_received' ? 'bg-amber-500' : 'bg-blue-600') }}">
                                        <i class="fa-solid {{ $act->outcome === 'order_taken' ? 'fa-check' : ($act->outcome === 'payment_received' ? 'fa-dollar-sign' : 'fa-circle') }}"></i>
                                    </div>

                                    <!-- Activity Card -->
                                    <div class="p-4 rounded-xl border border-slate-200 bg-white shadow-2xs hover:shadow-xs transition-shadow">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-slate-100">
                                            
                                                <div class="flex items-center space-x-2">
                                                    <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-slate-900 text-white">
                                                        {{ substr($act->check_in_time, 0, 5) }}
                                                        @if($act->check_out_time)
                                                            - {{ substr($act->check_out_time, 0, 5) }}
                                                        @endif
                                                    </span>
                                                    <span class="font-extrabold text-sm text-slate-900">
                                                        @if($act->customer)
                                                            <a href="{{ route('customers.show', $act->customer) }}" class="hover:text-blue-600">
                                                                {{ $act->customer->company_name }}
                                                            </a>
                                                        @else
                                                            <span class="text-slate-700">General Route / Prospecting</span>
                                                        @endif
                                                    </span>
                                                </div>

                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs px-2 py-0.5 rounded font-medium bg-slate-100 text-slate-700">
                                                    {{ $act->getActivityTypeLabelAttribute() }}
                                                </span>
                                                <span class="text-xs px-2 py-0.5 rounded font-bold
                                                    {{ $act->outcome === 'order_taken' ? 'bg-emerald-100 text-emerald-800' : 
                                                      ($act->outcome === 'payment_received' ? 'bg-amber-100 text-amber-800' : 'bg-blue-50 text-blue-700') }}">
                                                    {{ $act->getOutcomeLabelAttribute() }}
                                                </span>
                                            </div>

                                        </div>

                                        <!-- Purpose and Notes -->
                                        <div class="mt-3 text-xs space-y-2">
                                            <div>
                                                <span class="font-semibold text-slate-500 uppercase text-[10px] tracking-wider block">Meeting Purpose:</span>
                                                <p class="text-slate-800 font-medium">{{ $act->purpose }}</p>
                                            </div>

                                            @if($act->notes)
                                                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 text-slate-700">
                                                    <span class="font-semibold text-slate-500 text-[10px] uppercase tracking-wider block mb-0.5">Discussion Notes & Feedback:</span>
                                                    <p class="italic">{{ $act->notes }}</p>
                                                </div>
                                            @endif

                                            <!-- Location & Follow-up Row -->
                                            <div class="flex flex-wrap items-center justify-between gap-2 pt-2 text-[11px] text-slate-400">
                                                <div class="flex items-center space-x-1.5">
                                                    <i class="fa-solid fa-location-dot text-slate-400"></i>
                                                    <span>{{ $act->location_address ?: ($act->customer?->address ?: 'Address not captured') }}</span>
                                                    @if($act->latitude && $act->longitude)
                                                        <a href="https://maps.google.com/?q={{ $act->latitude }},{{ $act->longitude }}" target="_blank" class="text-blue-600 hover:underline ml-1">
                                                            (Map Pin)
                                                        </a>
                                                    @endif
                                                </div>

                                                @if($act->next_followup_date)
                                                    <div class="text-amber-700 font-medium">
                                                        <i class="fa-regular fa-clock mr-1"></i>Next Follow-Up: {{ $act->next_followup_date->format('M d, Y') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                    <i class="fa-regular fa-calendar-xmark"></i>
                </div>
                <h4 class="text-base font-bold text-slate-900">No Field Activities Logged on {{ $carbonDate->format('M d, Y') }}</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    None of the field agents have submitted check-ins or activity reports for this date yet.
                </p>
                <div class="mt-4 flex items-center justify-center space-x-2">
                    <a href="{{ route('activities.daily', ['date' => Carbon\Carbon::today()->toDateString()]) }}" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700">
                        Jump to Today
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Inactive Agents Notice (For Admin Compliance) -->
    @if($inactiveAgentsList->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
            <div class="flex items-center space-x-2 pb-3 border-b border-slate-100 mb-4">
                <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                <h4 class="text-sm font-bold text-slate-900">
                    Agents With No Activity Recorded on {{ $carbonDate->format('M d, Y') }} ({{ $inactiveAgentsList->count() }})
                </h4>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($inactiveAgentsList as $inact)
                    @php $iAgent = $inact['agent']; @endphp
                    <div class="p-3 rounded-xl border border-slate-200/80 bg-slate-50/50 flex items-center justify-between text-xs">
                        <div>
                            <div class="font-bold text-slate-900">{{ $iAgent->name }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">{{ $iAgent->agent_code }} &bull; {{ $iAgent->territory }}</div>
                        </div>
                        <span class="text-[10px] bg-slate-200 text-slate-600 font-semibold px-2 py-0.5 rounded">
                            0 Visits Logged
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection

