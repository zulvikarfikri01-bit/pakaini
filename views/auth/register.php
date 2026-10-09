<!-- Welcome Bonus Banner -->
<div class="mb-5 p-4 rounded-2xl bg-gradient-to-r from-amber-500/15 via-yellow-500/10 to-brand-500/15 border border-amber-500/30 flex items-center space-x-3.5 shadow-lg shadow-amber-500/5">
    <div class="w-11 h-11 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-black text-xl flex-shrink-0 border border-amber-500/30 shadow-inner">
        <i class="fa-solid fa-gift"></i>
    </div>
    <div>
        <h4 class="text-xs font-black text-amber-300 uppercase tracking-wider">Spesial Pendaftaran Baru!</h4>
        <p class="text-[11px] text-slate-300 mt-0.5">
            Dapatkan saldo awal <strong class="text-amber-400 font-black">500 Koin</strong> + Langsung aktif status <strong class="text-yellow-300 font-bold">VIP Sultan Trial 3 Hari</strong>!
        </p>
    </div>
</div>

<form action="index.php?page=register" method="POST" class="space-y-3.5">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

    <div>
        <label for="username" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
            Username Akun
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fa-solid fa-at text-xs"></i>
            </div>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($username ?? '', ENT_QUOTES, 'UTF-8') ?>" required autofocus
                class="w-full pl-10 pr-4 py-2.5 bg-slate-950/80 border border-slate-700/80 rounded-2xl text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 text-sm transition-all"
                placeholder="misal: bintang99">
        </div>
    </div>

    <div>
        <label for="email" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
            Email Aktif
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fa-solid fa-envelope text-xs"></i>
            </div>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>" required
                class="w-full pl-10 pr-4 py-2.5 bg-slate-950/80 border border-slate-700/80 rounded-2xl text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 text-sm transition-all"
                placeholder="nama@email.com">
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label for="password" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                Password
            </label>
            <div class="relative">
                <input type="password" id="password" name="password" required minlength="6"
                    class="w-full px-3.5 py-2.5 bg-slate-950/80 border border-slate-700/80 rounded-2xl text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 text-sm transition-all font-mono"
                    placeholder="Min 6 karakter">
            </div>
        </div>
        <div>
            <label for="password_confirm" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                Ulangi Password
            </label>
            <div class="relative">
                <input type="password" id="password_confirm" name="password_confirm" required minlength="6"
                    class="w-full px-3.5 py-2.5 bg-slate-950/80 border border-slate-700/80 rounded-2xl text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 text-sm transition-all font-mono"
                    placeholder="Konfirmasi password">
            </div>
        </div>
    </div>

    <div>
        <label for="referred_by" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
            Kode Referral Pengajak <span class="text-slate-500 font-normal lowercase">(opsional)</span>
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fa-solid fa-user-tag text-xs"></i>
            </div>
            <input type="text" id="referred_by" name="referred_by" value="<?= htmlspecialchars($referred_by ?? '', ENT_QUOTES, 'UTF-8') ?>"
                class="w-full pl-10 pr-4 py-2.5 bg-slate-950/80 border border-slate-700/80 rounded-2xl text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 text-sm transition-all font-mono"
                placeholder="Username teman yang mengajak Anda">
        </div>
        <?php if (!empty($referred_by)): ?>
            <p class="text-[11px] text-emerald-400 mt-1 flex items-center">
                <i class="fa-solid fa-check-circle mr-1"></i> Terhubung dengan afiliasi: <strong><?= htmlspecialchars($referred_by, ENT_QUOTES, 'UTF-8') ?></strong>
            </p>
        <?php endif; ?>
    </div>

    <button type="submit" 
        class="w-full py-3 sm:py-3.5 px-4 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-2xl shadow-lg shadow-indigo-600/30 transition-all transform active:scale-98 text-xs sm:text-sm flex items-center justify-center space-x-2 mt-4">
        <i class="fa-solid fa-user-plus text-xs"></i>
        <span>Daftar Sekarang & Klaim 500 Koin</span>
    </button>
</form>

<div class="mt-6 text-center text-xs text-slate-400">
    Sudah memiliki akun? 
    <a href="index.php?page=login" class="font-bold text-brand-400 hover:text-brand-300 ml-1 transition-colors">
        Masuk di sini
    </a>
</div>
