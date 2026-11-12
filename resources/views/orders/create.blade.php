@extends('layouts.app')

@section('title', 'Book New Sales Order')
@section('header_title', 'Create Sales Order')

@section('content')
<div class="max-w-4xl mx-auto" x-data="{
    products: {{ Js::from($products) }},
    items: [
        { product_id: '', quantity: 1, unit_price: 0, line_total: 0 }
    ],
    discount: 0,
    addItem() {
        this.items.push({ product_id: '', quantity: 1, unit_price: 0, line_total: 0 });
    },
    removeItem(index) {
        if (this.items.length > 1) {
            this.items.splice(index, 1);
        }
    },
    onProductChange(index) {
        const pId = this.items[index].product_id;
        const p = this.products.find(x => x.id == pId);
        if (p) {
            this.items[index].unit_price = parseFloat(p.wholesale_price);
            this.calculateLine(index);
        }
    },
    calculateLine(index) {
        const item = this.items[index];
        item.line_total = (parseInt(item.quantity) || 0) * (parseFloat(item.unit_price) || 0);
    },
    get subtotal() {
        return this.items.reduce((acc, curr) => acc + (parseFloat(curr.line_total) || 0), 0);
    },
    get grandTotal() {
        return Math.max(0, this.subtotal - (parseFloat(this.discount) || 0));
    }
}">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        
        @if(isset($isWalkIn) && $isWalkIn)
            <!-- Walk-in Counter Sale Banner -->
            <div class="p-4 bg-gradient-to-r from-cyan-950 via-slate-900 to-teal-950 text-white border-b border-cyan-800/80">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 flex items-center justify-center text-lg font-bold shrink-0">
                            <i class="fa-solid fa-person-walking-arrow-right"></i>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <h2 class="text-base sm:text-lg font-extrabold text-white">⚡ Quick Counter Sale (Walk-in Customer)</h2>
                                <span class="px-2 py-0.5 bg-cyan-500/20 text-cyan-300 text-[10px] rounded-full border border-cyan-500/30 font-bold uppercase tracking-wider">
                                    Gbagada Showroom
                                </span>
                            </div>
                            <p class="text-xs text-slate-300 mt-0.5">
                                Over-the-counter sale with instant checkout, stock deduction, and immediate waybill printout.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('orders.create') }}" class="hidden sm:inline-flex text-xs font-semibold text-slate-400 hover:text-white underline">
                        Switch to Standard Field Order
                    </a>
                </div>
            </div>
        @else
            <div class="p-6 bg-gradient-to-r from-slate-900 to-blue-950 text-white">
                <div class="flex items-center space-x-2 text-blue-400 text-xs font-bold uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>Field Sales Booking</span>
                </div>
                <h2 class="text-xl font-extrabold text-white">Book Commercial Sales Order</h2>
                <p class="text-xs text-slate-300 mt-1">
                    Generate a formal purchase order for an onboarded customer. Deducts product stock and updates agent commission targets.
                </p>
            </div>
        @endif

        <form action="{{ route('orders.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Customer & Order Meta -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Customer Account <span class="text-red-500">*</span>
                    </label>
                    <select name="customer_id" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">-- Select Customer --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ (old('customer_id') == $c->id || (isset($selectedCustomer) && $selectedCustomer->id == $c->id)) ? 'selected' : '' }}>
                                {{ $c->isWalkIn() ? '⚡ ' : '' }}{{ $c->company_name }} ({{ $c->contact_person }} - {{ $c->territory }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Order Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="order_number" required value="{{ old('order_number', $suggestedOrderNumber) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-slate-50 font-mono text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Order Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="order_date" required value="{{ old('order_date', Carbon\Carbon::today()->toDateString()) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Delivery / Warehouse Collection Address
                    </label>
                    <input type="text" name="delivery_address" 
                           value="{{ old('delivery_address', (isset($isWalkIn) && $isWalkIn) ? 'Immediate Counter Collection (Gbagada Showroom)' : (isset($selectedCustomer) ? $selectedCustomer->address : '')) }}" 
                           placeholder="Default store address or collection point"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <!-- Walk-in Individual Buyer Details (Optional) -->
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/70">
                <div class="flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                    <i class="fa-solid fa-id-card text-brand-600"></i>
                    <span>Walk-In Individual Buyer Details (Optional)</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Buyer / Contractor Name</label>
                        <input type="text" name="walkin_buyer_name" value="{{ old('walkin_buyer_name') }}" placeholder="e.g. Engr. Tunde Adeleke"
                               class="w-full px-3 py-2 border border-slate-300 rounded-lg bg-white text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Buyer Phone Number</label>
                        <input type="text" name="walkin_buyer_phone" value="{{ old('walkin_buyer_phone') }}" placeholder="e.g. +234 803 123 4567"
                               class="w-full px-3 py-2 border border-slate-300 rounded-lg bg-white text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Dynamic Line Items Section -->
            <div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-200 mb-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Order Line Items</h3>
                    <button type="button" @click="addItem()" 
                            class="px-3 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-bold flex items-center space-x-1">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Add Product Row</span>
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 grid grid-cols-12 gap-3 items-center text-xs">
                            
                            <!-- Product Select -->
                            <div class="col-span-6 sm:col-span-5">
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Product</label>
                                <select :name="`items[${index}][product_id]`" x-model="item.product_id" @change="onProductChange(index)" required
                                        class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg bg-white text-xs text-slate-800">
                                    <option value="">-- Choose Electrical Item --</option>
                                    <template x-for="p in products" :key="p.id">
                                        <option :value="p.id" x-text="`${p.name} (₦${Number(p.wholesale_price).toLocaleString()} • ${p.stock_quantity} in stock)`"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Quantity -->
                            <div class="col-span-3 sm:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Qty</label>
                                <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity" @input="calculateLine(index)" min="1" required
                                       class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg bg-white text-xs text-center font-bold">
                            </div>

                            <!-- Unit Price -->
                            <div class="col-span-3 sm:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Unit Price (₦)</label>
                                <input type="number" :name="`items[${index}][unit_price]`" x-model="item.unit_price" @input="calculateLine(index)" min="0" step="100" required
                                       class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg bg-white text-xs font-semibold">
                            </div>

                            <!-- Line Total -->
                            <div class="col-span-10 sm:col-span-2 text-right">
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Line Total</label>
                                <div class="font-extrabold text-slate-900 pt-1" x-text="`₦${Number(item.line_total).toLocaleString()}`"></div>
                            </div>

                            <!-- Remove Row -->
                            <div class="col-span-2 sm:col-span-1 text-center pt-3">
                                <button type="button" @click="removeItem(index)" class="text-slate-400 hover:text-red-500 p-1" title="Remove Row">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>

                        </div>
                    </template>
                </div>
            </div>

            <!-- Totals & Discount Card -->
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
                
                <div class="flex items-center space-x-3">
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Special Discount (₦)</label>
                        <input type="number" name="discount_amount" x-model="discount" min="0" step="500" placeholder="0"
                               class="w-36 px-3 py-1.5 border border-slate-300 rounded-lg bg-white text-xs font-semibold">
                    </div>
                </div>

                <div class="space-y-1 text-right">
                    <div class="text-slate-500">
                        Subtotal: <strong class="text-slate-800" x-text="`₦${Number(subtotal).toLocaleString()}`"></strong>
                    </div>
                    <div class="text-sm font-extrabold text-slate-900">
                        Total Order Amount: <span class="text-emerald-600 text-lg" x-text="`₦${Number(grandTotal).toLocaleString()}`"></span>
                    </div>
                </div>

            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Dispatch / Packing Notes
                </label>
                <textarea name="notes" rows="2" placeholder="e.g. Needs delivery by Thursday morning, pack chandeliers in protective wooden crates..."
                          class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
            </div>

            <!-- Instant Payment at Counter Option -->
            <div class="p-4 rounded-xl border border-emerald-300 bg-emerald-50/80 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <label class="flex items-center space-x-2.5 cursor-pointer">
                        <input type="checkbox" name="instant_payment" value="1" {{ (isset($isWalkIn) && $isWalkIn) ? 'checked' : '' }}
                               class="rounded bg-white border-emerald-400 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                        <span class="text-xs font-bold text-emerald-950">
                            Instant Payment Received at Counter (Mark as Paid & Approved)
                        </span>
                    </label>
                    <span class="text-[10px] px-2 py-0.5 bg-emerald-200 text-emerald-900 rounded font-bold uppercase tracking-wider self-start sm:self-auto">
                        Instant Waybill & Receipt
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-1">
                    <label class="flex items-center space-x-2 p-2.5 bg-white rounded-lg border border-emerald-200 text-xs cursor-pointer hover:border-emerald-500 transition-colors">
                        <input type="radio" name="payment_method" value="pos_card" checked class="text-emerald-600 focus:ring-emerald-500">
                        <span class="font-bold text-slate-800">💳 POS Terminal Card</span>
                    </label>
                    <label class="flex items-center space-x-2 p-2.5 bg-white rounded-lg border border-emerald-200 text-xs cursor-pointer hover:border-emerald-500 transition-colors">
                        <input type="radio" name="payment_method" value="cash" class="text-emerald-600 focus:ring-emerald-500">
                        <span class="font-bold text-slate-800">💵 Cash at Counter</span>
                    </label>
                    <label class="flex items-center space-x-2 p-2.5 bg-white rounded-lg border border-emerald-200 text-xs cursor-pointer hover:border-emerald-500 transition-colors">
                        <input type="radio" name="payment_method" value="bank_transfer" class="text-emerald-600 focus:ring-emerald-500">
                        <span class="font-bold text-slate-800">🏦 Instant Bank Transfer</span>
                    </label>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('orders.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition-all flex items-center space-x-2">
                    <i class="fa-solid fa-cart-arrow-down"></i>
                    <span>Submit & Book Order</span>
                </button>
            </div>

        </form>

    </div>

</div>
@endsection

