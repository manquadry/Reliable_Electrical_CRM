@extends('layouts.app')

@section('title', 'Log Field Check-In')
@section('header_title', 'Field Executive Check-In')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-card overflow-hidden">
        
        <!-- Header Banner -->
        <div class="p-6 bg-gradient-to-r from-slate-950 via-slate-900 to-brand-950 text-white relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-brand-600/20 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="relative z-10">
                <div class="flex items-center space-x-2 text-brand-400 text-xs font-bold uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>Field Route Operations</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">Log Field Visit / Customer Check-In</h1>
                <p class="text-xs text-slate-300 mt-1 max-w-xl leading-relaxed">
                    Capture in-person store visits, restocking discussions, payments, and retail audits with exact timestamps and live GPS geocoding.
                </p>
            </div>
        </div>

        <form action="{{ route('activities.store_checkin') }}" method="POST" class="p-6 sm:p-7 space-y-5" x-data="{
            hasGPS: false,
            lat: '',
            lng: '',
            address: '{{ old('location_address') }}',
            locStatus: '',
            isResolving: false,
            isLocating: false,
            async resolveAddress(latitude, longitude) {
                this.isResolving = true;
                this.locStatus = 'GPS locked: ' + latitude + ', ' + longitude + ' • Resolving physical address...';
                try {
                    const res = await fetch('{{ route('reverse_geocode') }}?lat=' + latitude + '&lng=' + longitude);
                    if (res.ok) {
                        const data = await res.json();
                        if (data.address) {
                            this.address = data.address;
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
            getLocation() {
                if ('geolocation' in navigator) {
                    this.isLocating = true;
                    this.locStatus = 'Requesting live GPS satellite lock...';
                    navigator.geolocation.getCurrentPosition((pos) => {
                        this.lat = pos.coords.latitude.toFixed(6);
                        this.lng = pos.coords.longitude.toFixed(6);
                        this.hasGPS = true;
                        this.isLocating = false;
                        this.resolveAddress(this.lat, this.lng);
                    }, (err) => {
                        this.isLocating = false;
                        this.locStatus = 'GPS unavailable (' + err.message + ')';
                    }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
                } else {
                    this.locStatus = 'Geolocation not supported by this browser';
                }
            }
        }">
            @csrf

            <!-- Customer Store Selection -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Customer / Store Visited <span class="text-rose-500">*</span>
                </label>
                <select name="customer_id" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none transition-all">
                    <option value="">-- General Prospecting / Unregistered Shop --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ (old('customer_id') == $c->id || (isset($selectedCustomer) && $selectedCustomer->id == $c->id)) ? 'selected' : '' }}>
                            {{ $c->company_name }} ({{ $c->contact_person }} - {{ $c->territory }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">
                    Visiting a new store? You can also <a href="{{ route('customers.create') }}" class="text-brand-600 font-bold hover:underline">onboard the customer first</a>.
                </p>
            </div>

            <!-- Date & Time Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Visit Date <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="activity_date" value="{{ old('activity_date', $today) }}" required
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Check-In Time <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" name="check_in_time" value="{{ old('check_in_time', $currentTime) }}" required
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none transition-all">
                </div>
            </div>

            <!-- Activity Type & Outcome -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Activity Type <span class="text-rose-500">*</span>
                    </label>
                    <select name="activity_type" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none transition-all">
                        <option value="store_visit">Store Visit / Meeting</option>
                        <option value="order_booking">Order Booking / Restock</option>
                        <option value="payment_collection">Payment / Debt Collection</option>
                        <option value="product_demo">Product Demonstration</option>
                        <option value="new_customer_onboarding">Customer Onboarding Follow-up</option>
                        <option value="complaint_resolution">Complaint / Warranty Resolution</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Outcome <span class="text-rose-500">*</span>
                    </label>
                    <select name="outcome" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none transition-all">
                        <option value="order_taken">Order Secured / Taken</option>
                        <option value="payment_received">Payment / Check Collected</option>
                        <option value="followup_scheduled">Follow-up Scheduled</option>
                        <option value="prospect_interested">Prospect Interested</option>
                        <option value="closed">Meeting Resolved / Closed</option>
                        <option value="not_interested">Not Interested at this time</option>
                    </select>
                </div>
            </div>

            <!-- Purpose of Visit -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Purpose of Visit / Agenda <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="purpose" required value="{{ old('purpose') }}" placeholder="e.g. Weekly inventory replenishment and introduction of 300W solar streetlights"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none transition-all">
            </div>

            <!-- Discussion Notes & Feedback -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Discussion Notes, Competitor Insights & Customer Feedback
                </label>
                <textarea name="notes" rows="3" placeholder="Key takeaways: competitor pricing observed, customer requested credit extension, products running low in store..."
                          class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none transition-all">{{ old('notes') }}</textarea>
            </div>

            <!-- Next Follow-up Date -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Scheduled Next Follow-Up Date (Optional)
                </label>
                <input type="date" name="next_followup_date" value="{{ old('next_followup_date') }}"
                       class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none transition-all">
            </div>

            <!-- Location & GPS Verification Card -->
            <div class="p-5 bg-gradient-to-br from-slate-50 to-brand-50/20 rounded-2xl border border-slate-200/80 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-900">Field Geolocation Stamp</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5">Captures high-accuracy GPS coordinates to confirm authentic physical presence on site.</p>
                    </div>
                    <button type="button" @click="getLocation()" 
                            class="inline-flex items-center space-x-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-500 text-white rounded-xl text-xs font-bold shadow-md shadow-brand-600/30 transition-all shrink-0">
                        <i class="fa-solid fa-crosshairs" :class="isLocating ? 'animate-spin' : ''"></i>
                        <span x-text="isLocating ? 'Locating...' : 'Get Current GPS'">Get Current GPS</span>
                    </button>
                </div>

                <div x-show="locStatus" class="p-3 rounded-xl bg-brand-50 border border-brand-200/70 text-xs font-bold text-brand-800 flex items-center space-x-2" x-cloak>
                    <i class="fa-solid fa-satellite-dish text-brand-600 text-sm"></i>
                    <span x-text="locStatus"></span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Latitude</label>
                        <input type="text" name="latitude" x-model="lat" placeholder="e.g. 6.524400"
                               class="w-full px-3.5 py-2 border border-slate-300 rounded-xl bg-white text-xs font-mono text-slate-800 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Longitude</label>
                        <input type="text" name="longitude" x-model="lng" placeholder="e.g. 3.379200"
                               class="w-full px-3.5 py-2 border border-slate-300 rounded-xl bg-white text-xs font-mono text-slate-800 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Physical Location Address / Landmark</label>
                        <span x-show="isResolving" class="text-[10px] text-brand-600 font-bold animate-pulse" x-cloak>
                            <i class="fa-solid fa-spinner fa-spin mr-1"></i> Auto-detecting landmark...
                        </span>
                    </div>
                    <div class="relative">
                        <input type="text" name="location_address" x-model="address" placeholder="e.g. 42 Allen Avenue, Ikeja, Lagos"
                               class="w-full pl-3.5 pr-8 py-2.5 border border-slate-300 rounded-xl bg-white text-xs text-slate-800 focus:ring-2 focus:ring-brand-500 focus:outline-none transition-all">
                        <button type="button" x-show="address" @click="address = ''" class="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600 text-xs" title="Clear">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">
                        Automatically reverse-geocoded from satellite coordinates. You can append specific details like shop name, suite number, or nearest landmark.
                    </p>
                </div>
            </div>

            <!-- Submit Button Row -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs shadow-lg shadow-brand-600/30 hover:shadow-brand-600/50 transition-all flex items-center space-x-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Save Field Check-In</span>
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
