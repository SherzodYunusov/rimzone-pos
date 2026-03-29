<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RIMzone — Parolni o'zgartirish</title>
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

        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            box-shadow: 0 4px 20px rgba(37,99,235,0.35);
            transition: all 0.2s;
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 28px rgba(37,99,235,0.45); }
        .btn-primary:active { transform: translateY(0); }
        .btn-primary:disabled { opacity: 0.6; transform: none; cursor: not-allowed; }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-in { animation: fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
    </style>
</head>
<body class="bg-pattern min-h-screen flex items-center justify-center p-4" x-data="{ showSecret: false, showNew: false, showConfirm: false, loading: false }">

    <div class="w-full max-w-sm animate-in">

        <!-- Logo -->
        <div class="flex flex-col items-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shadow-amber-500/30 mb-4">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">Parolni o'zgartirish</h1>
            <p class="text-slate-400 text-sm mt-1">Maxfiy kalit talab etiladi</p>
        </div>

        <!-- Card -->
        <div class="bg-slate-800/60 backdrop-blur-xl rounded-2xl p-7 card-glow">

            <!-- Success message -->
            @if(session('success'))
            <div class="mb-5 p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                <div class="flex items-start gap-3 mb-3">
                    <svg class="w-4 h-4 text-emerald-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-emerald-400 text-sm">{{ session('success') }}</p>
                </div>
                <a href="{{ url('/login') }}"
                   class="block w-full text-center py-2.5 rounded-xl bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 font-bold text-sm transition-colors">
                    Kirish sahifasiga o'tish →
                </a>
            </div>
            @endif

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

            <!-- Security notice -->
            <div class="mb-5 p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/20">
                <div class="flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-amber-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-amber-400/90 text-xs leading-relaxed">
                        Parolni o'zgartirish uchun maxfiy kalit kiritish shart. Bu ruxsatsiz o'zgartirishlardan himoya qiladi.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ url('/password') }}" @submit="loading = true">
                @csrf
                @method('PUT')

                <!-- Secret Keyword -->
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                        Maxfiy kalit
                    </label>
                    <div class="relative">
                        <input :type="showSecret ? 'text' : 'password'"
                               name="secret_keyword"
                               placeholder="Maxfiy kalitni kiriting"
                               required
                               autocomplete="off"
                               class="input-field w-full px-4 py-3 pr-12 rounded-xl text-sm font-medium">
                        <button type="button" @click="showSecret = !showSecret"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors p-1">
                            <svg x-show="!showSecret" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showSecret" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Separator -->
                <div class="flex items-center gap-3 my-5">
                    <div class="flex-1 h-px bg-slate-700"></div>
                    <span class="text-xs text-slate-600 font-medium">YANGI PAROL</span>
                    <div class="flex-1 h-px bg-slate-700"></div>
                </div>

                <!-- New Password -->
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                        Yangi parol
                    </label>
                    <div class="relative">
                        <input :type="showNew ? 'text' : 'password'"
                               name="new_password"
                               placeholder="Kamida 8 ta belgi"
                               required
                               autocomplete="new-password"
                               class="input-field w-full px-4 py-3 pr-12 rounded-xl text-sm font-medium">
                        <button type="button" @click="showNew = !showNew"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors p-1">
                            <svg x-show="!showNew" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showNew" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                        Yangi parolni tasdiqlang
                    </label>
                    <div class="relative">
                        <input :type="showConfirm ? 'text' : 'password'"
                               name="new_password_confirmation"
                               placeholder="Parolni qaytaring"
                               required
                               autocomplete="new-password"
                               class="input-field w-full px-4 py-3 pr-12 rounded-xl text-sm font-medium">
                        <button type="button" @click="showConfirm = !showConfirm"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors p-1">
                            <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showConfirm" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit"
                        :disabled="loading"
                        class="btn-primary w-full py-3 rounded-xl text-white font-bold text-sm tracking-wide flex items-center justify-center gap-2">
                    <svg x-show="loading" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="loading ? 'Saqlanmoqda...' : 'Parolni saqlash'"></span>
                </button>
            </form>
        </div>

        <!-- Back link -->
        <div class="text-center mt-5">
            <a href="{{ url('/login') }}" class="inline-flex items-center gap-1.5 text-slate-500 hover:text-slate-300 transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kirish sahifasiga qaytish
            </a>
        </div>
    </div>

</body>
</html>
