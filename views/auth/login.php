<form action="index.php?page=login" method="POST" class="space-y-4">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

    <div>
        <label for="username" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
            Username atau Email
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fa-solid fa-user text-xs"></i>
            </div>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($username ?? '', ENT_QUOTES, 'UTF-8') ?>" required autofocus
                class="w-full pl-10 pr-4 py-2.5 sm:py-3 bg-slate-950/80 border border-slate-700/80 rounded-2xl text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all text-xs sm:text-sm"
                placeholder="Masukkan username atau email">
        </div>
    </div>

    <div>
        <div class="flex items-center justify-between mb-2">
            <label for="password" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                Password
            </label>
            <span class="text-[11px] text-slate-500">Min. 6 karakter</span>
        </div>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fa-solid fa-lock text-xs"></i>
            </div>
            <input type="password" id="password" name="password" required
                class="w-full pl-10 pr-10 py-2.5 sm:py-3 bg-slate-950/80 border border-slate-700/80 rounded-2xl text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all text-xs sm:text-sm font-mono"
                placeholder="••••••••">
            <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition-colors">
                <i class="fa-solid fa-eye text-xs"></i>
            </button>
        </div>
        <!-- Lupa Password Link WhatsApp Admin -->
        <div class="mt-2 text-right">
            <a href="https://wa.me/628XXXXXXXXXX?text=Halo%20Admin%20Pakaini,%20saya%20lupa%20password%20akun%20saya.%20Username/Email%20saya%20adalah:%20" 
               target="_blank" 
               rel="noopener noreferrer"
               class="text-[11px] text-slate-400 hover:text-indigo-400 transition-colors inline-flex items-center gap-1.5">
                <i class="fa-brands fa-whatsapp text-emerald-400"></i>
                <span>Lupa Password? Hubungi Admin</span>
            </a>
        </div>
    </div>

    <button type="submit" 
        class="w-full py-3 sm:py-3.5 px-4 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-2xl shadow-lg shadow-indigo-600/30 transition-all transform active:scale-98 text-xs sm:text-sm flex items-center justify-center space-x-2">
        <span>Masuk ke Akun</span>
        <i class="fa-solid fa-arrow-right text-xs"></i>
    </button>
</form>

<div class="mt-6 text-center text-xs text-slate-400">
    Belum punya akun? 
    <a href="index.php?page=register" class="font-bold text-amber-400 hover:text-amber-300 ml-1 transition-colors">
        Daftar & Klaim 500 Koin + VIP!
    </a>
</div>
