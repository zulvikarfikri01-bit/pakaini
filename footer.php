    </main>

    <!-- Mobile-First App Bottom Navigation Bar (5 Items: Home, Cari Koin, Tambah Iklan, Komunitas, Profil) -->
    <?php if ($currentUser): ?>
    <?php $isAdminUser = (($currentUser['role'] ?? '') === 'admin' || in_array(strtolower($currentUser['username'] ?? ''), ['admin', 'adminzul', 'superadmin'])); ?>
    <nav class="md:hidden fixed bottom-0 w-full backdrop-blur-md bg-slate-950/80 border-t border-white/10 z-50 px-2 py-1.5 shadow-[0_-10px_30px_rgba(0,0,0,0.8)]">
        <div class="grid <?= $isAdminUser ? 'grid-cols-6' : 'grid-cols-5' ?> gap-1 max-w-md mx-auto text-center items-center">
            
            <!-- 1. Home -->
            <?php $isHome = ($currPage === 'dashboard.php'); ?>
            <a href="dashboard.php" class="flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl transition-all duration-200 <?= $isHome ? 'text-white font-bold' : 'text-slate-400 hover:text-slate-200' ?>">
                <div class="relative flex items-center justify-center">
                    <i class="fa-solid fa-house text-sm mb-0.5 <?= $isHome ? 'text-purple-400 drop-shadow-[0_0_8px_rgba(168,85,247,0.8)] scale-110' : 'text-slate-400' ?> transition-transform"></i>
                </div>
                <span class="text-[9px] sm:text-[10px] tracking-tight truncate w-full <?= $isHome ? 'text-white font-bold' : 'font-medium' ?>">Home</span>
                <?php if ($isHome): ?>
                    <span class="w-1 h-1 rounded-full bg-purple-400 shadow-[0_0_6px_rgba(168,85,247,0.9)] mt-0.5"></span>
                <?php endif; ?>
            </a>

            <!-- 2. Cari Koin -->
            <?php $isEarn = ($currPage === 'earn_coins.php'); ?>
            <a href="earn_coins.php" class="flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl transition-all duration-200 <?= $isEarn ? 'text-white font-bold' : 'text-slate-400 hover:text-slate-200' ?>">
                <div class="relative flex items-center justify-center">
                    <i class="fa-solid fa-coins text-sm mb-0.5 <?= $isEarn ? 'text-blue-400 drop-shadow-[0_0_8px_rgba(59,130,246,0.8)] scale-110' : 'text-slate-400' ?> transition-transform"></i>
                </div>
                <span class="text-[9px] sm:text-[10px] tracking-tight truncate w-full <?= $isEarn ? 'text-white font-bold' : 'font-medium' ?>">Cari Koin</span>
                <?php if ($isEarn): ?>
                    <span class="w-1 h-1 rounded-full bg-blue-400 shadow-[0_0_6px_rgba(59,130,246,0.9)] mt-0.5"></span>
                <?php endif; ?>
            </a>

            <!-- 3. Tambah Iklan / Pasang Iklan -->
            <?php $isTask = ($currPage === 'add_task.php'); ?>
            <a href="add_task.php" title="Pasang Iklan" class="flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl transition-all duration-200 <?= $isTask ? 'text-white font-bold' : 'text-slate-400 hover:text-slate-200' ?>">
                <div class="relative flex items-center justify-center">
                    <i class="fa-solid fa-bullhorn text-sm mb-0.5 <?= $isTask ? 'text-purple-400 drop-shadow-[0_0_8px_rgba(168,85,247,0.8)] scale-110' : 'text-slate-400' ?> transition-transform"></i>
                </div>
                <span class="text-[9px] sm:text-[10px] tracking-tight truncate w-full <?= $isTask ? 'text-white font-bold' : 'font-medium' ?>">Tambah Iklan</span>
                <?php if ($isTask): ?>
                    <span class="w-1 h-1 rounded-full bg-purple-400 shadow-[0_0_6px_rgba(168,85,247,0.9)] mt-0.5"></span>
                <?php endif; ?>
            </a>

            <!-- 4. Komunitas -->
            <?php $isComm = ($currPage === 'community.php' || ($_GET['page'] ?? '') === 'community'); ?>
            <a href="community.php" class="flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl transition-all duration-200 <?= $isComm ? 'text-white font-bold' : 'text-slate-400 hover:text-slate-200' ?>">
                <div class="relative flex items-center justify-center">
                    <i class="fa-solid fa-comments text-sm mb-0.5 <?= $isComm ? 'text-purple-400 drop-shadow-[0_0_8px_rgba(168,85,247,0.8)] scale-110' : 'text-slate-400' ?> transition-transform"></i>
                </div>
                <span class="text-[9px] sm:text-[10px] tracking-tight truncate w-full <?= $isComm ? 'text-white font-bold' : 'font-medium' ?>">Komunitas</span>
                <?php if ($isComm): ?>
                    <span class="w-1 h-1 rounded-full bg-purple-400 shadow-[0_0_6px_rgba(168,85,247,0.9)] mt-0.5"></span>
                <?php endif; ?>
            </a>

            <!-- 5. Profil -->
            <?php $isProfile = ($currPage === 'profile.php' || $currPage === 'edit_profile.php' || $currPage === 'referral.php' || $currPage === 'topup.php'); ?>
            <a href="profile.php" data-ref="referral.php" class="flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl transition-all duration-200 <?= $isProfile ? 'text-white font-bold' : 'text-slate-400 hover:text-slate-200' ?>">
                <div class="relative flex items-center justify-center">
                    <i class="fa-solid fa-circle-user text-sm mb-0.5 <?= $isProfile ? 'text-purple-400 drop-shadow-[0_0_8px_rgba(168,85,247,0.8)] scale-110' : 'text-slate-400' ?> transition-transform"></i>
                </div>
                <span class="text-[9px] sm:text-[10px] tracking-tight truncate w-full <?= $isProfile ? 'text-white font-bold' : 'font-medium' ?>">Profil</span>
                <?php if ($isProfile): ?>
                    <span class="w-1 h-1 rounded-full bg-purple-400 shadow-[0_0_6px_rgba(168,85,247,0.9)] mt-0.5"></span>
                <?php endif; ?>
            </a>

            <!-- 6. Panel Admin Khusus Super Admin -->
            <?php if ($isAdminUser): ?>
            <?php $isAdminActive = ($currPage === 'panel_pusat_pakaini_99x.php' || $currPage === 'admin.php'); ?>
            <a href="admin.php" class="flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl transition-all duration-200 <?= $isAdminActive ? 'text-rose-300 font-bold' : 'text-rose-400 hover:text-rose-200' ?>">
                <div class="relative flex items-center justify-center">
                    <i class="fa-solid fa-shield-halved text-sm mb-0.5 text-rose-400 <?= $isAdminActive ? 'drop-shadow-[0_0_8px_rgba(244,63,94,0.9)] scale-110' : '' ?> transition-transform"></i>
                </div>
                <span class="text-[9px] sm:text-[10px] tracking-tight truncate w-full font-bold text-rose-300">Admin</span>
                <?php if ($isAdminActive): ?>
                    <span class="w-1 h-1 rounded-full bg-rose-400 shadow-[0_0_6px_rgba(244,63,94,0.9)] mt-0.5"></span>
                <?php endif; ?>
            </a>
            <?php endif; ?>

        </div>
    </nav>
    <?php endif; ?>

    <!-- Global In-App Toast Container -->
    <div id="toastNotificationContainer" class="fixed top-4 right-4 sm:top-6 sm:right-6 z-[99999] flex flex-col gap-2.5 pointer-events-none max-w-sm w-[90vw] sm:w-80"></div>

    <script>
    window.showToast = function(type, message, duration = 3500) {
        let container = document.getElementById('toastNotificationContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastNotificationContainer';
            container.className = 'fixed top-4 right-4 sm:top-6 sm:right-6 z-[99999] flex flex-col gap-2.5 pointer-events-none max-w-sm w-[90vw] sm:w-80';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'pointer-events-auto flex items-center space-x-3 p-3.5 rounded-2xl border shadow-2xl backdrop-blur-xl transition-all duration-300 transform translate-y-[-10px] opacity-0 text-xs sm:text-sm font-semibold';
        
        if (type === 'success') {
            toast.className += ' bg-slate-900/95 border-emerald-500/40 text-emerald-200 shadow-emerald-950/50';
            toast.innerHTML = `
                <div class="w-7 h-7 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 text-sm">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="flex-1">${message}</div>
            `;
        } else if (type === 'error') {
            toast.className += ' bg-slate-900/95 border-rose-500/40 text-rose-200 shadow-rose-950/50';
            toast.innerHTML = `
                <div class="w-7 h-7 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0 text-sm">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <div class="flex-1">${message}</div>
            `;
        } else {
            toast.className += ' bg-slate-900/95 border-indigo-500/40 text-indigo-200 shadow-indigo-950/50';
            toast.innerHTML = `
                <div class="w-7 h-7 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0 text-sm">
                    <i class="fa-solid fa-circle-info"></i>
                </div>
                <div class="flex-1">${message}</div>
            `;
        }

        container.appendChild(toast);

        // Trigger enter animation
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-[-10px]', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
        });

        // Auto dismiss
        setTimeout(() => {
            toast.classList.add('opacity-0', 'scale-95');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    };
    </script>

    <!-- Global Footer -->
    <footer class="mt-auto border-t border-white/[0.06] bg-slate-950/40 py-6 text-center text-xs text-slate-500">
        <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="font-medium text-slate-400">Sistem Online & Siap Digunakan</span>
            </div>
            <p>&copy; <?= date('Y') ?> <strong class="text-slate-300">PAKAINI</strong> — Social Media Exchange Platform. All rights reserved.</p>
            <div class="flex items-center space-x-4 text-slate-400">

                <a href="topup.php" class="hover:text-amber-300 transition-colors">Beli VIP</a>
                <a href="referral.php" class="hover:text-white transition-colors">Program Bounty</a>
                <a href="community.php" class="hover:text-indigo-400 transition-colors">Komunitas</a>
                <a href="https://wa.me/<?= ADMIN_WHATSAPP ?>" target="_blank" class="hover:text-emerald-400 transition-colors">Bantuan Admin</a>
            </div>
        </div>
    </footer>

</body>
</html>
