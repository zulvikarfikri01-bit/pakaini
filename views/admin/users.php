<div class="space-y-6">

    <!-- Header & Search Bar -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="index.php?page=admin" class="hover:text-amber-400 transition-colors">Admin</a>
                <span>/</span>
                <span class="text-white font-medium">Manajemen Pengguna</span>
            </div>
            <h2 class="font-display font-black text-xl sm:text-3xl text-white flex items-center space-x-2.5">
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs sm:text-sm border border-amber-500/30">
                    <i class="fa-solid fa-users-gear"></i>
                </span>
                <span>Kelola User & Akun</span>
            </h2>
            <p class="text-[11px] sm:text-sm text-slate-400 mt-1">
                Kontrol penuh atas seluruh akun pengguna: top-up koin, reset password, ubah role admin, atur kasta VIP, hingga hapus akun.
            </p>
        </div>

        <!-- Actions: Create User + Search Form -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <button type="button" onclick="openCreateUserModal()" 
                    class="px-3.5 sm:px-4 py-2 sm:py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 font-black text-xs rounded-2xl transition-all shadow-lg shadow-emerald-500/20 flex items-center space-x-1.5 active:scale-95">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>Tambah Pengguna</span>
            </button>

            <!-- Search Form -->
            <form action="index.php" method="GET" class="flex items-center space-x-1.5">
                <input type="hidden" name="page" value="admin">
                <input type="hidden" name="action" value="users">
                <div class="relative">
                    <input type="text" name="q" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Cari username / email..."
                        class="pl-8 pr-3 py-2 sm:py-2.5 bg-slate-900 border border-slate-700/80 rounded-2xl text-white text-xs focus:outline-none focus:border-amber-400 w-36 sm:w-56 transition-all">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-3 top-2.5 sm:top-3 text-[11px]"></i>
                </div>
                <button type="submit" class="px-3 py-2 sm:py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs rounded-2xl transition-all shadow-md shadow-amber-500/20 active:scale-95">
                    Cari
                </button>
                <?php if (!empty($search)): ?>
                    <a href="index.php?page=admin&action=users" class="px-2.5 py-2 sm:py-2.5 bg-slate-800 text-slate-300 text-xs rounded-2xl hover:bg-slate-700 transition-colors">
                        Reset
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="glass-card bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-3.5 sm:p-7 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-display font-bold text-xs sm:text-sm text-slate-300 uppercase tracking-wider flex items-center space-x-2">
                <span>Daftar Pengguna Sistem (<?= count($users) ?> Akun Ditemukan)</span>
            </h3>
            <span class="text-[10px] sm:text-xs text-slate-500 hidden sm:inline">Kelola saldo, role, password, kuota klik, dan status VIP</span>
        </div>

        <div class="overflow-x-auto -mx-3.5 sm:mx-0">
            <table class="w-full text-left text-xs min-w-[700px]">
                <thead class="bg-slate-950/80 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-3">ID</th>
                        <th class="py-3 px-3">User & Email</th>
                        <th class="py-3 px-3">Role</th>
                        <th class="py-3 px-3">Saldo Koin</th>
                        <th class="py-3 px-3">Status VIP</th>
                        <th class="py-3 px-3">Klik Hari Ini</th>
                        <th class="py-3 px-3 text-right">Tindakan Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-user-slash text-3xl mb-2 block"></i>
                                Tidak ada akun yang cocok dengan kata kunci pencarian Anda.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): 
                            $isVip = (int)$u['is_vip'] === 1;
                            $coins = (int)$u['coins'];
                            $isAdmin = $u['role'] === 'admin';
                            $isSelf = (int)$u['id'] === (int)Auth::id();
                        ?>
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="py-3 px-3 text-slate-500 font-mono text-[11px]">#<?= $u['id'] ?></td>
                                <td class="py-3 px-3">
                                    <div class="font-bold text-white flex items-center space-x-1.5">
                                        <span><?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php if ($isVip): ?>
                                            <i class="fa-solid fa-crown text-amber-400 text-[10px]" title="VIP Sultan"></i>
                                        <?php endif; ?>
                                        <?php if ($isSelf): ?>
                                            <span class="text-[9px] px-1.5 py-0.2 bg-amber-500/20 text-amber-300 rounded border border-amber-500/30 font-semibold">Anda</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-mono"><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></div>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase <?= $isAdmin ? 'bg-rose-500/15 text-rose-400 border border-rose-500/30' : 'bg-slate-800 text-slate-300' ?>">
                                        <?= htmlspecialchars($u['role'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="py-3 px-3 font-black font-mono <?= ($coins < 0) ? 'text-rose-400 bg-rose-500/10 px-2 py-1 rounded-xl border border-rose-500/30' : 'text-amber-300' ?>">
                                    <?= number_format($coins) ?> 🪙
                                    <?php if ($coins < 0): ?>
                                        <span class="text-[9px] text-rose-400 block font-normal">(Hutang Denda)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-3">
                                    <?php if ($isVip): ?>
                                        <span class="inline-flex items-center text-[10px] font-black text-amber-300 bg-amber-500/15 px-2 py-0.5 rounded-full border border-amber-500/30">
                                            <i class="fa-solid fa-crown mr-1 text-[9px]"></i>VIP Sultan
                                        </span>
                                        <?php if (!empty($u['vip_expired_at'])): ?>
                                            <div class="text-[9px] text-slate-500 mt-0.5">s/d <?= date('d M Y', strtotime($u['vip_expired_at'])) ?></div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-slate-400 text-xs">Member Biasa</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="text-sky-300 font-mono text-xs font-semibold"><?= (int)$u['daily_clicks'] ?> Klik</span>
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <div class="flex items-center justify-end space-x-1.5">
                                        <!-- Kelola Saldo / VIP Modal Trigger -->
                                        <button type="button" 
                                                onclick="openManualModal(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?>', <?= $coins ?>, <?= $isVip ? 1 : 0 ?>)"
                                                title="Top Up / Potong Koin / Ubah VIP"
                                                class="px-2.5 py-1.5 rounded-xl bg-amber-500/15 hover:bg-amber-500/25 text-amber-300 border border-amber-500/30 text-xs font-bold transition-all shadow-sm active:scale-95 inline-flex items-center space-x-1">
                                            <i class="fa-solid fa-coins text-[10px]"></i>
                                            <span class="hidden sm:inline">Saldo & VIP</span>
                                        </button>

                                        <!-- Tombol Reset Sandi ke Default 123456 -->
                                        <form action="index.php?page=admin&action=reset_password_default" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mereset kata sandi akun @<?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?> menjadi: 123456?');" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                            <button type="submit" 
                                                    title="Reset Kata Sandi ke 123456"
                                                    class="px-2.5 py-1.5 rounded-xl bg-sky-500/15 hover:bg-sky-500/25 text-sky-300 border border-sky-500/30 text-xs font-bold transition-all shadow-sm active:scale-95 inline-flex items-center space-x-1 cursor-pointer">
                                                <i class="fa-solid fa-key text-[10px]"></i>
                                                <span class="hidden sm:inline">Reset Sandi</span>
                                            </button>
                                        </form>

                                        <!-- Quick Action Dropdown Trigger -->
                                        <div class="relative inline-block text-left" id="userMenuContainer_<?= $u['id'] ?>">
                                            <button type="button" onclick="toggleUserMenu(<?= $u['id'] ?>)"
                                                    class="w-7 h-7 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition-colors">
                                                <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                                            </button>
                                            
                                            <!-- Dropdown Menu -->
                                            <div id="userMenu_<?= $u['id'] ?>" class="hidden absolute right-0 mt-2 w-48 bg-slate-900 border border-slate-700/80 rounded-2xl shadow-2xl py-1.5 z-40 text-left">
                                                <!-- Set Exact Coins -->
                                                <button type="button" onclick="openExactCoinsModal(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?>', <?= $coins ?>)" 
                                                        class="w-full text-left px-3.5 py-2 text-xs text-slate-300 hover:text-white hover:bg-slate-800/80 flex items-center space-x-2">
                                                    <i class="fa-solid fa-calculator w-4 text-amber-400"></i>
                                                    <span>Setel Saldo Tepat</span>
                                                </button>

                                                <!-- Reset Password -->
                                                <button type="button" onclick="openResetPasswordModal(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?>')" 
                                                        class="w-full text-left px-3.5 py-2 text-xs text-slate-300 hover:text-white hover:bg-slate-800/80 flex items-center space-x-2">
                                                    <i class="fa-solid fa-key w-4 text-sky-400"></i>
                                                    <span>Reset Password</span>
                                                </button>

                                                <!-- Toggle VIP Immediate -->
                                                <?php if ($isVip): ?>
                                                    <form action="index.php?page=admin&action=toggle_vip" method="POST" onsubmit="return confirm('Cabut status VIP pengguna <?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?>?');">
                                                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                        <input type="hidden" name="vip_action" value="deactivate">
                                                        <button type="submit" class="w-full text-left px-3.5 py-2 text-xs text-orange-300 hover:text-orange-200 hover:bg-orange-500/10 flex items-center space-x-2">
                                                            <i class="fa-solid fa-ban w-4 text-orange-400"></i>
                                                            <span>Cabut Status VIP</span>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <!-- Change Role -->
                                                <?php if (!$isSelf): ?>
                                                    <form action="index.php?page=admin&action=change_role" method="POST" onsubmit="return confirm('Ubah role akun <?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?> menjadi <?= $isAdmin ? 'Member Biasa' : 'Administrator' ?>?');">
                                                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                        <input type="hidden" name="role" value="<?= $isAdmin ? 'user' : 'admin' ?>">
                                                        <button type="submit" class="w-full text-left px-3.5 py-2 text-xs text-purple-300 hover:text-purple-200 hover:bg-purple-500/10 flex items-center space-x-2">
                                                            <i class="fa-solid fa-user-shield w-4 text-purple-400"></i>
                                                            <span><?= $isAdmin ? 'Ubah ke Member' : 'Jadikan Admin' ?></span>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <!-- Reset Daily Clicks -->
                                                <form action="index.php?page=admin&action=reset_clicks" method="POST" onsubmit="return confirm('Reset kuota klik harian <?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?> ke 0?');">
                                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                    <button type="submit" class="w-full text-left px-3.5 py-2 text-xs text-cyan-300 hover:text-cyan-200 hover:bg-cyan-500/10 flex items-center space-x-2">
                                                        <i class="fa-solid fa-rotate-left w-4 text-cyan-400"></i>
                                                        <span>Reset Klik Harian</span>
                                                    </button>
                                                </form>

                                                <!-- Delete User (Permanent) -->
                                                <?php if (!$isSelf): ?>
                                                    <div class="border-t border-slate-800 my-1"></div>
                                                    <form action="index.php?page=admin&action=delete_user" method="POST" onsubmit="return confirm('PERINGATAN: Hapus akun <?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?> secara permanen beserta seluruh koin dan tugasnya? Tindakan ini tidak dapat dibatalkan!');">
                                                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                        <button type="submit" class="w-full text-left px-3.5 py-2 text-xs text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 flex items-center space-x-2">
                                                            <i class="fa-solid fa-trash w-4 text-rose-400"></i>
                                                            <span>Hapus Akun</span>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL 1: TAMBAH PENGGUNA BARU LANGSUNG OLEH ADMIN                         -->
<!-- ========================================================================= -->
<div id="createUserModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl relative">
        <button type="button" onclick="closeCreateUserModal()" class="absolute top-5 right-5 text-slate-400 hover:text-white transition-colors">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="text-center mb-5">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl mx-auto mb-2 border border-emerald-500/30 shadow-lg shadow-emerald-500/10">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h3 class="font-display font-black text-xl text-white">Tambah Pengguna Baru</h3>
            <p class="text-xs text-slate-400 mt-0.5">Buat akun langsung dengan koin awal & status VIP dari Admin.</p>
        </div>

        <form action="index.php?page=admin&action=create_user" method="POST" class="space-y-3.5">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <div>
                <label for="new_username" class="block text-xs font-bold text-slate-300 mb-1">Username *</label>
                <input type="text" id="new_username" name="username" required placeholder="misal: bobsultan"
                    class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-xs focus:outline-none focus:border-emerald-400">
            </div>

            <div>
                <label for="new_email" class="block text-xs font-bold text-slate-300 mb-1">Email *</label>
                <input type="email" id="new_email" name="email" required placeholder="misal: bob@gmail.com"
                    class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-xs focus:outline-none focus:border-emerald-400">
            </div>

            <div>
                <label for="new_password" class="block text-xs font-bold text-slate-300 mb-1">Password Baru (min 6 karakter) *</label>
                <input type="text" id="new_password" name="password" required value="pakaini123"
                    class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-xs font-mono focus:outline-none focus:border-emerald-400">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="new_role" class="block text-xs font-bold text-slate-300 mb-1">Role Akun</label>
                    <select id="new_role" name="role" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-xs focus:outline-none focus:border-emerald-400">
                        <option value="user">Member Biasa</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div>
                    <label for="new_coins" class="block text-xs font-bold text-slate-300 mb-1">Saldo Koin Awal</label>
                    <input type="number" id="new_coins" name="coins" value="1000" min="0"
                        class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-xs font-mono font-bold focus:outline-none focus:border-emerald-400">
                </div>
            </div>

            <div class="p-3 bg-slate-950/70 border border-slate-800 rounded-2xl space-y-2">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="is_vip" value="1" checked class="rounded border-slate-700 text-amber-500 focus:ring-0">
                    <span class="text-xs font-bold text-white flex items-center space-x-1">
                        <i class="fa-solid fa-crown text-amber-400 text-xs"></i>
                        <span>Berikan Status VIP Sultan</span>
                    </span>
                </label>
                <div>
                    <label for="new_vip_days" class="block text-[11px] text-slate-400 mb-1">Masa Aktif VIP (Hari)</label>
                    <input type="number" id="new_vip_days" name="vip_days" value="30" min="1"
                        class="w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-xl text-white text-xs font-mono focus:outline-none focus:border-emerald-400">
                </div>
            </div>

            <div class="flex items-center space-x-2 pt-2">
                <button type="button" onclick="closeCreateUserModal()" class="flex-1 py-3 rounded-2xl bg-slate-800 text-slate-300 text-xs font-bold hover:bg-slate-700 transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-black shadow-lg shadow-emerald-500/25 transition-all active:scale-95">
                    Buat Akun Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: INPUT SALDO / VIP MANUAL (TOP UP, EXTEND VIP, POTONG)            -->
<!-- ========================================================================= -->
<div id="manualModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl relative">
        <button type="button" onclick="closeManualModal()" class="absolute top-5 right-5 text-slate-400 hover:text-white transition-colors">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="text-center mb-5">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-xl mx-auto mb-2 border border-amber-500/30 shadow-lg shadow-amber-500/10">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <h3 class="font-display font-black text-xl text-white">Input Saldo / Status VIP</h3>
            <p class="text-xs text-slate-400 mt-0.5">Top up koin atau aktifkan durasi VIP untuk pesanan WhatsApp.</p>
        </div>

        <form action="index.php?page=admin&action=manual_action" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" id="modal_user_id" name="user_id" value="">

            <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 text-xs space-y-1">
                <div class="flex justify-between text-slate-400">
                    <span>Target Pengguna:</span>
                    <strong id="modal_username" class="text-white text-sm">--</strong>
                </div>
                <div class="flex justify-between text-slate-400">
                    <span>Saldo Koin Sekarang:</span>
                    <strong id="modal_coins" class="text-amber-300 font-mono">-- Koin</strong>
                </div>
            </div>

            <div>
                <label for="action_type" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Tindakan</label>
                <select id="action_type" name="action_type" onchange="toggleActionFields()" class="w-full px-3.5 py-3 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-xs focus:outline-none focus:border-amber-400">
                    <option value="add_coins">Tambah Saldo Koin (Top Up)</option>
                    <option value="extend_vip">Aktifkan / Perpanjang VIP Sultan (Hari)</option>
                    <option value="deduct_coins">Potong Saldo Koin (Sanksi / Penyesuaian)</option>
                </select>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label id="lblAmount" for="amount" class="text-xs font-bold text-slate-300 uppercase tracking-wider">
                        Jumlah Koin Tambahan
                    </label>
                    <span id="amountHintBadge" class="text-[10px] text-amber-400 font-bold">Preset Cepat:</span>
                </div>

                <!-- Quick Presets -->
                <div id="coinPresets" class="grid grid-cols-3 gap-2 mb-2">
                    <button type="button" onclick="setAmount(1000)" class="p-1.5 bg-slate-950 border border-slate-800 hover:border-amber-400/50 rounded-xl text-xs font-bold text-amber-300">
                        +1.000 🪙
                    </button>
                    <button type="button" onclick="setAmount(2000)" class="p-1.5 bg-slate-950 border border-slate-800 hover:border-amber-400/50 rounded-xl text-xs font-bold text-amber-300">
                        +2.000 🪙
                    </button>
                    <button type="button" onclick="setAmount(5500)" class="p-1.5 bg-slate-950 border border-slate-800 hover:border-amber-400/50 rounded-xl text-xs font-bold text-amber-300">
                        +5.500 🪙
                    </button>
                </div>

                <div id="vipPresets" class="hidden grid-cols-2 gap-2 mb-2">
                    <button type="button" onclick="setAmount(30)" class="p-1.5 bg-slate-950 border border-slate-800 hover:border-amber-400/50 rounded-xl text-xs font-bold text-amber-300">
                        30 Hari (1 Bulan)
                    </button>
                    <button type="button" onclick="setAmount(90)" class="p-1.5 bg-slate-950 border border-slate-800 hover:border-amber-400/50 rounded-xl text-xs font-bold text-amber-300">
                        90 Hari (3 Bulan)
                    </button>
                </div>

                <input type="number" id="amount" name="amount" required min="1" value="1000"
                    class="w-full px-3.5 py-3 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-sm font-black font-mono focus:outline-none focus:border-amber-400">
            </div>

            <div>
                <label for="notes" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Catatan / Keterangan</label>
                <input type="text" id="notes" name="notes" value="Transfer WhatsApp divalidasi"
                    class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-xs focus:outline-none focus:border-amber-400">
            </div>

            <div class="flex items-center space-x-2 pt-2">
                <button type="button" onclick="closeManualModal()" class="flex-1 py-3 rounded-2xl bg-slate-800 text-slate-300 text-xs font-bold hover:bg-slate-700 transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-3 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black shadow-lg shadow-amber-500/25 transition-all active:scale-95">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 3: SETEL SALDO KOIN TEPAT (EXACT COINS)                             -->
<!-- ========================================================================= -->
<div id="exactCoinsModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl relative">
        <button type="button" onclick="closeExactCoinsModal()" class="absolute top-5 right-5 text-slate-400 hover:text-white transition-colors">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="text-center mb-5">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-xl mx-auto mb-2 border border-amber-500/30">
                <i class="fa-solid fa-calculator"></i>
            </div>
            <h3 class="font-display font-black text-xl text-white">Setel Saldo Koin Tepat</h3>
            <p class="text-xs text-slate-400 mt-0.5">Ubah saldo koin pengguna ke angka absolut tertentu.</p>
        </div>

        <form action="index.php?page=admin&action=set_coins" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" id="exact_user_id" name="user_id" value="">

            <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 text-xs flex justify-between">
                <span class="text-slate-400">Pengguna: <strong id="exact_username" class="text-white">--</strong></span>
                <span class="text-slate-400">Saldo Saat Ini: <strong id="exact_current_coins" class="text-amber-300 font-mono">--</strong></span>
            </div>

            <div>
                <label for="exact_new_coins" class="block text-xs font-bold text-slate-300 mb-2">Target Saldo Baru (Koin)</label>
                <input type="number" id="exact_new_coins" name="coins" required value="0"
                    class="w-full px-3.5 py-3 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-base font-black font-mono focus:outline-none focus:border-amber-400">
                <p class="text-[10px] text-slate-500 mt-1">Sistem otomatis mencatat selisih mutasi ke buku kas transaksi.</p>
            </div>

            <div>
                <label for="exact_reason" class="block text-xs font-bold text-slate-300 mb-2">Alasan Penyesuaian</label>
                <input type="text" id="exact_reason" name="reason" value="Penyesuaian saldo langsung oleh Admin"
                    class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-xs focus:outline-none focus:border-amber-400">
            </div>

            <div class="flex items-center space-x-2 pt-2">
                <button type="button" onclick="closeExactCoinsModal()" class="flex-1 py-3 rounded-2xl bg-slate-800 text-slate-300 text-xs font-bold hover:bg-slate-700 transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-3 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black shadow-lg shadow-amber-500/25 transition-all active:scale-95">
                    Terapkan Saldo
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 4: RESET PASSWORD USER LANGSUNG                                     -->
<!-- ========================================================================= -->
<div id="resetPasswordModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl relative">
        <button type="button" onclick="closeResetPasswordModal()" class="absolute top-5 right-5 text-slate-400 hover:text-white transition-colors">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="text-center mb-5">
            <div class="w-12 h-12 rounded-2xl bg-sky-500/20 text-sky-400 flex items-center justify-center text-xl mx-auto mb-2 border border-sky-500/30">
                <i class="fa-solid fa-key"></i>
            </div>
            <h3 class="font-display font-black text-xl text-white">Reset Password Pengguna</h3>
            <p class="text-xs text-slate-400 mt-0.5">Tentukan password baru untuk akun yang lupa atau bermasalah.</p>
        </div>

        <form action="index.php?page=admin&action=reset_password" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" id="reset_user_id" name="user_id" value="">

            <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 text-xs">
                <span class="text-slate-400">Target Akun: <strong id="reset_username" class="text-white">--</strong></span>
            </div>

            <div>
                <label for="new_user_password" class="block text-xs font-bold text-slate-300 mb-2">Password Baru (min 6 karakter)</label>
                <div class="relative">
                    <input type="text" id="new_user_password" name="new_password" required minlength="6" placeholder="Masukkan password baru..."
                        class="w-full px-3.5 py-3 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-xs font-mono focus:outline-none focus:border-sky-400">
                    <button type="button" onclick="generateRandomPass()" class="absolute right-2.5 top-2.5 px-2 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] rounded-lg">
                        Acak
                    </button>
                </div>
            </div>

            <div class="flex items-center space-x-2 pt-2">
                <button type="button" onclick="closeResetPasswordModal()" class="flex-1 py-3 rounded-2xl bg-slate-800 text-slate-300 text-xs font-bold hover:bg-slate-700 transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-3 rounded-2xl bg-sky-500 hover:bg-sky-400 text-slate-950 text-xs font-black shadow-lg shadow-sky-500/25 transition-all active:scale-95">
                    Ganti Password
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Click outside to close user dropdowns
    document.addEventListener('click', (e) => {
        if (!e.target.closest('[id^="userMenuContainer_"]')) {
            document.querySelectorAll('[id^="userMenu_"]').forEach(el => el.classList.add('hidden'));
        }
    });

    function toggleUserMenu(userId) {
        const menu = document.getElementById('userMenu_' + userId);
        const isHidden = menu.classList.contains('hidden');
        document.querySelectorAll('[id^="userMenu_"]').forEach(el => el.classList.add('hidden'));
        if (isHidden) {
            menu.classList.remove('hidden');
        }
    }

    // Modal 1: Create User
    function openCreateUserModal() {
        document.getElementById('createUserModal').classList.remove('hidden');
    }
    function closeCreateUserModal() {
        document.getElementById('createUserModal').classList.add('hidden');
    }

    // Modal 2: Manual Balance / VIP
    function openManualModal(userId, username, coins, isVip) {
        document.getElementById('modal_user_id').value = userId;
        document.getElementById('modal_username').innerText = username;
        document.getElementById('modal_coins').innerText = `${coins.toLocaleString()} Koin`;
        document.getElementById('manualModal').classList.remove('hidden');
    }
    function closeManualModal() {
        document.getElementById('manualModal').classList.add('hidden');
    }
    function setAmount(val) {
        document.getElementById('amount').value = val;
    }
    function toggleActionFields() {
        const type = document.getElementById('action_type').value;
        const lbl = document.getElementById('lblAmount');
        const coinPresets = document.getElementById('coinPresets');
        const vipPresets = document.getElementById('vipPresets');
        const amtInput = document.getElementById('amount');

        if (type === 'add_coins') {
            lbl.innerText = 'Jumlah Koin Tambahan';
            coinPresets.classList.remove('hidden');
            coinPresets.classList.add('grid');
            vipPresets.classList.add('hidden');
            vipPresets.classList.remove('grid');
            amtInput.value = 1000;
        } else if (type === 'extend_vip') {
            lbl.innerText = 'Jumlah Hari VIP Sultan';
            coinPresets.classList.add('hidden');
            coinPresets.classList.remove('grid');
            vipPresets.classList.remove('hidden');
            vipPresets.classList.add('grid');
            amtInput.value = 30;
        } else if (type === 'deduct_coins') {
            lbl.innerText = 'Jumlah Koin yang Dipotong';
            coinPresets.classList.add('hidden');
            coinPresets.classList.remove('grid');
            vipPresets.classList.add('hidden');
            vipPresets.classList.remove('grid');
            amtInput.value = 100;
        }
    }

    // Modal 3: Exact Coins
    function openExactCoinsModal(userId, username, coins) {
        document.getElementById('exact_user_id').value = userId;
        document.getElementById('exact_username').innerText = username;
        document.getElementById('exact_current_coins').innerText = `${coins.toLocaleString()} Koin`;
        document.getElementById('exact_new_coins').value = coins;
        document.getElementById('exactCoinsModal').classList.remove('hidden');
    }
    function closeExactCoinsModal() {
        document.getElementById('exactCoinsModal').classList.add('hidden');
    }

    // Modal 4: Reset Password
    function openResetPasswordModal(userId, username) {
        document.getElementById('reset_user_id').value = userId;
        document.getElementById('reset_username').innerText = username;
        document.getElementById('new_user_password').value = '';
        document.getElementById('resetPasswordModal').classList.remove('hidden');
    }
    function closeResetPasswordModal() {
        document.getElementById('resetPasswordModal').classList.add('hidden');
    }
    function generateRandomPass() {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#';
        let pass = '';
        for (let i = 0; i < 8; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('new_user_password').value = pass;
    }
</script>
