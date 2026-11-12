<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Reliable Electrics CRM') - Field Sales & Enterprise Distribution</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#0f172a',
                        },
                        accent: {
                            500: '#f59e0b',
                            600: '#d97706',
                        }
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
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: #fff !important; }
        }
    </style>
    @stack('styles')
</head>
<body class="h-full font-sans antialiased text-slate-800" x-data="{ sidebarOpen: false, userMenuOpen: false }">
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
             class="fixed inset-0 bg-slate-900/60 z-40 lg:hidden" x-cloak></div>

        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed lg:static inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-200 flex flex-col transition-transform duration-200 ease-in-out border-r border-slate-800 shadow-xl lg:shadow-none no-print">
            
            <!-- Brand Header -->
            <div class="p-5 flex items-center justify-between border-b border-slate-800/80 bg-slate-950/40">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                        <i class="fa-solid fa-bolt text-lg"></i>
                    </div>
                    <div>
                        <div class="font-bold text-white tracking-tight leading-tight text-base">Reliable Electrics</div>
                        <div class="text-[11px] text-blue-400 font-medium uppercase tracking-wider">Enterprise CRM</div>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Role Badge Banner -->
            <div class="px-5 py-3 bg-slate-800/40 border-b border-slate-800/60 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full {{ Auth::user()->isAdmin() ? 'bg-amber-400 animate-pulse' : 'bg-emerald-400' }}"></span>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-300">
                        {{ Auth::user()->isAdmin() ? 'Administrator' : 'Field Sales Rep' }}
                    </span>
                </div>
                <span class="text-[11px] bg-slate-800 text-slate-400 px-2 py-0.5 rounded border border-slate-700">
                    {{ Auth::user()->agent_code ?: 'HQ' }}
                </span>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto text-sm">
                <!-- Main Section -->
                <div class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Main</div>

                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-gauge w-5 text-center mr-3 text-base {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400' }}"></i>
                    {{ Auth::user()->isAdmin() ? 'Executive Dashboard' : 'My Field Dashboard' }}
                </a>

                @if(Auth::user()->isAdmin())
                    <!-- ================= ADMIN NAVIGATION ================= -->
                    <div class="pt-5 px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-amber-400/90 flex items-center justify-between">
                        <span>Field Operations</span>
                        <span class="text-[9px] bg-amber-400/20 text-amber-300 px-1 py-0.2 rounded font-mono font-bold">ADMIN</span>
                    </div>

                    <a href="{{ route('activities.daily') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('activities.daily') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i class="fa-solid fa-calendar-day w-5 text-center mr-3 text-base {{ request()->routeIs('activities.daily') ? 'text-white' : 'text-emerald-400' }}"></i>
                        <div class="flex-1 flex items-center justify-between">
                            <span>Daily Activity Tracker</span>
                            <span class="text-[10px] bg-emerald-500/20 text-emerald-400 px-1.5 py-0.5 rounded font-mono">By Date</span>
                        </div>
                    </a>

                    <a href="{{ route('activities.report') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('activities.report') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i class="fa-solid fa-chart-line w-5 text-center mr-3 text-base {{ request()->routeIs('activities.report') ? 'text-white' : 'text-blue-400' }}"></i>
                        Activity Reports & Audit
                    </a>

                    <a href="{{ route('agents.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('agents.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i class="fa-solid fa-user-tie w-5 text-center mr-3 text-base {{ request()->routeIs('agents.*') ? 'text-white' : 'text-purple-400' }}"></i>
                        <div class="flex-1 flex items-center justify-between">
                            <span>Field Executives</span>
                            <span class="text-[10px] bg-slate-800 text-slate-400 px-1.5 py-0.5 rounded border border-slate-700">Team</span>
                        </div>
                    </a>

                    <div class="pt-5 px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Distribution Network</div>

                    <a href="{{ route('customers.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('customers.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i class="fa-solid fa-shop w-5 text-center mr-3 text-base {{ request()->routeIs('customers.*') ? 'text-white' : 'text-cyan-400' }}"></i>
                        <div class="flex-1 flex items-center justify-between">
                            <span>Customers & Stores</span>
                            <span class="text-[10px] bg-slate-800 text-slate-400 px-1.5 py-0.5 rounded border border-slate-700">All Lagos</span>
                        </div>
                    </a>

                    <a href="{{ route('orders.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('orders.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i class="fa-solid fa-file-invoice-dollar w-5 text-center mr-3 text-base {{ request()->routeIs('orders.*') ? 'text-white' : 'text-emerald-400' }}"></i>
                        Sales Orders & Waybills
                    </a>

                    <a href="{{ route('payments.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('payments.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i class="fa-solid fa-money-bill-wave w-5 text-center mr-3 text-base {{ request()->routeIs('payments.*') ? 'text-white' : 'text-green-400' }}"></i>
                        Collections & Receipts
                    </a>

                    <a href="{{ route('products.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('products.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i class="fa-solid fa-boxes-stacked w-5 text-center mr-3 text-base {{ request()->routeIs('products.*') ? 'text-white' : 'text-yellow-400' }}"></i>
                        Products & Inventory
                    </a>

                @else
                    <!-- ================= FIELD SALES AGENT NAVIGATION ================= -->
                    <div class="pt-5 px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-emerald-400/90 flex items-center justify-between">
                        <span>My Field Work</span>
                        <span class="text-[9px] bg-emerald-400/20 text-emerald-300 px-1 py-0.2 rounded font-mono font-bold">FIELD</span>
                    </div>

                    <a href="{{ route('activities.create_checkin') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('activities.create_checkin') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i class="fa-solid fa-location-crosshairs w-5 text-center mr-3 text-base {{ request()->routeIs('activities.create_checkin') ? 'text-white' : 'text-amber-400' }}"></i>
                        Log Field Check-In
                    </a>

                    <a href="{{ route('activities.daily') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('activities.daily') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i class="fa-solid fa-calendar-day w-5 text-center mr-3 text-base {{ request()->routeIs('activities.daily') ? 'text-white' : 'text-emerald-400' }}"></i>
                        My Daily Schedule
                    </a>

                    <div class="pt-5 px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">My Territory & Clients</div>

                    <a href="{{ route('customers.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('customers.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i class="fa-solid fa-shop w-5 text-center mr-3 text-base {{ request()->routeIs('customers.*') ? 'text-white' : 'text-cyan-400' }}"></i>
                        <div class="flex-1 flex items-center justify-between">
                            <span>My Stores & Clients</span>
                            <span class="text-[10px] bg-emerald-500/20 text-emerald-400 px-1.5 py-0.5 rounded font-mono">Active</span>
                        </div>
                    </a>

                    <a href="{{ route('orders.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('orders.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i class="fa-solid fa-file-invoice-dollar w-5 text-center mr-3 text-base {{ request()->routeIs('orders.*') ? 'text-white' : 'text-emerald-400' }}"></i>
                        My Sales Orders
                    </a>

                    <a href="{{ route('payments.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('payments.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i class="fa-solid fa-money-bill-wave w-5 text-center mr-3 text-base {{ request()->routeIs('payments.*') ? 'text-white' : 'text-green-400' }}"></i>
                        My Collections
                    </a>

                    <a href="{{ route('products.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('products.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                        <i class="fa-solid fa-boxes-stacked w-5 text-center mr-3 text-base {{ request()->routeIs('products.*') ? 'text-white' : 'text-yellow-400' }}"></i>
                        Product Catalog & Stock
                    </a>
                @endif
            </nav>

            <!-- User Footer Profile -->
            <div class="p-3 border-t border-slate-800/80 bg-slate-950/60">
                <div class="flex items-center justify-between p-2 rounded-lg bg-slate-900/80 border border-slate-800">
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <div class="w-9 h-9 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-sm shrink-0">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="truncate">
                            <div class="text-xs font-semibold text-white truncate">{{ Auth::user()->name }}</div>
                            <div class="text-[11px] text-slate-400 truncate">{{ Auth::user()->territory ?: 'Lagos Operations' }}</div>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Logout" class="text-slate-400 hover:text-red-400 p-1.5 rounded transition-colors">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50">

            <!-- Top Header Navbar -->
            <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs no-print">
                <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                    
                    <div class="flex items-center space-x-3">
                        <button @click="sidebarOpen = true" class="lg:hidden text-slate-600 hover:text-slate-900 p-2 rounded-lg">
                            <i class="fa-solid fa-bars text-lg"></i>
                        </button>
                        <div>
                            <h1 class="text-lg font-bold text-slate-900 leading-tight">@yield('header_title', 'Reliable Electrics CRM')</h1>
                            <p class="text-xs text-slate-500 hidden sm:block">Gbagada Phase 1, Lagos • Field Operations Platform</p>
                        </div>
                    </div>

                    <!-- Header Actions & Fast Role Impersonation Switcher -->
                    <div class="flex items-center space-x-3">
                        
                        <!-- Quick Role Switcher Dropdown (Essential for testing all roles) -->
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" 
                                    class="inline-flex items-center space-x-2 text-xs font-medium px-3 py-1.5 rounded-lg border border-slate-300 bg-slate-50 hover:bg-slate-100 text-slate-700 shadow-xs transition-colors">
                                <i class="fa-solid fa-shuffle text-blue-600"></i>
                                <span class="hidden md:inline">Switch Role:</span>
                                <span class="font-bold text-slate-900">{{ Auth::user()->name }}</span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                            </button>

                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-72 rounded-xl bg-white shadow-2xl border border-slate-200 py-2 z-50 divide-y divide-slate-100" 
                                 x-cloak>
                                <div class="px-3 py-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    Instant Role Switcher
                                </div>
                                <div class="py-1">
                                    @php
                                        $switchUsers = \App\Models\User::orderByRaw("CASE WHEN role = 'admin' THEN 1 ELSE 2 END")->orderBy('id')->get();
                                    @endphp
                                    @foreach($switchUsers as $su)
                                        <a href="{{ route('quick-login', $su) }}" 
                                           class="flex items-center justify-between px-3 py-2 text-xs hover:bg-blue-50 {{ $su->id === Auth::id() ? 'bg-blue-50/60 font-semibold text-blue-700' : 'text-slate-700' }}">
                                            <div class="flex items-center space-x-2 truncate">
                                                <span class="w-2 h-2 rounded-full {{ $su->isAdmin() ? 'bg-amber-400' : 'bg-emerald-400' }}"></span>
                                                <span class="truncate">{{ $su->name }}</span>
                                            </div>
                                            <span class="text-[10px] text-slate-400 shrink-0 ml-2">
                                                {{ $su->agent_code ?: 'Admin' }}
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Date Badge -->
                        <div class="hidden md:flex items-center space-x-1.5 px-3 py-1.5 bg-slate-100 rounded-lg text-xs font-medium text-slate-600 border border-slate-200">
                            <i class="fa-regular fa-clock text-slate-400"></i>
                            <span>{{ Carbon\Carbon::today()->format('D, M d, Y') }}</span>
                        </div>

                        <!-- Quick Onboard Customer Button -->
                        <a href="{{ route('customers.create') }}" 
                           class="inline-flex items-center space-x-1.5 text-xs font-semibold px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-sm transition-colors">
                            <i class="fa-solid fa-plus"></i>
                            <span class="hidden sm:inline">Onboard Customer</span>
                        </a>

                    </div>
                </div>
            </header>

            <!-- Flash Notifications -->
            <div class="px-4 sm:px-6 lg:px-8 pt-4">
                @if(session('success'))
                    <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 mb-4 flex items-start space-x-3 text-emerald-800 shadow-xs">
                        <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 text-base"></i>
                        <div class="text-sm font-medium">{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('info'))
                    <div class="rounded-xl bg-blue-50 border border-blue-200 p-4 mb-4 flex items-start space-x-3 text-blue-800 shadow-xs">
                        <i class="fa-solid fa-circle-info text-blue-500 mt-0.5 text-base"></i>
                        <div class="text-sm font-medium">{{ session('info') }}</div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="rounded-xl bg-red-50 border border-red-200 p-4 mb-4 text-red-800 shadow-xs">
                        <div class="flex items-center space-x-2 font-semibold text-sm mb-1">
                            <i class="fa-solid fa-triangle-exclamation text-red-500"></i>
                            <span>Please check the following errors:</span>
                        </div>
                        <ul class="list-disc list-inside text-xs space-y-0.5 ml-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Page Body Content -->
            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
                @yield('content')
            </main>

            <!-- Bottom Mobile Navigation Bar for Field Agents -->
            <div class="lg:hidden fixed bottom-0 inset-x-0 bg-white border-t border-slate-200 py-2 px-4 flex items-center justify-around z-30 shadow-lg no-print">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center text-xs {{ request()->routeIs('dashboard') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">
                    <i class="fa-solid fa-gauge text-base"></i>
                    <span class="text-[10px] mt-0.5">Home</span>
                </a>
                <a href="{{ route('activities.daily') }}" class="flex flex-col items-center text-xs {{ request()->routeIs('activities.daily') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">
                    <i class="fa-solid fa-calendar-day text-base"></i>
                    <span class="text-[10px] mt-0.5">Daily Log</span>
                </a>
                <a href="{{ route('activities.create_checkin') }}" class="flex flex-col items-center justify-center -mt-5">
                    <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-500/40">
                        <i class="fa-solid fa-location-dot text-lg"></i>
                    </div>
                    <span class="text-[10px] font-semibold text-blue-600 mt-0.5">Check-In</span>
                </a>
                <a href="{{ route('customers.index') }}" class="flex flex-col items-center text-xs {{ request()->routeIs('customers.*') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">
                    <i class="fa-solid fa-shop text-base"></i>
                    <span class="text-[10px] mt-0.5">Customers</span>
                </a>
                <a href="{{ route('orders.index') }}" class="flex flex-col items-center text-xs {{ request()->routeIs('orders.*') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">
                    <i class="fa-solid fa-file-invoice-dollar text-base"></i>
                    <span class="text-[10px] mt-0.5">Orders</span>
                </a>
            </div>

            <!-- Global Footer -->
            <footer class="bg-white border-t border-slate-200 py-4 px-4 sm:px-6 lg:px-8 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between no-print mb-14 lg:mb-0">
                <div>
                    &copy; {{ date('Y') }} <strong class="text-slate-700">Reliable Electrics Nigeria Limited</strong>. Enterprise Field Sales & Commercial Distribution CRM.
                </div>
                <div class="mt-2 sm:mt-0 flex items-center space-x-3 text-slate-400">
                    <span>Phase 1, 32B Oguntona Crescent, Gbagada, Lagos</span>
                </div>
            </footer>

        </div>
    </div>

    @stack('scripts')
</body>
</html>

