<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RIMzone — Kirish</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        .bg-pattern {
            background-color: #0f172a;
            background-image:
                radial-gradient(ellipse 80% 60% at 50% -10%, rgba(37,99,235,0.18) 0%, transparent 65%),
                radial-gradient(ellipse 50% 40% at 90% 100%, rgba(99,102,241,0.12) 0%, transparent 60%);
        }

        .input-field {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: #f1f5f9;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .input-field::placeholder { color: rgba(148,163,184,0.6); }
        .input-field:focus {
            outline: none;
            border-color: rgba(59,130,246,0.7);
            background: rgba(255,255,255,0.08);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        }
        .input-field:-webkit-autofill,
        .input-field:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px #1e293b inset !important;
            -webkit-text-fill-color: #f1f5f9 !important;
        }

        .card-glow {
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.06),
                0 20px 60px rgba(0,0,0,0.5),
                0 8px 24px rgba(0,0,0,0.3);
        }

        .btn-login {
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            box-shadow: 0 4px 20px rgba(37,99,235,0.35);
            transition: all 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(37,99,235,0.45);
        }
        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 2px 10px rgba(37,99,235,0.3);
        }
        .btn-login:disabled {
            opacity: 0.6;
            transform: none;
            cursor: not-allowed;
        }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-in { animation: fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
    </style>
</head>
<body class="bg-pattern min-h-screen flex items-center justify-center p-4" x-data="{ showPass: false, loading: false }">

    <div class="w-full max-w-sm animate-in">

        <!-- Logo -->
        <div class="flex flex-col items-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30 mb-4">
                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                    <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">RIMzone</h1>
            <p class="text-slate-400 text-sm mt-1">Boshqaruv tizimiga kirish</p>
        </div>

        <!-- Card -->
        <div class="bg-slate-800/60 backdrop-blur-xl rounded-2xl p-7 card-glow">

            <!-- Error messages -->
            @if($errors->any())
            <div class="mb-5 p-3.5 rounded-xl bg-red-500/10 border border-red-500/20 flex items-start gap-3">
                <svg class="w-4 h-4 text-red-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    @foreach($errors->all() as $error)
                    <p class="text-red-400 text-sm leading-snug">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
            @endif

            <form method="POST" action="{{ url('/login') }}" @submit="loading = true">
                @csrf

                <!-- Email -->
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                        Email
                    </label>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="email@example.com"
                           required
                           autocomplete="email"
                           class="input-field w-full px-4 py-3 rounded-xl text-sm font-medium">
                </div>

                <!-- Password -->
                <div class="mb-5">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                        Parol
                    </label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'"
                               name="password"
                               placeholder="••••••••"
                               required
                               autocomplete="current-password"
                               class="input-field w-full px-4 py-3 pr-12 rounded-xl text-sm font-medium">
                        <button type="button"
                                @click="showPass = !showPass"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors p-1">
                            <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember me -->
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="checkbox" name="remember" value="1"
                               class="w-4 h-4 rounded border-slate-600 bg-slate-700 text-blue-500 focus:ring-blue-500 focus:ring-offset-slate-800 cursor-pointer">
                        <span class="text-sm text-slate-400 group-hover:text-slate-300 transition-colors select-none">
                            Eslab qol
                        </span>
                    </label>
                    <a href="{{ url('/password') }}" class="text-xs text-blue-400 hover:text-blue-300 transition-colors font-medium">
                        Parolni o'zgartirish
                    </a>
                </div>

                <!-- Submit -->
                <button type="submit"
                        :disabled="loading"
                        class="btn-login w-full py-3 rounded-xl text-white font-bold text-sm tracking-wide flex items-center justify-center gap-2">
                    <svg x-show="loading" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="loading ? 'Kirilmoqda...' : 'Kirish'"></span>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-slate-600 text-xs mt-6">
            © {{ date('Y') }} RIMzone. Barcha huquqlar himoyalangan.
        </p>
    </div>

</body>
</html>
