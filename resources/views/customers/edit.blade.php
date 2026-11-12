@extends('layouts.app')

@section('title', 'Edit ' . $customer->company_name)
@section('header_title', 'Edit Customer')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        
        <div class="p-6 bg-slate-900 text-white">
            <h2 class="text-xl font-extrabold text-white">Edit {{ $customer->company_name }}</h2>
            <p class="text-xs text-slate-300 mt-1">Update customer contacts, category, credit limit, or operating status.</p>
        </div>

        <form action="{{ route('customers.update', $customer) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Company / Store Name</label>
                    <input type="text" name="company_name" required value="{{ old('company_name', $customer->company_name) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Contact Person</label>
                    <input type="text" name="contact_person" required value="{{ old('contact_person', $customer->contact_person) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Category</label>
                    <select name="category" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="retailer" {{ $customer->category === 'retailer' ? 'selected' : '' }}>Retail Shop</option>
                        <option value="wholesaler" {{ $customer->category === 'wholesaler' ? 'selected' : '' }}>Wholesale Distributor</option>
                        <option value="electrical_contractor" {{ $customer->category === 'electrical_contractor' ? 'selected' : '' }}>Electrical Contractor</option>
                        <option value="lighting_showroom" {{ $customer->category === 'lighting_showroom' ? 'selected' : '' }}>Lighting Showroom</option>
                        <option value="supermarket" {{ $customer->category === 'supermarket' ? 'selected' : '' }}>Supermarket</option>
                        <option value="real_estate_developer" {{ $customer->category === 'real_estate_developer' ? 'selected' : '' }}>Estate Developer</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Phone</label>
                    <input type="text" name="phone" required value="{{ old('phone', $customer->phone) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Alternative Phone</label>
                    <input type="text" name="alt_phone" value="{{ old('alt_phone', $customer->alt_phone) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Operating Status</label>
                    <select name="status" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="active" {{ $customer->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $customer->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="blacklisted" {{ $customer->status === 'blacklisted' ? 'selected' : '' }}>Blacklisted (Credit Risk)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Territory</label>
                    <input type="text" name="territory" required value="{{ old('territory', $customer->territory) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Landmark</label>
                    <input type="text" name="landmark" value="{{ old('landmark', $customer->landmark) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Physical Address</label>
                    <input type="text" name="address" required value="{{ old('address', $customer->address) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">City</label>
                    <input type="text" name="city" required value="{{ old('city', $customer->city) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">State</label>
                    <input type="text" name="state" required value="{{ old('state', $customer->state) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Credit Limit (₦)</label>
                    <input type="number" name="credit_limit" required min="0" step="10000" value="{{ old('credit_limit', $customer->credit_limit) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Payment Terms</label>
                    <select name="payment_terms" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="cash_on_delivery" {{ $customer->payment_terms === 'cash_on_delivery' ? 'selected' : '' }}>Cash on Delivery (COD)</option>
                        <option value="net_7" {{ $customer->payment_terms === 'net_7' ? 'selected' : '' }}>Net 7 Days</option>
                        <option value="net_14" {{ $customer->payment_terms === 'net_14' ? 'selected' : '' }}>Net 14 Days</option>
                        <option value="net_30" {{ $customer->payment_terms === 'net_30' ? 'selected' : '' }}>Net 30 Days</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('notes', $customer->notes) }}</textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('customers.show', $customer) }}" class="text-xs font-bold text-slate-600 hover:underline">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition-all">
                    Save Changes
                </button>
            </div>
        </form>

    </div>

</div>
@endsection

