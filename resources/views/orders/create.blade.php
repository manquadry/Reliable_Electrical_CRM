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

        <form action="{{ route('orders.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Customer & Order Meta -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Customer Store <span class="text-red-500">*</span>
                    </label>
                    <select name="customer_id" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">-- Select Customer --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ (old('customer_id') == $c->id || (isset($selectedCustomer) && $selectedCustomer->id == $c->id)) ? 'selected' : '' }}>
                                {{ $c->company_name }} ({{ $c->contact_person }} - {{ $c->territory }})
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
                        Delivery / Warehouse Address
                    </label>
                    <input type="text" name="delivery_address" value="{{ old('delivery_address', isset($selectedCustomer) ? $selectedCustomer->address : '') }}" placeholder="Default store address or site address"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
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

