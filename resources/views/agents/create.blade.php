@extends('layouts.app')

@section('title', 'Onboard Field Executive')
@section('header_title', 'Field Executive Onboarding')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        
        <!-- Header Banner -->
        <div class="p-6 bg-gradient-to-r from-slate-900 to-indigo-950 text-white">
            <div class="flex items-center space-x-2 text-blue-400 text-xs font-bold uppercase tracking-wider mb-1">
                <i class="fa-solid fa-user-plus"></i>
                <span>Human Resources & Commercial Field Team</span>
            </div>
            <h2 class="text-xl font-extrabold text-white">Onboard New Field Sales Executive</h2>
            <p class="text-xs text-slate-300 mt-1">
                Register a new sales representative into Reliable Electrics CRM. Assign their operational territory, daily visit target, and sales quota.
            </p>
        </div>

        <form action="{{ route('agents.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Personal & Login Info -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 pb-2 border-b border-slate-100 mb-4">
                    1. Identity & System Credentials
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" required value="{{ old('name') }}" placeholder="e.g. Babatunde Fashola"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Official Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" required value="{{ old('email') }}" placeholder="e.g. babatunde@reliableelectrics.ng"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Agent Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="agent_code" required value="{{ old('agent_code', $suggestedCode) }}"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-slate-50 font-mono text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <p class="text-[10px] text-slate-400 mt-0.5">Unique employee sales code</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="phone" required value="{{ old('phone') }}" placeholder="e.g. +234 803 123 4567"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Portal Login Password
                        </label>
                        <input type="password" name="password" placeholder="Leave blank to default to 'password'"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Territory & Logistics -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 pb-2 border-b border-slate-100 mb-4">
                    2. Assigned Territory & Operations
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Primary Sales Territory / Route <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="territory" required value="{{ old('territory') }}" placeholder="e.g. Surulere, Ojuelegba & Yaba Hub"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Vehicle / Mode of Transport
                        </label>
                        <input type="text" name="vehicle_type" value="{{ old('vehicle_type', 'Company Motorbike') }}" placeholder="e.g. Company Motorbike, Car, Public Transit"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Onboarding Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="onboarded_date" required value="{{ old('onboarded_date', Carbon\Carbon::today()->toDateString()) }}"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Employment Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="active">Active (Full Field Access)</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Performance Targets & Quotas -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 pb-2 border-b border-slate-100 mb-4">
                    3. Performance Targets & KPI Quotas
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Daily Visits Target <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="daily_visit_target" required min="1" max="25" value="{{ old('daily_visit_target', 6) }}"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <p class="text-[10px] text-slate-400 mt-0.5">Visits expected per day</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Monthly Onboard Target <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="monthly_onboard_target" required min="1" max="100" value="{{ old('monthly_onboard_target', 15) }}"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <p class="text-[10px] text-slate-400 mt-0.5">New stores per month</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Monthly Revenue Quota (₦) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="monthly_revenue_target" required min="0" step="50000" value="{{ old('monthly_revenue_target', 5000000) }}"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <p class="text-[10px] text-slate-400 mt-0.5">In Nigerian Naira (₦)</p>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Remarks / Onboarding Notes
                </label>
                <textarea name="notes" rows="2" placeholder="Guarantor verified, previous distributor experience, specialized electrical categories..."
                          class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('notes') }}</textarea>
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('agents.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition-all flex items-center space-x-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Complete Agent Onboarding</span>
                </button>
            </div>

        </form>

    </div>

</div>
@endsection

