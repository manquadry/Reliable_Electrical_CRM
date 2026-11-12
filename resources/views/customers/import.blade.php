@extends('layouts.app')

@section('title', 'Bulk Customer Import - Reliable Electrics CRM')
@section('header_title', 'Customer Ledger Import')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Back Navigation & Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('customers.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Customer Directory</span>
        </a>
        <div class="flex items-center space-x-2">
            @if($unassignedCount > 0)
                <a href="{{ route('customers.index', ['agent_id' => 'unassigned']) }}" 
                   class="px-3 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 text-xs font-bold hover:bg-amber-100 transition-colors flex items-center space-x-1.5">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                    <span>{{ $unassignedCount }} Unassigned Customers</span>
                </a>
            @endif
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start space-x-3">
            <i class="fa-solid fa-triangle-exclamation text-rose-500 text-sm shrink-0 mt-0.5"></i>
            <div>
                <strong class="font-bold block mb-1">Import Errors Detected:</strong>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-card overflow-hidden">
        
        <!-- Header Banner -->
        <div class="p-6 bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-950 text-white">
            <div class="flex items-center space-x-2 text-brand-400 text-xs font-bold uppercase tracking-wider mb-1">
                <i class="fa-solid fa-file-excel"></i>
                <span>Enterprise Spreadsheet Migration</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                Import Customers from Excel / CSV Sheet
            </h1>
            <p class="text-xs text-slate-300 mt-1 max-w-2xl leading-relaxed">
                Quickly migrate your manual customer registers into the CRM. You can upload an Excel workbook (.xlsx) or CSV (.csv), assign them to a Field Sales Representative of your choice, or keep them unassigned for territory allocation.
            </p>
        </div>

        <!-- 3-Step Guided Workflow -->
        <div class="p-6 sm:p-8 space-y-8">

            <!-- Step 1: Download Official Template -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start space-x-3.5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg font-bold shrink-0">
                        <i class="fa-solid fa-download"></i>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Step 1</span>
                            <span class="text-sm font-extrabold text-slate-900">Download Spreadsheet Template</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Pre-configured with official column headers and sample electrical retail shops across Lagos.
                        </p>
                    </div>
                </div>

                <a href="{{ route('customers.download_template') }}" 
                   class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-600/30 hover:shadow-emerald-600/50 transition-all shrink-0">
                    <i class="fa-solid fa-file-csv"></i>
                    <span>Download Sample Template (.CSV)</span>
                </a>
            </div>

            <!-- Step 2: Upload Form & Agent Allocation -->
            <form action="{{ route('customers.import_process') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-600">Step 2 & 3</span>
                        <span class="text-sm font-extrabold text-slate-900">Select File & Assign Field Sales Rep</span>
                    </div>

                    <!-- File Dropzone -->
                    <div class="border-2 border-dashed border-slate-300 hover:border-brand-500 rounded-2xl p-6 sm:p-8 text-center bg-slate-50/50 hover:bg-brand-50/20 transition-all cursor-pointer relative"
                         x-data="{ fileName: '' }">
                        <input type="file" name="spreadsheet" required accept=".csv,.xlsx,.txt"
                               @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="space-y-2">
                            <div class="w-12 h-12 mx-auto rounded-2xl bg-brand-100 text-brand-600 flex items-center justify-center text-xl">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <div class="text-xs text-slate-600">
                                <span class="font-bold text-brand-700 hover:underline">Click to browse</span> or drag and drop your spreadsheet here
                            </div>
                            <p class="text-[11px] text-slate-400">
                                Supports Microsoft Excel (<strong>.xlsx</strong>) and standard Comma-Separated Values (<strong>.csv</strong>) up to 10MB
                            </p>
                            <template x-if="fileName">
                                <div class="pt-2">
                                    <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-bold">
                                        <i class="fa-solid fa-file-lines text-emerald-600"></i>
                                        <span x-text="fileName"></span>
                                    </span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Field Sales Rep Attachment Option -->
                <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/80 space-y-3">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-user-check text-brand-600 text-sm"></i>
                        <label for="default_agent_id" class="text-xs font-bold uppercase tracking-wider text-slate-800">
                            Attach Customers to Field Sales Rep (Default Assignment)
                        </label>
                    </div>
                    <p class="text-[11px] text-slate-500">
                        Choose a Field Sales Representative to assign to these customers. If the spreadsheet contains an <code>assigned_agent_code</code> column (e.g., <code>REL-FE-101</code>), that specific row will override this default.
                    </p>
                    <select id="default_agent_id" name="default_agent_id" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl bg-white text-slate-800 text-xs font-semibold focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        <option value="">-- Leave Unassigned (Move to Central HQ Pool for Later Allocation) --</option>
                        @foreach($agents as $ag)
                            <option value="{{ $ag->id }}">
                                Attach to: {{ $ag->name }} &bull; {{ $ag->agent_code }} ({{ $ag->territory }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Column Reference Accordion -->
                <div class="p-4 rounded-xl border border-slate-200 text-xs space-y-2" x-data="{ open: false }">
                    <button type="button" @click="open = !open" class="flex items-center justify-between w-full font-bold text-slate-700 hover:text-slate-900">
                        <span class="flex items-center space-x-2">
                            <i class="fa-solid fa-circle-question text-brand-500"></i>
                            <span>View Supported Column Headers & Descriptions</span>
                        </span>
                        <i class="fa-solid" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    <div x-show="open" class="pt-3 border-t border-slate-200/80 space-y-2 text-[11px] text-slate-600">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <div class="p-2 bg-slate-100/70 rounded-lg">
                                <strong class="text-slate-900">Company / Store Name</strong> <span class="text-red-500">*</span><br>
                                Name of the retail shop, wholesaler, or developer.
                            </div>
                            <div class="p-2 bg-slate-100/70 rounded-lg">
                                <strong class="text-slate-900">Primary Phone</strong> <span class="text-red-500">*</span><br>
                                Customer's direct phone number (e.g. +234 803 123 4567).
                            </div>
                            <div class="p-2 bg-slate-100/70 rounded-lg">
                                <strong class="text-slate-900">Contact Person</strong><br>
                                Proprietor or store manager (defaults to 'Proprietor').
                            </div>
                            <div class="p-2 bg-slate-100/70 rounded-lg">
                                <strong class="text-slate-900">Territory / Zone</strong><br>
                                e.g. Ikeja, Alaba, Lekki, Victoria Island, Gbagada.
                            </div>
                            <div class="p-2 bg-slate-100/70 rounded-lg">
                                <strong class="text-slate-900">Customer Category</strong><br>
                                retailer, wholesaler, electrical_contractor, lighting_showroom, supermarket, walk_in.
                            </div>
                            <div class="p-2 bg-slate-100/70 rounded-lg">
                                <strong class="text-slate-900">Assigned Agent Code</strong><br>
                                Rep's code (e.g. REL-FE-101) or rep's work email.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Action -->
                <div class="pt-4 border-t border-slate-200 flex items-center justify-end space-x-3">
                    <a href="{{ route('customers.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs shadow-md shadow-brand-600/30 hover:shadow-brand-600/50 transition-all flex items-center space-x-2">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Upload & Import Customers</span>
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>
@endsection

