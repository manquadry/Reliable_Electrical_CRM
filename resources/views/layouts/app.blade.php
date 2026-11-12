<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50/50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Reliable Electrics CRM') - Enterprise Field Sales & Commercial Distribution</title>

    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#eef6ff',
                            100: '#d9ebff',
                            200: '#bcd9ff',
                            300: '#8ec1fe',
                            400: '#589efb',
                            500: '#317cf7',
                            600: '#1b5eed',
                            700: '#1449cb',
                            800: '#163ea4',
                            900: '#173681',
                            950: '#0e204e',
                        },
                        slate: {
                            850: '#151f32',
                            900: '#0b1322',
                            950: '#070b14',
                        }
                    },
                    boxShadow: {
                        'soft': '0 2px 10px -2px rgba(15, 23, 42, 0.05), 0 1px 3px -1px rgba(15, 23, 42, 0.05)',
                        'card': '0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.04)',
                        'card-hover': '0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 4px 10px -4px rgba(15, 23, 42, 0.03)',
                        'glow': '0 0 20px -3px rgba(37, 99, 235, 0.35)',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style>
        [x-cloak] { display: none !important; }
        
        /* Modern Slim Scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Glassmorphism styling */
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: #fff !important; }
        }
    </style>
    @stack('styles')
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-[#f8fafc]" x-data="{ sidebarOpen: false }">
    <div class="min-h-full flex flex-col lg:flex-row">

        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false" 
             class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-40 lg:hidden" x-cloak></div>

        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed lg:static inset-y-0 left-0 z-50 w-72 bg-[#090e1a] text-slate-300 flex flex-col transition-transform duration-200 ease-in-out border-r border-slate-800/80 shadow-2xl lg:shadow-none no-print">
            
            <!-- Brand Header -->
            <div class="p-5 flex items-center justify-between border-b border-slate-800/80 bg-[#060a12]/60">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                    <div class="relative w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-sky-400 p-[1.5px] shadow-[0_0_20px_rgba(37,99,235,0.4)] flex items-center justify-center transition-transform group-hover:scale-105">
                        <div class="w-full h-full bg-[#0c1322] rounded-[10px] flex items-center justify-center">
                            <i class="fa-solid fa-bolt text-lg text-transparent bg-clip-text bg-gradient-to-tr from-sky-400 to-blue-500"></i>
                        </div>
                    </div>
                    <div>
                        <div class="font-extrabold text-white tracking-tight leading-tight text-base flex items-center space-x-1.5">
                            <span>Reliable Electrics</span>
                        </div>
                        <div class="flex items-center space-x-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                            <span class="text-[10px] text-blue-400 font-bold uppercase tracking-wider">Enterprise CRM</span>
                        </div>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Role Badge Banner -->
            <div class="px-4 py-3 bg-[#0c1424]/90 border-b border-slate-800/70">
                <div class="p-2.5 rounded-xl bg-slate-900/90 border border-slate-800/80 flex items-center justify-between shadow-xs">
                    <div class="flex items-center space-x-2.5 min-w-0">
                        <div class="relative flex items-center justify-center w-7 h-7 rounded-lg shrink-0 {{ Auth::user()->isAdmin() ? 'bg-amber-500/10 text-amber-400 border border-amber-500/25' : (Auth::user()->isSalesManager() ? 'bg-indigo-500/15 text-indigo-400 border border-indigo-500/30' : 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30') }}">
                            <i class="fa-solid {{ Auth::user()->isAdmin() ? 'fa-user-shield text-xs' : (Auth::user()->isSalesManager() ? 'fa-user-tie text-xs' : 'fa-motorcycle text-xs') }}"></i>
                        </div>
                        <div class="truncate">
                            <div class="text-xs font-bold text-white truncate">
                                @if(Auth::user()->isAdmin())
                                    Executive Admin
                                @elseif(Auth::user()->isSalesManager())
                                    Field Sales Lead (HOD)
                                @else
                                    Field Sales Rep
                                @endif
                            </div>
                            <div class="text-[10px] text-slate-400 truncate">{{ Auth::user()->territory ?: 'Lagos Central Operations' }}</div>
                        </div>
                    </div>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700/80 shrink-0 font-medium">
                        {{ Auth::user()->agent_code ?: (Auth::user()->isAdmin() ? 'HQ' : 'HOD') }}
                    </span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto text-xs">
                
                <!-- Main Section -->
                <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Core Navigation</div>

                <a href="{{ route('dashboard') }}" 
                   class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <div class="w-6 text-center mr-2.5 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-blue-400 group-hover:text-blue-300' }}">
                        <i class="fa-solid fa-gauge-high text-sm"></i>
                    </div>
                    <span class="flex-1">
                        @if(Auth::user()->isAdmin())
                            Executive Dashboard
                        @elseif(Auth::user()->isSalesManager())
                            Team Leadership Dashboard
                        @else
                            My Field Dashboard
                        @endif
                    </span>
                </a>

                @if(Auth::user()->isAdmin())
                    <!-- ================= ADMIN NAVIGATION ================= -->
                    <div class="pt-5 px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-amber-400/90 flex items-center justify-between">
                        <span>Executive Field Operations</span>
                        <span class="text-[9px] bg-amber-400/15 text-amber-300 px-1.5 py-0.2 rounded font-mono font-bold border border-amber-400/20">ADMIN</span>
                    </div>

                    <a href="{{ route('activities.daily') }}" 
                       class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('activities.daily') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="w-6 text-center mr-2.5 {{ request()->routeIs('activities.daily') ? 'text-white' : 'text-emerald-400 group-hover:text-emerald-300' }}">
                            <i class="fa-solid fa-calendar-day text-sm"></i>
                        </div>
                        <div class="flex-1 flex items-center justify-between">
                            <span>Daily Activity Tracker</span>
                            <span class="text-[10px] bg-emerald-500/20 text-emerald-300 px-1.5 py-0.5 rounded font-mono">By Date</span>
                        </div>
                    </a>

                    <a href="{{ route('activities.report') }}" 
                       class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('activities.report') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="w-6 text-center mr-2.5 {{ request()->routeIs('activities.report') ? 'text-white' : 'text-purple-400 group-hover:text-purple-300' }}">
                            <i class="fa-solid fa-chart-line text-sm"></i>
                        </div>
                        <span class="flex-1">Activity Reports & Audit</span>
                    </a>

                    <a href="{{ route('agents.index') }}" 
                       class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('agents.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="w-6 text-center mr-2.5 {{ request()->routeIs('agents.*') ? 'text-white' : 'text-indigo-400 group-hover:text-indigo-300' }}">
                            <i class="fa-solid fa-user-tie text-sm"></i>
                        </div>
                        <div class="flex-1 flex items-center justify-between">
                            <span>Field Executives</span>
                            <span class="text-[10px] bg-slate-800 text-slate-400 px-1.5 py-0.5 rounded border border-slate-700">Team</span>
                        </div>
                    </a>

                    <div class="pt-5 px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Distribution Network</div>

                    <a href="{{ route('customers.index') }}" 
                       class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('customers.index') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="w-6 text-center mr-2.5 {{ request()->routeIs('customers.index') ? 'text-white' : 'text-cyan-400 group-hover:text-cyan-300' }}">
                            <i class="fa-solid fa-shop text-sm"></i>
                        </div>
                        <div class="flex-1 flex items-center justify-between">
                            <span>Customers & Stores</span>
                            <span class="text-[10px] bg-slate-800 text-slate-400 px-1.5 py-0.5 rounded border border-slate-700">All Lagos</span>
                        </div>
                    </a>

                    <a href="{{ route('customers.import_form') }}" 
                       class="group flex items-center px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('customers.import*') ? 'bg-slate-800 text-emerald-400 font-semibold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200' }} ml-4">
                        <i class="fa-solid fa-file-excel mr-2 text-emerald-400 text-xs"></i>
                        <span>Import Excel / CSV</span>
                    </a>

                    <a href="{{ route('orders.index') }}" 
                       class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('orders.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="w-6 text-center mr-2.5 {{ request()->routeIs('orders.*') ? 'text-white' : 'text-amber-400 group-hover:text-amber-300' }}">
                            <i class="fa-solid fa-file-invoice-dollar text-sm"></i>
                        </div>
                        <span class="flex-1">Sales Orders & Waybills</span>
                    </a>

                    <a href="{{ route('payments.index') }}" 
                       class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('payments.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="w-6 text-center mr-2.5 {{ request()->routeIs('payments.*') ? 'text-white' : 'text-green-400 group-hover:text-green-300' }}">
                            <i class="fa-solid fa-money-bill-wave text-sm"></i>
                        </div>
                        <span class="flex-1">Collections & Receipts</span>
                    </a>

                    <a href="{{ route('products.index') }}" 
                       class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('products.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="w-6 text-center mr-2.5 {{ request()->routeIs('products.*') ? 'text-white' : 'text-sky-400 group-hover:text-sky-300' }}">
                            <i class="fa-solid fa-boxes-stacked text-sm"></i>
                        </div>
                        <span class="flex-1">Products & Inventory</span>
                    </a>

                @elseif(Auth::user()->isSalesManager())
                    <!-- ================= FIELD SALES LEAD / HOD NAVIGATION ================= -->
                    <div class="pt-5 px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-indigo-400/90 flex items-center justify-between">
                        <span>Field Team Management</span>
                        <span class="text-[9px] bg-indigo-500/20 text-indigo-300 px-1.5 py-0.5 rounded font-mono font-bold border border-indigo-500/30">HOD / LEAD</span>
                    </div>

                    <a href="{{ route('activities.daily') }}" 
                       class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('activities.daily') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="w-6 text-center mr-2.5 {{ request()->routeIs('activities.daily') ? 'text-white' : 'text-emerald-400 group-hover:text-emerald-300' }}">
                            <i class="fa-solid fa-calendar-day text-sm"></i>
                        </div>
                        <div class="flex-1 flex items-center justify-between">
                            <span>Daily Activity Tracker</span>
                            <span class="text-[10px] bg-emerald-500/20 text-emerald-300 px-1.5 py-0.5 rounded font-mono">Date Audit</span>
                        </div>
                    </a>

                    <a href="{{ route('activities.report') }}" 
                       class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('activities.report') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="w-6 text-center mr-2.5 {{ request()->routeIs('activities.report') ? 'text-white' : 'text-purple-400 group-hover:text-purple-300' }}">
                            <i class="fa-solid fa-chart-line text-sm"></i>
                        </div>
                        <span class="flex-1">Team Performance & Audit</span>
                    </a>

                    <a href="{{ route('agents.index') }}" 
                       class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('agents.index') || request()->routeIs('agents.show') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="w-6 text-center mr-2.5 {{ request()->routeIs('agents.index') ? 'text-white' : 'text-indigo-400 group-hover:text-indigo-300' }}">
                            <i class="fa-solid fa-users-gear text-sm"></i>
                        </div>
                        <div class="flex-1 flex items-center justify-between">
                            <span>Field Executives</span>
                            <span class="text-[10px] bg-slate-800 text-slate-400 px-1.5 py-0.5 rounded border border-slate-700">Team</span>
                        </div>
                    </a>

                    <a href="{{ route('agents.create') }}" 
                       class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('agents.create') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="w-6 text-center mr-2.5 {{ request()->routeIs('agents.create') ? 'text-white' : 'text-amber-400 group-hover:text-amber-300' }}">
                            <i class="fa-solid fa-user-plus text-sm"></i>
                        </div>
                        <div class="flex-1 flex items-center justify-between">
                            <span>Onboard New Agent</span>
                            <span class="text-[10px] bg-amber-500/20 text-amber-300 px-1.5 py-0.5 rounded font-mono">+ Add</span>
                        </div>
                    </a>

                    <div class="pt-5 px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Territory & Operations</div>

                    <a href="{{ route('customers.index') }}" 
                       class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('customers.index') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="w-6 text-center mr-2.5 {{ request()->routeIs('customers.index') ? 'text-white' : 'text-cyan-400 group-hover:text-cyan-300' }}">
                            <i class="fa-solid fa-shop text-sm"></i>
                        </div>
                        <div class="flex-1 flex items-center justify-between">
                            <span>Customers & Stores</span>
                            <span class="text-[10px] bg-slate-800 text-slate-400 px-1.5 py-0.5 rounded border border-slate-700">All Lagos</span>
                        </div>
                    </a>

                    <a href="{{ route('customers.import_form') }}" 
                       class="group flex items-center px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('customers.import*') ? 'bg-slate-800 text-emerald-400 font-semibold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200' }} ml-4">
                        <i class="fa-solid fa-file-excel mr-2 text-emerald-400 text-xs"></i>
                        <span>Import Excel / CSV</span>
                    </a>

                    <a href="{{ route('orders.index') }}" 
                       class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('orders.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="w-6 text-center mr-2.5 {{ request()->routeIs('orders.*') ? 'text-white' : 'text-amber-400 group-hover:text-amber-300' }}">
                            <i class="fa-solid fa-file-invoice-dollar text-sm"></i>
                        </div>
                        <span class="flex-1">Sales Orders & Waybills</span>
                    </a>

                    <a href="{{ route('payments.index') }}" 
                       class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('payments.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="w-6 text-center mr-2.5 {{ request()->routeIs('payments.*') ? 'text-white' : 'text-green-400 group-hover:text-green-300' }}">
                            <i class="fa-solid fa-money-bill-wave text-sm"></i>
                        </div>
                        <span class="flex-1">Collections & Receipts</span>
                    </a>

                    <a href="{{ route('products.index') }}" 
                       class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('products.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="w-6 text-center mr-2.5 {{ request()->routeIs('products.*') ? 'text-white' : 'text-sky-400 group-hover:text-sky-300' }}">
                            <i class="fa-solid fa-boxes-stacked text-sm"></i>
                        </div>
                        <span class="flex-1">Product Catalog & Stock</span>
                    </a>

                @else
                    <!-- ================= FIELD SALES AGENT NAVIGATION ================= -->
                    <div class="pt-5 px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-emerald-400/90 flex items-center justify-between">
                        <span>My Field Work</span>
                        <span class="text-[9px] bg-emerald-500/20 text-emerald-300 px-1.5 py-0.5 rounded font-mono font-bold border border-emerald-500/30">FIELD</span>
                    </div>

                    <a href="{{ route('activities.create_checkin') }}" 
                       class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('activities.create_checkin') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="w-6 text-center mr-2.5 {{ request()->routeIs('activities.create_checkin') ? 'text-white' : 'text-amber-400 group-hover:text-amber-300' }}">
                            <i class="fa-solid fa-location-crosshairs text-sm"></i>
                        </div>
                        <span class="flex-1">Log Field Check-In</span>
                    </a>

                    <a href="{{ route('activities.daily') }}" 
                       class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('activities.daily') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="w-6 text-center mr-2.5 {{ request()->routeIs('activities.daily') ? 'text-white' : 'text-emerald-400 group-hover:text-emerald-300' }}">
                            <i class="fa-solid fa-calendar-day text-sm"></i>
                        </div>
                        <span class="flex-1">My Daily Schedule</span>
                    </a>

                    <div class="pt-5 px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">My Territory & Clients</div>

                    <a href="{{ route('customers.index') }}" 
                       class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('customers.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="w-6 text-center mr-2.5 {{ request()->routeIs('customers.*') ? 'text-white' : 'text-cyan-400 group-hover:text-cyan-300' }}">
                            <i class="fa-solid fa-shop text-sm"></i>
                        </div>
                        <div class="flex-1 flex items-center justify-between">
                            <span>My Stores & Clients</span>
                            <span class="text-[10px] bg-emerald-500/20 text-emerald-400 px-1.5 py-0.5 rounded font-mono">Active</span>
                        </div>
                    </a>

                    <a href="{{ route('orders.index') }}" 
                       class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('orders.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="w-6 text-center mr-2.5 {{ request()->routeIs('orders.*') ? 'text-white' : 'text-amber-400 group-hover:text-amber-300' }}">
                            <i class="fa-solid fa-file-invoice-dollar text-sm"></i>
                        </div>
                        <span class="flex-1">My Sales Orders</span>
                    </a>

                    <a href="{{ route('payments.index') }}" 
                       class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('payments.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="w-6 text-center mr-2.5 {{ request()->routeIs('payments.*') ? 'text-white' : 'text-green-400 group-hover:text-green-300' }}">
                            <i class="fa-solid fa-money-bill-wave text-sm"></i>
                        </div>
                        <span class="flex-1">My Collections</span>
                    </a>

                    <a href="{{ route('products.index') }}" 
                       class="group flex items-center px-3 py-2.5 rounded-xl font-medium transition-all duration-150 {{ request()->routeIs('products.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <div class="w-6 text-center mr-2.5 {{ request()->routeIs('products.*') ? 'text-white' : 'text-sky-400 group-hover:text-sky-300' }}">
                            <i class="fa-solid fa-boxes-stacked text-sm"></i>
                        </div>
                        <span class="flex-1">Product Catalog & Stock</span>
                    </a>
                @endif
            </nav>

            <!-- User Footer Profile Card -->
            <div class="p-3 border-t border-slate-800/80 bg-[#060a12]/80">
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-900/90 border border-slate-800 shadow-sm">
                    <div class="flex items-center space-x-2.5 overflow-hidden">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="truncate">
                            <div class="text-xs font-semibold text-white truncate">{{ Auth::user()->name }}</div>
                            <div class="text-[10px] text-slate-400 truncate flex items-center space-x-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                <span>{{ Auth::user()->territory ?: 'Lagos Operations' }}</span>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Logout" class="text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 p-1.5 rounded-lg transition-colors">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-[#f8fafc]">

            <!-- Top Header Navbar -->
            <header class="glass-panel border-b border-slate-200/80 sticky top-0 z-30 shadow-xs no-print">
                <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
                    
                    <!-- Left: Mobile Trigger & Breadcrumbs -->
                    <div class="flex items-center space-x-3 min-w-0">
                        <button @click="sidebarOpen = true" class="lg:hidden text-slate-600 hover:text-slate-900 p-2 rounded-xl hover:bg-slate-100 transition-colors">
                            <i class="fa-solid fa-bars text-lg"></i>
                        </button>
                        <div class="truncate">
                            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-0.5">
                                <span>Reliable Electrics</span>
                                <span>/</span>
                                <span class="font-medium text-slate-600 truncate">@yield('header_title', 'Operations')</span>
                            </div>
                            <h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight truncate">@yield('header_title', 'Reliable Electrics CRM')</h1>
                        </div>
                    </div>

                    <!-- Right: Search Mockup, Live Status & Fast Role Switcher -->
                    <div class="flex items-center space-x-2 sm:space-x-3 shrink-0">
                        
                        <!-- Search Bar Mockup (Enterprise Feel) -->
                        <div class="hidden xl:flex items-center space-x-2 text-xs text-slate-400 bg-slate-100/80 hover:bg-slate-100 border border-slate-200/90 px-3 py-1.5 rounded-xl transition-colors cursor-pointer" onclick="document.querySelector('input[type=search], input[type=text]')?.focus()">
                            <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                            <span class="text-slate-500 font-medium">Quick search...</span>
                            <kbd class="text-[10px] bg-white text-slate-500 px-1.5 py-0.5 rounded border border-slate-200 font-mono shadow-2xs">Ctrl K</kbd>
                        </div>

                        <!-- Date Badge with Live Pulse -->
                        <div class="hidden sm:flex items-center space-x-2 px-3 py-1.5 bg-slate-100/90 rounded-xl text-xs font-semibold text-slate-700 border border-slate-200/80 shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>{{ Carbon\Carbon::today()->format('D, M d, Y') }}</span>
                        </div>

                        <!-- Enterprise Instant Role Switcher Dropdown -->
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" 
                                    class="inline-flex items-center space-x-2 text-xs font-semibold px-3 py-1.5 rounded-xl border border-slate-200/90 bg-white hover:bg-slate-50 text-slate-800 shadow-xs hover:border-slate-300 transition-all">
                                <div class="w-6 h-6 rounded-md {{ Auth::user()->isAdmin() ? 'bg-amber-100 text-amber-700' : (Auth::user()->isSalesManager() ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700') }} flex items-center justify-center font-bold text-[10px] shrink-0">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <div class="text-left hidden md:block leading-tight">
                                    <div class="text-[11px] font-bold text-slate-900 truncate max-w-[130px]">{{ Auth::user()->name }}</div>
                                    <div class="text-[9px] text-slate-400 font-medium uppercase tracking-wide">
                                        {{ Auth::user()->isAdmin() ? 'Executive MD' : (Auth::user()->isSalesManager() ? 'Field Lead' : 'Field Rep') }}
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                            </button>

                            <!-- Role Switcher Panel -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="transform opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="transform opacity-0 scale-95 -translate-y-1"
                                 class="absolute right-0 mt-2 w-80 rounded-2xl bg-white shadow-2xl border border-slate-200/90 py-2 z-50 divide-y divide-slate-100 text-xs" 
                                 x-cloak>
                                
                                <div class="px-4 py-2.5 bg-slate-50/70 flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Enterprise Persona Switcher</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-blue-50 text-blue-600 border border-blue-200">1-Click Test</span>
                                </div>

                                <div class="py-1 max-h-96 overflow-y-auto">
                                    @php
                                        $switchUsers = \App\Models\User::orderByRaw("CASE WHEN role = 'admin' THEN 1 WHEN role = 'sales_manager' THEN 2 ELSE 3 END")->orderBy('id')->get();
                                    @endphp

                                    @foreach($switchUsers as $su)
                                        <a href="{{ route('quick-login', $su) }}" 
                                           class="flex items-center justify-between px-3.5 py-2.5 hover:bg-slate-50 transition-colors {{ $su->id === Auth::id() ? 'bg-blue-50/70 font-semibold' : '' }}">
                                            <div class="flex items-center space-x-2.5 truncate">
                                                <div class="w-7 h-7 rounded-lg {{ $su->isAdmin() ? 'bg-amber-100 text-amber-700' : ($su->isSalesManager() ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700') }} flex items-center justify-center font-bold text-xs shrink-0">
                                                    {{ substr($su->name, 0, 1) }}
                                                </div>
                                                <div class="truncate">
                                                    <div class="text-xs font-bold text-slate-800 truncate flex items-center space-x-1.5">
                                                        <span>{{ $su->name }}</span>
                                                        @if($su->id === Auth::id())
                                                            <i class="fa-solid fa-circle-check text-blue-600 text-[10px]"></i>
                                                        @endif
                                                    </div>
                                                    <div class="text-[10px] text-slate-400 truncate">
                                                        @if($su->isAdmin())
                                                            Managing Director (Admin)
                                                        @elseif($su->isSalesManager())
                                                            Field Sales Lead (HOD)
                                                        @else
                                                            {{ $su->territory ?: 'Field Sales Rep' }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="text-[10px] font-mono font-semibold px-1.5 py-0.5 rounded shrink-0 ml-2 {{ $su->isAdmin() ? 'bg-amber-50 text-amber-700 border border-amber-200' : ($su->isSalesManager() ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-600 border border-slate-200') }}">
                                                {{ $su->agent_code ?: ($su->isAdmin() ? 'HQ-MD' : 'LEAD') }}
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Quick Action Primary Button -->
                        <a href="{{ route('customers.create') }}" 
                           class="inline-flex items-center space-x-1.5 text-xs font-bold px-3.5 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-md shadow-blue-500/25 hover:shadow-lg hover:shadow-blue-500/35 transition-all">
                            <i class="fa-solid fa-plus text-[11px]"></i>
                            <span class="hidden sm:inline">Onboard Customer</span>
                        </a>

                    </div>
                </div>
            </header>

            <!-- Flash Notifications -->
            <div class="px-4 sm:px-6 lg:px-8 pt-4">
                @if(session('success'))
                    <div class="rounded-2xl bg-emerald-50/90 border border-emerald-200 p-4 mb-4 flex items-start justify-between text-emerald-900 shadow-xs" x-data="{ show: true }" x-show="show">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fa-solid fa-check text-sm"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold uppercase tracking-wider text-emerald-700">Success</div>
                                <div class="text-xs font-medium text-emerald-900 mt-0.5">{{ session('success') }}</div>
                            </div>
                        </div>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-800 p-1">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="rounded-2xl bg-blue-50/90 border border-blue-200 p-4 mb-4 flex items-start justify-between text-blue-900 shadow-xs" x-data="{ show: true }" x-show="show">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fa-solid fa-info text-sm"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold uppercase tracking-wider text-blue-700">Information</div>
                                <div class="text-xs font-medium text-blue-900 mt-0.5">{{ session('info') }}</div>
                            </div>
                        </div>
                        <button @click="show = false" class="text-blue-500 hover:text-blue-800 p-1">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="rounded-2xl bg-rose-50/90 border border-rose-200 p-4 mb-4 text-rose-900 shadow-xs" x-data="{ show: true }" x-show="show">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-xs font-bold uppercase tracking-wider text-rose-700">Form Validation Notice</div>
                                    <ul class="list-disc list-inside text-xs space-y-0.5 mt-1 text-rose-800">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button @click="show = false" class="text-rose-500 hover:text-rose-800 p-1">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Page Body Content -->
            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
                @yield('content')
            </main>

            <!-- Bottom Mobile Navigation Bar for Field Agents -->
            <div class="lg:hidden fixed bottom-0 inset-x-0 glass-panel border-t border-slate-200/90 py-2 px-4 flex items-center justify-around z-30 shadow-xl no-print">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center text-xs {{ request()->routeIs('dashboard') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                    <i class="fa-solid fa-gauge-high text-base"></i>
                    <span class="text-[10px] mt-0.5">Dashboard</span>
                </a>
                <a href="{{ route('activities.daily') }}" class="flex flex-col items-center text-xs {{ request()->routeIs('activities.daily') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                    <i class="fa-solid fa-calendar-day text-base"></i>
                    <span class="text-[10px] mt-0.5">Daily Log</span>
                </a>
                <a href="{{ route('activities.create_checkin') }}" class="flex flex-col items-center justify-center -mt-5">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center shadow-lg shadow-blue-500/40">
                        <i class="fa-solid fa-location-crosshairs text-lg"></i>
                    </div>
                    <span class="text-[10px] font-bold text-blue-600 mt-0.5">Check-In</span>
                </a>
                <a href="{{ route('customers.index') }}" class="flex flex-col items-center text-xs {{ request()->routeIs('customers.*') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                    <i class="fa-solid fa-shop text-base"></i>
                    <span class="text-[10px] mt-0.5">Stores</span>
                </a>
                <a href="{{ route('orders.index') }}" class="flex flex-col items-center text-xs {{ request()->routeIs('orders.*') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                    <i class="fa-solid fa-file-invoice-dollar text-base"></i>
                    <span class="text-[10px] mt-0.5">Orders</span>
                </a>
            </div>

            <!-- Global Footer -->
            <footer class="bg-white border-t border-slate-200/80 py-4 px-4 sm:px-6 lg:px-8 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between no-print mb-14 lg:mb-0">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>&copy; {{ date('Y') }} <strong class="text-slate-800">Reliable Electrics Nigeria Limited</strong>. Enterprise Commercial Distribution CRM.</span>
                </div>
                <div class="mt-2 sm:mt-0 flex items-center space-x-3 text-slate-400 text-[11px]">
                    <span>Phase 1, 32B Oguntona Crescent, Gbagada, Lagos</span>
                </div>
            </footer>

        </div>
    </div>

    @stack('scripts')
</body>
</html>
