@extends('layouts.app')

@section('title', 'Customers Directory')
@section('header_title', 'Customers & Electrical Retailers')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Top Action Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-brand-600 mb-1">
                <i class="fa-solid fa-shop"></i>
                <span>{{ Auth::user()->isFieldAgent() ? 'My Assigned Territory' : 'Lagos Commercial Operations' }}</span>
            </div>
            @if(Auth::user()->isFieldAgent())
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">My Customer Directory</h1>
                <p class="text-xs text-slate-500 mt-0.5">Showing stores and retailers managed by {{ Auth::user()->name }} ({{ Auth::user()->agent_code }} &bull; {{ Auth::user()->territory }}).</p>
            @else
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">All Onboarded Stores & Retailers</h1>
                <p class="text-xs text-slate-500 mt-0.5">Central directory across all field offices, territories, and central HQ pool.</p>
            @endif
        </div>

        <div class="flex items-center space-x-2 shrink-0">
            @if(Auth::user()->isManagerOrAdmin())
                <a href="{{ route('customers.import_form') }}" 
                   class="inline-flex items-center space-x-1.5 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-md transition-all">
                    <i class="fa-solid fa-file-excel text-emerald-400"></i>
                    <span>Import Excel / CSV</span>
                </a>
            @endif

            <a href="{{ route('customers.create') }}" 
               class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold shadow-md shadow-brand-600/30 hover:shadow-brand-600/50 transition-all">
                <i class="fa-solid fa-plus"></i>
                <span>Onboard New Customer</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-card p-4 sm:p-5">
        <form action="{{ route('customers.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 {{ Auth::user()->isLeadOrAdmin() ? 'lg:grid-cols-5' : 'lg:grid-cols-4' }} gap-3 text-xs">
            <div class="{{ Auth::user()->isLeadOrAdmin() ? 'lg:col-span-2' : 'lg:col-span-2' }}">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search company, contact person, phone or address..."
                           class="w-full pl-9 pr-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-brand-500 focus:outline-none transition-all">
                </div>
            </div>

            <div>
                <select name="category" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-brand-500 focus:outline-none transition-all">
                    <option value="">All Categories</option>
                    <option value="retailer" {{ request('category') == 'retailer' ? 'selected' : '' }}>Retail Shop</option>
                    <option value="wholesaler" {{ request('category') == 'wholesaler' ? 'selected' : '' }}>Wholesale Distributor</option>
                    <option value="electrical_contractor" {{ request('category') == 'electrical_contractor' ? 'selected' : '' }}>Electrical Contractor</option>
                    <option value="lighting_showroom" {{ request('category') == 'lighting_showroom' ? 'selected' : '' }}>Lighting Showroom</option>
                    <option value="real_estate_developer" {{ request('category') == 'real_estate_developer' ? 'selected' : '' }}>Estate Developer</option>
                    <option value="walk_in" {{ request('category') == 'walk_in' ? 'selected' : '' }}>Walk-in / Counter Sales</option>
                </select>
            </div>

            @if(Auth::user()->isLeadOrAdmin())
            <div>
                <select name="agent_id" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-brand-500 focus:outline-none transition-all">
                    <option value="">Assigned Field Rep / Office (All)</option>
                    @if(isset($unassignedCount) && $unassignedCount > 0)
                        <option value="unassigned" {{ request('agent_id') === 'unassigned' ? 'selected' : '' }}>
                            ⚠️ Unassigned (HQ Pool) ({{ $unassignedCount }})
                        </option>
                    @endif
                    @foreach($agents as $ag)
                        <option value="{{ $ag->id }}" {{ request('agent_id') == $ag->id ? 'selected' : '' }}>
                            {{ $ag->name }} &bull; {{ $ag->agent_code }} ({{ $ag->territory }})
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="flex items-center space-x-2">
                <input type="date" name="onboarded_date" value="{{ request('onboarded_date') }}" title="Filter by Onboarded Day"
                       class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs focus:ring-2 focus:ring-brand-500 focus:outline-none">
                <button type="submit" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all shadow-2xs">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'category', 'agent_id', 'onboarded_date']))
                    <a href="{{ route('customers.index') }}" class="p-2.5 border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl text-xs font-semibold" title="Reset">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-100/70 text-slate-500 uppercase font-bold tracking-wider text-[11px] border-b border-slate-200">
                        <th class="py-3 px-4">Company / Store Name</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Contact Person</th>
                        <th class="py-3 px-4">Address / Territory</th>
                        <th class="py-3 px-4">Onboarded By Rep</th>
                        <th class="py-3 px-4 text-center">Day Onboarded</th>
                        <th class="py-3 px-4 text-right">Credit Limit</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customers as $c)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                <a href="{{ route('customers.show', $c) }}" class="text-brand-700 hover:underline text-sm font-extrabold block">
                                    {{ $c->company_name }}
                                </a>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $c->customer_code }}</span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($c->category === 'walk_in')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-200 inline-flex items-center space-x-1">
                                        <i class="fa-solid fa-person-walking text-[9px]"></i>
                                        <span>Walk-in Counter</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        {{ $c->getCategoryLabelAttribute() }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800">{{ $c->contact_person }}</div>
                                <div class="text-[11px] text-slate-500">
                                    <a href="tel:{{ $c->phone }}" class="text-brand-600 hover:underline font-mono">{{ $c->phone }}</a>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <div class="truncate text-slate-700 font-medium">{{ $c->address }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5 flex items-center space-x-1">
                                    <i class="fa-solid fa-map-pin text-rose-500 text-[9px]"></i>
                                    <span>{{ $c->territory }} &bull; {{ $c->city }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($c->onboardedBy)
                                    <div>
                                        <a href="{{ route('agents.show', $c->onboardedBy) }}" class="font-bold text-slate-800 hover:text-brand-600 transition-colors">
                                            {{ $c->onboardedBy->name }}
                                        </a>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $c->onboardedBy->agent_code }}</div>
                                    </div>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 inline-flex items-center space-x-1">
                                        <i class="fa-solid fa-triangle-exclamation text-[9px]"></i>
                                        <span>Unassigned</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-800 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-md bg-slate-100 font-mono text-[11px]">
                                    {{ $c->onboarded_date->format('M d, Y') }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="font-mono font-extrabold text-slate-900 text-xs">₦{{ number_format($c->credit_limit) }}</div>
                                <div class="text-[10px] text-slate-400 uppercase font-semibold">{{ str_replace('_', ' ', $c->payment_terms) }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <a href="{{ route('activities.create_checkin', ['customer_id' => $c->id]) }}" 
                                       title="Log Check-in" class="p-2 rounded-xl bg-brand-50 text-brand-600 hover:bg-brand-600 hover:text-white transition-all shadow-2xs">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </a>
                                    <a href="{{ route('orders.create', ['customer_id' => $c->id]) }}" 
                                       title="Take Order" class="p-2 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all shadow-2xs">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </a>
                                    <a href="{{ route('customers.show', $c) }}" 
                                       class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition-colors">
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400 font-medium">
                                No customers found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
