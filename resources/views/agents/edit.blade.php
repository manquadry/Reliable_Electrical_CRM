@extends('layouts.app')

@section('title', 'Edit ' . $agent->name)
@section('header_title', 'Edit Field Executive')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        
        <div class="p-6 bg-slate-900 text-white">
            <h2 class="text-xl font-extrabold text-white">Edit {{ $agent->name }} ({{ $agent->agent_code }})</h2>
            <p class="text-xs text-slate-300 mt-1">Update operational territory, sales targets, status, or login details.</p>
        </div>

        <form action="{{ route('agents.update', $agent) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Full Name</label>
                    <input type="text" name="name" required value="{{ old('name', $agent->name) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Email Address</label>
                    <input type="email" name="email" required value="{{ old('email', $agent->email) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Phone Number</label>
                    <input type="text" name="phone" required value="{{ old('phone', $agent->phone) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="active" {{ $agent->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $agent->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ $agent->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Sales Territory</label>
                    <input type="text" name="territory" required value="{{ old('territory', $agent->territory) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Vehicle / Transport</label>
                    <input type="text" name="vehicle_type" value="{{ old('vehicle_type', $agent->vehicle_type) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Daily Visit Target</label>
                    <input type="number" name="daily_visit_target" required min="1" max="30" value="{{ old('daily_visit_target', $agent->daily_visit_target) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Monthly Onboard Target</label>
                    <input type="number" name="monthly_onboard_target" required min="1" max="100" value="{{ old('monthly_onboard_target', $agent->monthly_onboard_target) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Monthly Revenue Quota (₦)</label>
                    <input type="number" name="monthly_revenue_target" required min="0" step="10000" value="{{ old('monthly_revenue_target', $agent->monthly_revenue_target) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">New Password (leave blank to keep current)</label>
                    <input type="password" name="password" placeholder="Enter new password if updating"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('notes', $agent->notes) }}</textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('agents.show', $agent) }}" class="text-xs font-bold text-slate-600 hover:underline">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition-all">
                    Update Profile
                </button>
            </div>
        </form>

    </div>

</div>
@endsection

