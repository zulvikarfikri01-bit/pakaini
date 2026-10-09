<div class="space-y-6">

    <!-- Header & Statistics Cards -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="index.php?page=admin" class="hover:text-amber-400 transition-colors">Admin</a>
                <span>/</span>
                <span class="text-white font-medium">Manajemen Kampanye</span>
            </div>
            <h2 class="font-display font-black text-xl sm:text-3xl text-white flex items-center space-x-2.5">
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xs sm:text-sm border border-indigo-500/30">
                    <i class="fa-solid fa-list-check"></i>
                </span>
                <span>Kelola Kampanye & Tugas</span>
            </h2>
            <p class="text-[11px] sm:text-sm text-slate-400 mt-1">
                Pantau seluruh tugas tukar interaksi di platform: tinjau tautan, ubah CPC/target klik, jeda sementara, atau hapus tugas pelanggaran.
            </p>
        </div>
    </div>

    <!-- Quick Status Counter Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="glass-card bg-slate-900/80 p-3.5 sm:p-5 rounded-2xl border border-slate-800">
            <span class="text-[10px] sm:text-xs text-slate-400 uppercase tracking-wider font-semibold">Total Kampanye</span>
            <div class="text-lg sm:text-2xl font-black text-white font-display mt-1"><?= number_format($total_all) ?></div>
        </div>
        <div class="glass-card bg-emerald-500/10 p-3.5 sm:p-5 rounded-2xl border border-emerald-500/20">
            <span class="text-[10px] sm:text-xs text-emerald-400 uppercase tracking-wider font-semibold flex items-center space-x-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Sedang Aktif</span>
            </span>
            <div class="text-lg sm:text-2xl font-black text-emerald-300 font-display mt-1"><?= number_format($total_active) ?></div>
        </div>
        <div class="glass-card bg-amber-500/10 p-3.5 sm:p-5 rounded-2xl border border-amber-500/20">
            <span class="text-[10px] sm:text-xs text-amber-400 uppercase tracking-wider font-semibold">Dijeda / Habis Saldo</span>
            <div class="text-lg sm:text-2xl font-black text-amber-300 font-display mt-1"><?= number_format($total_paused) ?></div>
        </div>
        <div class="glass-card bg-indigo-500/10 p-3.5 sm:p-5 rounded-2xl border border-indigo-500/20">
            <span class="text-[10px] sm:text-xs text-indigo-400 uppercase tracking-wider font-semibold">Selesai Target</span>
            <div class="text-lg sm:text-2xl font-black text-indigo-300 font-display mt-1"><?= number_format($total_completed) ?></div>
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="glass-card bg-slate-900/90 border border-slate-800 rounded-2xl p-4 shadow-xl">
        <form action="index.php" method="GET" class="flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
            <input type="hidden" name="page" value="admin">
            <input type="hidden" name="action" value="tasks">

            <!-- Filter Platform & Status Pills -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-slate-400 font-bold uppercase text-[10px] mr-1">Platform:</span>
                <?php 
                $platforms = [
                    'all'       => 'Semua',
                    'youtube'   => 'YouTube',
                    'facebook'  => 'Facebook',
                    'tiktok'    => 'TikTok',
                    'instagram' => 'Instagram'
                ];
                foreach ($platforms as $pk => $pLabel): 
                ?>
                    <a href="index.php?page=admin&action=tasks&platform=<?= $pk ?>&status=<?= htmlspecialchars($selected_status, ENT_QUOTES, 'UTF-8') ?>&q=<?= urlencode($search) ?>"
                       class="px-2.5 py-1 rounded-xl font-bold transition-all text-xs <?= ($selected_plat === $pk) ? 'bg-indigo-500 text-white shadow-md shadow-indigo-500/20' : 'bg-slate-800/80 text-slate-400 hover:text-white hover:bg-slate-700' ?>">
                        <?= $pLabel ?>
                    </a>
                <?php endforeach; ?>

                <span class="text-slate-500 mx-1">|</span>

                <span class="text-slate-400 font-bold uppercase text-[10px] mr-1">Status:</span>
                <?php 
                $statuses = [
                    'all'       => 'Semua',
                    'active'    => 'Aktif',
                    'paused'    => 'Dijeda',
                    'completed' => 'Selesai'
                ];
                foreach ($statuses as $sk => $sLabel): 
                ?>
                    <a href="index.php?page=admin&action=tasks&platform=<?= htmlspecialchars($selected_plat, ENT_QUOTES, 'UTF-8') ?>&status=<?= $sk ?>&q=<?= urlencode($search) ?>"
                       class="px-2.5 py-1 rounded-xl font-bold transition-all text-xs <?= ($selected_status === $sk) ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'bg-slate-800/80 text-slate-400 hover:text-white hover:bg-slate-700' ?>">
                        <?= $sLabel ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Search Field -->
            <div class="flex items-center space-x-1.5">
                <input type="hidden" name="platform" value="<?= htmlspecialchars($selected_plat, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="status" value="<?= htmlspecialchars($selected_status, ENT_QUOTES, 'UTF-8') ?>">
                <div class="relative">
                    <input type="text" name="q" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Cari URL / username..."
                        class="pl-8 pr-3 py-1.5 sm:py-2 bg-slate-950 border border-slate-700/80 rounded-xl text-white text-xs focus:outline-none focus:border-indigo-400 w-44 sm:w-56 transition-all">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-2 sm:top-2.5 text-[10px]"></i>
                </div>
                <button type="submit" class="px-3 py-1.5 sm:py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl transition-all shadow-md shadow-indigo-600/20">
                    Cari
                </button>
                <?php if (!empty($search) || $selected_plat !== 'all' || $selected_status !== 'all'): ?>
                    <a href="index.php?page=admin&action=tasks" class="px-2.5 py-1.5 sm:py-2 bg-slate-800 text-slate-300 text-xs rounded-xl hover:bg-slate-700 transition-colors">
                        Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tasks Table -->
    <div class="glass-card bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-3.5 sm:p-7 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-display font-bold text-xs sm:text-sm text-slate-300 uppercase tracking-wider">
                Daftar Kampanye (<?= count($tasks) ?> Kampanye Ditampilkan)
            </h3>
            <span class="text-[10px] sm:text-xs text-slate-500 hidden sm:inline">Kelola status kampanye secara instan</span>
        </div>

        <div class="overflow-x-auto -mx-3.5 sm:mx-0">
            <table class="w-full text-left text-xs min-w-[760px]">
                <thead class="bg-slate-950/80 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-3">ID</th>
                        <th class="py-3 px-3">Pembuat Kampanye</th>
                        <th class="py-3 px-3">Platform & Tindakan</th>
                        <th class="py-3 px-3">Tautan Tugas</th>
                        <th class="py-3 px-3">CPC</th>
                        <th class="py-3 px-3">Progress Klik</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3 text-right">Tindakan Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($tasks)): ?>
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-folder-open text-3xl mb-2 block"></i>
                                Tidak ada kampanye yang cocok dengan filter yang dipilih.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tasks as $t): 
                            $cpc = (int)$t['cpc'];
                            $currentClicks = (int)$t['current_clicks'];
                            $maxClicks = (int)$t['max_clicks'];
                            $pct = ($maxClicks > 0) ? min(100, round(($currentClicks / $maxClicks) * 100)) : 0;
                            $ownerCoins = (int)($t['owner_coins'] ?? 0);
                            $isOwnerVip = !empty($t['owner_is_vip']);

                            $platformIcons = [
                                'youtube'   => 'fa-brands fa-youtube text-rose-500',
                                'facebook'  => 'fa-brands fa-facebook text-blue-500',
                                'tiktok'    => 'fa-brands fa-tiktok text-slate-200',
                                'instagram' => 'fa-brands fa-instagram text-pink-500'
                            ];
                            $platIcon = $platformIcons[$t['platform']] ?? 'fa-solid fa-share-nodes text-indigo-400';
                        ?>
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="py-3 px-3 text-slate-500 font-mono text-[11px]">#<?= $t['id'] ?></td>
                                <td class="py-3 px-3">
                                    <div class="font-bold text-white flex items-center space-x-1.5">
                                        <span><?= htmlspecialchars($t['owner_username'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php if ($isOwnerVip): ?>
                                            <i class="fa-solid fa-crown text-amber-400 text-[9px]" title="VIP Sultan"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-mono">
                                        Saldo: <span class="<?= $ownerCoins < $cpc ? 'text-rose-400 font-bold' : 'text-amber-300' ?>"><?= number_format($ownerCoins) ?> 🪙</span>
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="flex items-center space-x-1.5">
                                        <i class="<?= $platIcon ?> text-sm"></i>
                                        <span class="font-bold text-white uppercase text-[11px]"><?= htmlspecialchars($t['platform'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 capitalize block mt-0.5"><?= htmlspecialchars($t['task_type'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td class="py-3 px-3 max-w-[200px]">
                                    <a href="<?= htmlspecialchars($t['task_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" 
                                       title="<?= htmlspecialchars($t['task_url'], ENT_QUOTES, 'UTF-8') ?>"
                                       class="text-indigo-400 hover:text-indigo-300 truncate block text-[11px] hover:underline flex items-center space-x-1">
                                        <span class="truncate"><?= htmlspecialchars($t['task_url'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px] flex-shrink-0"></i>
                                    </a>
                                </td>
                                <td class="py-3 px-3 font-mono font-bold text-amber-300">
                                    <?= number_format($cpc) ?> 🪙
                                </td>
                                <td class="py-3 px-3">
                                    <div class="flex items-center justify-between text-[10px] font-mono text-slate-300 mb-1">
                                        <span><?= $currentClicks ?> / <?= $maxClicks ?></span>
                                        <span><?= $pct ?>%</span>
                                    </div>
                                    <div class="w-24 bg-slate-950 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-gradient-to-r from-indigo-500 to-amber-400 h-1.5 rounded-full" style="width: <?= $pct ?>%"></div>
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    <?php if ($t['status'] === 'active'): ?>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 inline-flex items-center space-x-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                            <span>Aktif</span>
                                        </span>
                                    <?php elseif ($t['status'] === 'paused'): ?>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                            Dijeda
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-slate-800 text-slate-400">
                                            Selesai
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <div class="flex items-center justify-end space-x-1.5">
                                        <!-- Edit Modal Trigger -->
                                        <button type="button" 
                                                onclick="openEditTaskModal(<?= $t['id'] ?>, '<?= $t['status'] ?>', <?= $cpc ?>, <?= $maxClicks ?>, '<?= htmlspecialchars($t['platform'], ENT_QUOTES) ?>', '<?= htmlspecialchars($t['owner_username'], ENT_QUOTES) ?>')"
                                                class="px-2.5 py-1.5 rounded-xl bg-indigo-500/15 hover:bg-indigo-500/25 text-indigo-300 border border-indigo-500/30 text-xs font-bold transition-all shadow-sm active:scale-95 flex items-center space-x-1">
                                            <i class="fa-solid fa-sliders text-[10px]"></i>
                                            <span class="hidden sm:inline">Ubah</span>
                                        </button>

                                        <!-- Delete Task Button -->
                                        <form action="index.php?page=admin&action=delete_task" method="POST" onsubmit="return confirm('Hapus kampanye #<?= $t['id'] ?> secara permanen?');">
                                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                            <input type="hidden" name="task_id" value="<?= $t['id'] ?>">
                                            <button type="submit" title="Hapus Kampanye Pelanggaran"
                                                    class="w-7 h-7 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 flex items-center justify-center transition-colors">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
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
<!-- MODAL EDIT KAMPANYE ADMIN (STATUS, CPC, TARGET KLIK)                      -->
<!-- ========================================================================= -->
<div id="editTaskModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl relative">
        <button type="button" onclick="closeEditTaskModal()" class="absolute top-5 right-5 text-slate-400 hover:text-white transition-colors">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="text-center mb-5">
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xl mx-auto mb-2 border border-indigo-500/30">
                <i class="fa-solid fa-sliders"></i>
            </div>
            <h3 class="font-display font-black text-xl text-white">Ubah Parameter Kampanye</h3>
            <p class="text-xs text-slate-400 mt-0.5">Ubah status tayang, CPC, atau target klik kampanye.</p>
        </div>

        <form action="index.php?page=admin&action=update_task" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" id="edit_task_id" name="task_id" value="">

            <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 text-xs flex justify-between">
                <span class="text-slate-400">ID Kampanye: <strong id="edit_task_label" class="text-white">#--</strong></span>
                <span class="text-slate-400">Pemilik: <strong id="edit_task_owner" class="text-amber-300">--</strong></span>
            </div>

            <div>
                <label for="edit_status" class="block text-xs font-bold text-slate-300 mb-2">Status Tayang Kampanye</label>
                <select id="edit_status" name="status" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-xs focus:outline-none focus:border-indigo-400">
                    <option value="active">Aktif (Bisa dikerjakan member)</option>
                    <option value="paused">Dijeda (Sembunyikan sementara)</option>
                    <option value="completed">Selesai (Ditutup)</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="edit_cpc" class="block text-xs font-bold text-slate-300 mb-1">CPC (Koin per Klik)</label>
                    <input type="number" id="edit_cpc" name="cpc" required min="2" max="1000"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700/80 rounded-xl text-white text-xs font-mono font-bold focus:outline-none focus:border-indigo-400">
                </div>
                <div>
                    <label for="edit_max_clicks" class="block text-xs font-bold text-slate-300 mb-1">Target Total Klik</label>
                    <input type="number" id="edit_max_clicks" name="max_clicks" required min="1" max="100000"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700/80 rounded-xl text-white text-xs font-mono font-bold focus:outline-none focus:border-indigo-400">
                </div>
            </div>

            <div class="flex items-center space-x-2 pt-2">
                <button type="button" onclick="closeEditTaskModal()" class="flex-1 py-3 rounded-2xl bg-slate-800 text-slate-300 text-xs font-bold hover:bg-slate-700 transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-black shadow-lg shadow-indigo-600/25 transition-all active:scale-95">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditTaskModal(id, status, cpc, maxClicks, platform, owner) {
        document.getElementById('edit_task_id').value = id;
        document.getElementById('edit_task_label').innerText = `#${id} (${platform.toUpperCase()})`;
        document.getElementById('edit_task_owner').innerText = owner;
        document.getElementById('edit_status').value = status;
        document.getElementById('edit_cpc').value = cpc;
        document.getElementById('edit_max_clicks').value = maxClicks;
        document.getElementById('editTaskModal').classList.remove('hidden');
    }

    function closeEditTaskModal() {
        document.getElementById('editTaskModal').classList.add('hidden');
    }
</script>
