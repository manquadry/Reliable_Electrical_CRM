@extends('layouts.app')

@section('title', 'Onboard New Customer')
@section('header_title', 'Customer & Retailer Onboarding')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        
        <!-- Header Banner -->
        <div class="p-6 bg-gradient-to-r from-blue-900 via-indigo-950 to-slate-900 text-white">
            <div class="flex items-center space-x-2 text-blue-400 text-xs font-bold uppercase tracking-wider mb-1">
                <i class="fa-solid fa-shop"></i>
                <span>Field Client Registration</span>
            </div>
            <h2 class="text-xl font-extrabold text-white">Onboard New Customer / Store</h2>
            <p class="text-xs text-slate-300 mt-1">
                Register a new retail shop, electrical wholesaler, contractor, or lighting showroom. Records the exact onboarding date and assigning field sales executive.
            </p>
        </div>

        <form action="{{ route('customers.store') }}" method="POST" class="p-6 space-y-6" x-data="{
            lat: '',
            lng: '',
            address: '{{ old('address') }}',
            city: '{{ old('city', 'Lagos') }}',
            state: '{{ old('state', 'Lagos State') }}',
            landmark: '{{ old('landmark') }}',
            locStatus: '',
            isResolving: false,
            async resolveAddress(latitude, longitude) {
                this.isResolving = true;
                this.locStatus = 'GPS locked: ' + latitude + ', ' + longitude + ' • Resolving address & landmark...';
                try {
                    const res = await fetch('{{ route('reverse_geocode') }}?lat=' + latitude + '&lng=' + longitude);
                    if (res.ok) {
                        const data = await res.json();
                        if (data.address) {
                            this.address = data.address;
                            if (data.city) this.city = data.city;
                            if (data.state) this.state = data.state;
                            if (data.suburb && !this.landmark) this.landmark = 'Near ' + data.suburb;
                            this.locStatus = '📍 ' + data.address;
                            this.isResolving = false;
                            return;
                        }
                    }
                } catch (e) {
                    try {
                        const bdc = await fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${latitude}&longitude=${longitude}&localityLanguage=en`);
                        if (bdc.ok) {
                            const data = await bdc.json();
                            const parts = [data.locality, data.city, data.principalSubdivision].filter(Boolean);
                            if (parts.length > 0) {
                                this.address = parts.join(', ');
                                if (data.city) this.city = data.city;
                                if (data.principalSubdivision) this.state = data.principalSubdivision;
                                this.locStatus = '📍 ' + this.address;
                                this.isResolving = false;
                                return;
                            }
                        }
                    } catch (err) {}
                }
                this.locStatus = 'GPS locked: ' + latitude + ', ' + longitude;
                this.isResolving = false;
            },
            getGPS() {
                if ('geolocation' in navigator) {
                    this.locStatus = 'Retrieving GPS position...';
                    navigator.geolocation.getCurrentPosition((pos) => {
                        this.lat = pos.coords.latitude.toFixed(6);
                        this.lng = pos.coords.longitude.toFixed(6);
                        this.resolveAddress(this.lat, this.lng);
                    }, (err) => {
                        this.locStatus = 'GPS error: ' + err.message;
                    }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
                } else {
                    this.locStatus = 'Geolocation not supported';
                }
            }
        }">
            @csrf

            <!-- Section 1: Business Identity -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 pb-2 border-b border-slate-100 mb-4">
                    1. Store & Business Identity
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Store / Company Business Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="company_name" required value="{{ old('company_name') }}" placeholder="e.g. De-Royal Electricals & Construction Supplies"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Customer Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="customer_code" required value="{{ old('customer_code', $suggestedCode) }}"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-slate-50 font-mono text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Customer Category <span class="text-red-500">*</span>
                        </label>
                        <select name="category" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="retailer">Retail Appliance & Light Shop</option>
                            <option value="wholesaler">Wholesale Distributor / Importer</option>
                            <option value="electrical_contractor">Electrical Contractor / Installer</option>
                            <option value="lighting_showroom">Luxury Lighting Showroom</option>
                            <option value="supermarket">Supermarket Electrical Section</option>
                            <option value="real_estate_developer">Real Estate Developer</option>
                            <option value="walk_in">Walk-in Customer / Over-The-Counter Cash</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Contact Person / Proprietor <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="contact_person" required value="{{ old('contact_person') }}" placeholder="e.g. Chief Kenneth Nwachukwu"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Primary Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="phone" required value="{{ old('phone') }}" placeholder="e.g. +234 803 555 1234"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Alternative Phone
                        </label>
                        <input type="text" name="alt_phone" value="{{ old('alt_phone') }}" placeholder="e.g. +234 812 000 9988"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Email Address
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="e.g. store@gmail.com"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Section 2: Location & Address -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 pb-2 border-b border-slate-100 mb-4">
                    2. Physical Store Location & Coordinates
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Sales Territory / Zone <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="territory" required value="{{ old('territory', Auth::user()->territory) }}" placeholder="e.g. Ikeja, Alaba, Lekki, Gbagada"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Prominent Landmark
                        </label>
                        <input type="text" name="landmark" x-model="landmark" placeholder="e.g. Opposite FCMB Bank, Beside Total Filling Station"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Detailed Physical Address <span class="text-red-500">*</span>
                            </label>
                            <span x-show="isResolving" class="text-[10px] text-blue-600 font-semibold animate-pulse" x-cloak>
                                <i class="fa-solid fa-spinner fa-spin mr-1"></i> Auto-detecting physical address...
                            </span>
                        </div>
                        <input type="text" name="address" required x-model="address" placeholder="e.g. Plot 14B Adetokunbo Ademola Street, Victoria Island"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">City <span class="text-red-500">*</span></label>
                        <input type="text" name="city" required x-model="city"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">State <span class="text-red-500">*</span></label>
                        <input type="text" name="state" required x-model="state"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <!-- GPS Coordinates Auto Capture -->
                    <div class="sm:col-span-2 p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <span class="text-xs font-bold text-slate-700">GPS Pinpoint for Mapping</span>
                                <p class="text-[10px] text-slate-500">Auto-detects physical address, city, state and landmark.</p>
                            </div>
                            <button type="button" @click="getGPS()" 
                                    class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-bold flex items-center space-x-1">
                                <i class="fa-solid fa-location-crosshairs text-[11px]"></i>
                                <span>Capture Current GPS</span>
                            </button>
                        </div>
                        <div x-show="locStatus" class="text-[11px] font-semibold text-blue-700 mb-2" x-text="locStatus" x-cloak></div>
                        <div class="grid grid-cols-2 gap-3">
                            <input type="text" name="latitude" x-model="lat" placeholder="Latitude (e.g. 6.4281)"
                                   class="px-3 py-1.5 border border-slate-300 rounded-lg bg-white text-xs text-slate-800">
                            <input type="text" name="longitude" x-model="lng" placeholder="Longitude (e.g. 3.4219)"
                                   class="px-3 py-1.5 border border-slate-300 rounded-lg bg-white text-xs text-slate-800">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Onboarding Date & Field Agent (The Core Requirements) -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 pb-2 border-b border-slate-100 mb-4">
                    3. Onboarding Details & Field Agent Assignment
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Day Onboarded <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="onboarded_date" required value="{{ old('onboarded_date', Carbon\Carbon::today()->toDateString()) }}"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <p class="text-[10px] text-slate-400 mt-0.5">The exact date this customer is registered</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Onboarded By (Field Sales Rep) <span class="text-red-500">*</span>
                        </label>
                        <select name="onboarded_by" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            @foreach($agents as $ag)
                                <option value="{{ $ag->id }}" {{ (old('onboarded_by') == $ag->id || Auth::id() == $ag->id) ? 'selected' : '' }}>
                                    {{ $ag->name }} ({{ $ag->agent_code }} - {{ $ag->territory }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400 mt-0.5">Field executive credited for customer acquisition</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Approved Credit Limit (₦) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="credit_limit" required min="0" step="50000" value="{{ old('credit_limit', 1000000) }}"
                               class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Payment Terms <span class="text-red-500">*</span>
                        </label>
                        <select name="payment_terms" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="cash_on_delivery">Cash on Delivery (COD)</option>
                            <option value="net_7">Net 7 Days</option>
                            <option value="net_14" selected>Net 14 Days</option>
                            <option value="net_30">Net 30 Days (Approved Key Accounts)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Store Remarks & Business Potential Notes
                </label>
                <textarea name="notes" rows="2" placeholder="e.g. Major buyer of floodlights, currently expanding showroom, owner prefers deliveries before 10 AM..."
                          class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('customers.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition-all flex items-center space-x-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Register & Onboard Customer</span>
                </button>
            </div>

        </form>

    </div>

</div>
@endsection

