<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Reliable Electrics Nigeria Limited CRM</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<body class="h-full font-sans antialiased text-slate-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <!-- Logo Badge -->
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 shadow-xl shadow-blue-500/30 text-white mb-4">
            <i class="fa-solid fa-bolt text-3xl"></i>
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
            Reliable Electrics Nigeria Ltd
        </h2>
        <p class="mt-1 text-sm text-blue-400 font-medium">
            Enterprise Field Sales & Distribution CRM
        </p>
        <p class="text-xs text-slate-400 mt-0.5">
            Phase 1, 32B Oguntona Crescent, Gbagada, Lagos
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-lg px-4">
        <div class="bg-slate-800/90 py-8 px-6 shadow-2xl rounded-2xl border border-slate-700/80 sm:px-10 backdrop-blur-sm">
            
            @if(session('info'))
                <div class="mb-5 p-3 rounded-xl bg-blue-900/50 border border-blue-700/60 text-blue-200 text-xs flex items-center space-x-2">
                    <i class="fa-solid fa-circle-info text-blue-400"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3 rounded-xl bg-red-900/50 border border-red-700/60 text-red-200 text-xs">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Standard Credentials Form -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">Email Address</label>
                    <div class="mt-1.5 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-envelope text-sm"></i>
                        </div>
                        <input id="email" name="email" type="email" required value="{{ old('email', 'admin@reliableelectrics.ng') }}"
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-900/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">Password</label>
                    <div class="mt-1.5 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>
                        <input id="password" name="password" type="password" required value="password"
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-900/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center space-x-2 text-slate-400">
                        <input type="checkbox" name="remember" checked class="rounded bg-slate-900 border-slate-700 text-blue-600 focus:ring-blue-500">
                        <span>Remember my device</span>
                    </label>
                    <span class="text-slate-500">Default pwd: <code class="text-blue-400">password</code></span>
                </div>

                <button type="submit" 
                        class="w-full py-2.5 px-4 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 shadow-lg shadow-blue-600/30 transition-all flex items-center justify-center space-x-2">
                    <span>Sign In to CRM</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>

            <!-- Quick Demo One-Click Login -->
            <div class="mt-8 pt-6 border-t border-slate-700/80">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">One-Click Quick Login</span>
                    <span class="text-[11px] text-amber-400 bg-amber-400/10 px-2 py-0.5 rounded border border-amber-400/20 font-medium">Instant Test</span>
                </div>

                <div class="space-y-2">
                    @foreach($demoUsers as $user)
                        <a href="{{ route('quick-login', $user) }}" 
                           class="flex items-center justify-between p-3 rounded-xl bg-slate-900/60 hover:bg-slate-700/70 border border-slate-700/60 hover:border-blue-500/50 transition-all text-xs group">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg {{ $user->isAdmin() ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : ($user->isSalesManager() ? 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/30' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30') }} flex items-center justify-center font-bold">
                                    <i class="fa-solid {{ $user->isAdmin() ? 'fa-user-shield' : ($user->isSalesManager() ? 'fa-user-tie' : 'fa-motorcycle') }}"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-white group-hover:text-blue-400 transition-colors">
                                        {{ $user->name }}
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        @if($user->isAdmin())
                                            Managing Director (Admin)
                                        @elseif($user->isSalesManager())
                                            Field Sales Lead (HOD)
                                        @else
                                            {{ $user->territory ?: 'Field Sales Rep' }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-[10px] bg-slate-800 text-slate-400 px-2 py-0.5 rounded font-mono">
                                    {{ $user->agent_code ?: ($user->isAdmin() ? 'ADMIN' : 'HOD') }}
                                </span>
                                <i class="fa-solid fa-chevron-right text-slate-500 text-[10px] group-hover:text-white transition-colors"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

        </div>

        <p class="text-center text-xs text-slate-500 mt-6">
            Reliable Electrics Nigeria Limited • Distributor of Premium Lighting, Switchgear & Appliances
        </p>
    </div>

</body>
</html>

