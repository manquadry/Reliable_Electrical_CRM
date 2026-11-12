<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterprise Sign In - Reliable Electrics Nigeria Limited CRM</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#1b5eed',
                            700: '#1548be',
                            800: '#143d99',
                            900: '#153578',
                            950: '#0e204c',
                        },
                    },
                    boxShadow: {
                        'glow': '0 0 25px -5px rgba(27, 94, 237, 0.45)',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        .mesh-gradient {
            background-color: #050811;
            background-image: 
                radial-gradient(at 15% 15%, rgba(27, 94, 237, 0.22) 0px, transparent 55%),
                radial-gradient(at 85% 85%, rgba(99, 102, 241, 0.18) 0px, transparent 55%),
                radial-gradient(at 50% 50%, rgba(15, 23, 42, 0.9) 0px, transparent 100%);
        }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-100 mesh-gradient flex flex-col justify-center py-10 px-4 sm:px-6 lg:px-8 selection:bg-brand-600 selection:text-white">

    <div class="sm:mx-auto sm:w-full sm:max-w-xl text-center">
        <!-- Glowing Lightning Emblem -->
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-brand-600 via-indigo-600 to-sky-400 shadow-glow text-white mb-3 ring-1 ring-white/20">
            <i class="fa-solid fa-bolt text-2xl animate-pulse"></i>
        </div>
        <div class="flex items-center justify-center space-x-2 text-[11px] font-bold uppercase tracking-wider text-brand-400 mb-1">
            <span>Enterprise Field CRM</span>
            <span class="w-1 h-1 rounded-full bg-brand-400"></span>
            <span>Commercial Distribution</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
            Reliable Electrics Nigeria Ltd
        </h1>
        <p class="text-xs text-slate-400 mt-1">
            Phase 1, 32B Oguntona Crescent, Gbagada, Lagos State
        </p>
    </div>

    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-xl">
        <div class="bg-slate-900/90 backdrop-blur-xl py-7 px-5 sm:px-8 shadow-2xl rounded-2xl border border-slate-800/90 ring-1 ring-white/5">
            
            @if(session('info'))
                <div class="mb-5 p-3.5 rounded-xl bg-brand-950/60 border border-brand-700/50 text-brand-200 text-xs flex items-center space-x-3">
                    <i class="fa-solid fa-circle-info text-brand-400 text-sm shrink-0"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-rose-950/60 border border-rose-700/50 text-rose-200 text-xs flex items-start space-x-3">
                    <i class="fa-solid fa-triangle-exclamation text-rose-400 text-sm shrink-0 mt-0.5"></i>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Standard Manual Credentials Form -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300">Work Email Address</label>
                    <div class="mt-1.5 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-envelope text-sm"></i>
                        </div>
                        <input id="email" name="email" type="email" required value="{{ old('email') }}"
                               placeholder="e.g. adekunle@reliableelectrics.ng"
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-950/80 border border-slate-700/80 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition-all shadow-inner font-mono">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300">Password</label>
                    <div class="mt-1.5 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>
                        <input id="password" name="password" type="password" required
                               placeholder="••••••••"
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-950/80 border border-slate-700/80 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition-all shadow-inner font-mono">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center space-x-2 text-slate-400 cursor-pointer">
                        <input type="checkbox" name="remember" checked class="rounded bg-slate-950 border-slate-700 text-brand-600 focus:ring-brand-500 focus:ring-offset-slate-900">
                        <span>Keep me signed in</span>
                    </label>
                    <span class="text-slate-500 font-mono text-[11px]">Default: <span class="text-brand-400">password</span></span>
                </div>

                <button type="submit" 
                        class="w-full py-3 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 shadow-lg shadow-brand-600/30 hover:shadow-brand-600/50 transition-all flex items-center justify-center space-x-2 group">
                    <span>Sign In With Credentials</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

            <!-- 4. 1-Click Directory List -->
            <div class="mt-7 pt-5 border-t border-slate-800">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-fingerprint text-brand-400 text-xs"></i>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-300">Complete Seeded Directory (One-Click)</span>
                    </div>
                    <span class="text-[10px] text-brand-400 bg-brand-500/10 px-2 py-0.5 rounded-full border border-brand-500/20 font-bold uppercase tracking-wider">
                        6 Stakeholders
                    </span>
                </div>

                <div class="space-y-2">
                    @foreach($demoUsers as $user)
                        <a href="{{ route('quick-login', $user) }}" 
                           class="flex items-center justify-between p-3 rounded-xl bg-slate-950/60 hover:bg-slate-800/80 border border-slate-800/80 hover:border-brand-500/50 transition-all text-xs group ring-1 ring-white/5">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-xl {{ $user->isAdmin() ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : ($user->isSalesManager() ? 'bg-indigo-500/15 text-indigo-400 border border-indigo-500/30' : 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30') }} flex items-center justify-center font-bold text-sm shrink-0">
                                    <i class="fa-solid {{ $user->isAdmin() ? 'fa-user-shield' : ($user->isSalesManager() ? 'fa-user-tie' : 'fa-motorcycle') }}"></i>
                                </div>
                                <div class="text-left">
                                    <div class="font-bold text-white group-hover:text-brand-400 transition-colors flex items-center space-x-1.5">
                                        <span>{{ $user->name }}</span>
                                        @if($user->isAdmin())
                                            <span class="text-[9px] bg-amber-500/20 text-amber-300 px-1.5 py-0.2 rounded font-semibold uppercase">Executive</span>
                                        @elseif($user->isSalesManager())
                                            <span class="text-[9px] bg-indigo-500/20 text-indigo-300 px-1.5 py-0.2 rounded font-semibold uppercase">HOD / Lead</span>
                                        @else
                                            <span class="text-[9px] bg-emerald-500/20 text-emerald-300 px-1.5 py-0.2 rounded font-semibold uppercase">Field Rep</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-mono">
                                        {{ $user->email }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 shrink-0">
                                <span class="text-[10px] bg-slate-900 border border-slate-700/60 text-slate-300 px-2 py-0.5 rounded font-mono font-medium">
                                    {{ $user->agent_code ?: ($user->isAdmin() ? 'EXEC-01' : 'HOD-01') }}
                                </span>
                                <i class="fa-solid fa-chevron-right text-slate-500 text-[10px] group-hover:text-brand-400 group-hover:translate-x-0.5 transition-all"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Enterprise Trust Badges -->
            <div class="mt-6 pt-5 border-t border-slate-800/80 flex items-center justify-around text-[10px] text-slate-400">
                <div class="flex items-center space-x-1.5">
                    <i class="fa-solid fa-shield-halved text-emerald-400"></i>
                    <span>256-Bit SSL</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <i class="fa-solid fa-satellite-dish text-brand-400"></i>
                    <span>Live GPS Audited</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <i class="fa-solid fa-building text-slate-400"></i>
                    <span>Lagos HQ Enforced</span>
                </div>
            </div>

        </div>

        <p class="text-center text-xs text-slate-500 mt-6 font-medium">
            &copy; {{ date('Y') }} Reliable Electrics Nigeria Limited • Premium Lighting, Switchgear & Commercial Distribution
        </p>
    </div>

</body>
</html>
