<?php
/**
 * PAKAINI - Task Model
 * Manajemen Kampanye, Urutan Tampil VIP & CPC, Eksekusi Tugas, dan Pajak Anti-Inflasi
 */

require_once ROOT_PATH . '/app/Core/Model.php';

class Task extends Model {

    public function findById(int $id): ?array {
        $sql = "SELECT t.*, u.username as owner_username, u.is_vip as owner_is_vip, u.coins as owner_coins
                FROM tasks t
                JOIN users u ON t.user_id = u.id
                WHERE t.id = :id LIMIT 1";
        return $this->fetch($sql, ['id' => $id]);
    }

    /**
     * Cek apakah user memiliki tugas wajib admin yang belum diselesaikan
     */
    public function getPendingAdminTasks(int $userId): array {
        $sql = "SELECT t.*, 
                       u.username as owner_username, 
                       u.is_vip as owner_is_vip,
                       u.active_days_streak as owner_streak
                FROM tasks t
                JOIN users u ON t.user_id = u.id
                LEFT JOIN task_history th ON (t.id = th.task_id AND th.user_id = :uid)
                WHERE t.is_admin_task = 1
                  AND t.status = 'active'
                  AND th.id IS NULL
                ORDER BY t.id ASC";
        return $this->fetchAll($sql, ['uid' => $userId]);
    }

    /**
     * Dapatkan daftar tugas yang tersedia untuk dikerjakan oleh $userId:
     * - Monopoli Admin: Jika user belum menyelesaikan semua tugas wajib admin, HANYA tampilkan tugas admin!
     * - Tugas Prioritas (is_priority = 1) SELALU tampil di urutan paling atas!
     */
    public function getAvailableTasks(int $userId, ?string $platform = null, ?string $type = null): array {
        // Cek apakah user adalah admin
        $userObj = (new User())->findById($userId);
        $isAdmin = ($userObj && ($userObj['role'] ?? '') === 'admin');

        // Monopoli Admin: Cek tugas wajib yang belum selesai
        $pendingAdmin = $this->getPendingAdminTasks($userId);
        if (!empty($pendingAdmin) && !$isAdmin) {
            // Pengguna wajib menyelesaikan semua tugas admin terlebih dahulu
            return $pendingAdmin;
        }

        $sql = "SELECT t.*, 
                       u.username as owner_username, 
                       u.is_vip as owner_is_vip,
                       u.active_days_streak as owner_streak
                FROM tasks t
                JOIN users u ON t.user_id = u.id
                LEFT JOIN task_history th ON (t.id = th.task_id AND th.user_id = :uid)
                WHERE t.status = 'active'
                  AND t.current_clicks < t.max_clicks
                  AND (
                      (t.is_priority = 1 AND u.coins >= (t.cpc * 2)) OR
                      (t.is_priority = 0 AND u.coins >= t.cpc)
                  )
                  AND t.user_id != :uid_owner
                  AND th.id IS NULL";

        $params = [
            'uid'       => $userId,
            'uid_owner' => $userId
        ];

        if (!empty($platform) && $platform !== 'all') {
            $sql .= " AND t.platform = :platform";
            $params['platform'] = $platform;
        }

        if (!empty($type) && $type !== 'all') {
            $sql .= " AND t.task_type = :type";
            $params['type'] = $type;
        }

        // Urutan Tampil Wajib:
        // 1. is_priority DESC (Sematkan ke Atas selalu paling atas)
        // 2. u.is_vip DESC (VIP di atas non-VIP)
        // 3. t.cpc DESC (Harga tertinggi di atas)
        $sql .= " ORDER BY t.is_priority DESC, u.is_vip DESC, t.cpc DESC, t.id DESC LIMIT 100";

        return $this->fetchAll($sql, $params);
    }

    /**
     * Dapatkan daftar kampanye milik pengguna tertentu
     */
    public function getUserTasks(int $userId): array {
        $sql = "SELECT * FROM tasks WHERE user_id = :uid ORDER BY is_priority DESC, id DESC";
        return $this->fetchAll($sql, ['uid' => $userId]);
    }

    /**
     * Hitung jumlah tugas aktif yang dimiliki user
     */
    public function countUserActiveTasks(int $userId): int {
        $sql = "SELECT COUNT(*) FROM tasks WHERE user_id = :uid AND status = 'active'";
        return (int)$this->fetchColumn($sql, ['uid' => $userId]);
    }

    /**
     * Dapatkan riwayat user yang telah mengerjakan tugas tertentu milik pembuat kampanye
     * Beserta status laporan (apakah sudah dilaporkan curang)
     */
    public function getTaskCompletions(int $taskId, int $ownerId): array {
        $sql = "SELECT tc.*, 
                       u.username as worker_username, 
                       u.email as worker_email,
                       u.is_vip as worker_is_vip,
                       r.id as report_id,
                       r.status as report_status
                FROM task_completions tc
                JOIN users u ON tc.user_id = u.id
                LEFT JOIN reports r ON (r.task_id = tc.task_id AND r.reported_user_id = tc.user_id)
                WHERE tc.task_id = :tid AND tc.task_owner_id = :oid
                ORDER BY tc.id DESC";
        return $this->fetchAll($sql, ['tid' => $taskId, 'oid' => $ownerId]);
    }

    /**
     * Buat Tugas Baru (Pasang Kampanye)
     * - Memotong/Validasi terhadap Saldo Koin (coins)
     * - Opsi Prioritas (is_priority = 1, Biaya 2x Lipat)
     */
    public function createTask(int $userId, string $platform, string $taskType, string $url, int $cpc, int $maxClicks, bool $isVip, bool $isPriority = false): array {
        global $PLATFORM_MIN_CPC;

        // Validasi platform & task type
        $minCpc = 20; // Default minimum
        if (isset($PLATFORM_MIN_CPC[$platform][$taskType])) {
            $minCpc = $PLATFORM_MIN_CPC[$platform][$taskType];
        } elseif ($taskType === 'likes') {
            $minCpc = 20;
        }

        if ($cpc < $minCpc) {
            return [
                'success' => false,
                'message' => "Nilai minimal CPC untuk {$platform} ({$taskType}) adalah {$minCpc} Koin."
            ];
        }

        // Validasi Aturan Kasta: User biasa maks 2 tugas aktif
        if (!$isVip) {
            $activeCount = $this->countUserActiveTasks($userId);
            if ($activeCount >= REGULAR_MAX_ACTIVE_TASKS) {
                return [
                    'success' => false,
                    'message' => "User Reguler maksimal memiliki " . REGULAR_MAX_ACTIVE_TASKS . " tugas aktif bersamaan. Upgrade ke VIP Sultan untuk pasang tanpa batas!"
                ];
            }
        }

        // Cek saldo koin user (jika prioritas, biaya 2x lipat)
        $costPerClick = $isPriority ? ($cpc * 2) : $cpc;
        $user = (new User())->findById($userId);
        $userCoins = (int)($user['coins'] ?? 0);

        if (!$user || $userCoins < $costPerClick) {
            $priorityNote = $isPriority ? " (Prioritas Biaya 2x Lipat = {$costPerClick} Koin)" : "";
            return [
                'success' => false,
                'message' => "Saldo Koin Anda ({$userCoins} Koin) tidak mencukupi untuk biaya per klik {$costPerClick} Koin{$priorityNote}. Silakan beli koin di halaman Top-Up terlebih dahulu."
            ];
        }

        $sql = "INSERT INTO tasks (user_id, platform, task_type, task_url, cpc, is_priority, is_admin_task, target_clicks, max_clicks, current_clicks, is_active, status)
                VALUES (:uid, :platform, :type, :url, :cpc, :priority, 0, :target_clicks, :max_clicks, 0, 1, 'active')";
        $this->execute($sql, [
            'uid'           => $userId,
            'platform'      => $platform,
            'type'          => $taskType,
            'url'           => $url,
            'cpc'           => $cpc,
            'priority'      => $isPriority ? 1 : 0,
            'target_clicks' => $maxClicks,
            'max_clicks'    => $maxClicks
        ]);

        return [
            'success' => true,
            'task_id' => (int)$this->lastInsertId('tasks_id_seq'),
            'message' => "Kampanye berhasil dipasang dan langsung aktif!" . ($isPriority ? " (Status: Disematkan ke Atas 🔥)" : "")
        ];
    }

    /**
     * Klaim Tugas & Logika Koin:
     * 1. Cek batas harian user biasa (Maks 50 klik/hari)
     * 2. Pastikan belum pernah dikerjakan oleh user ini
     * 3. Potong saldo koin pembuat kampanye sebesar CPC (atau 2x jika prioritas)
     * 4. User pengklaim menerima imbalan penuh CPC ke saldo koin (coins)
     * 5. Catat log transaksi lengkap tanpa kata pajak
     */
    public function claimTask(int $taskId, int $clickerId): array {
        $this->beginTransaction();

        try {
            $task = $this->findById($taskId);
            if (!$task || $task['status'] !== 'active') {
                $this->rollBack();
                return ['success' => false, 'message' => 'Tugas sudah tidak aktif atau tidak ditemukan.'];
            }

            if ($task['user_id'] == $clickerId) {
                $this->rollBack();
                return ['success' => false, 'message' => 'Anda tidak bisa mengerjakan tugas milik Anda sendiri.'];
            }

            if ($task['current_clicks'] >= $task['max_clicks']) {
                $this->rollBack();
                return ['success' => false, 'message' => 'Tugas ini sudah mencapai target maksimal klik.'];
            }

            // Ambil data user pengklik
            $clickerStmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
            $clickerStmt->execute(['id' => $clickerId]);
            $clicker = $clickerStmt->fetch();

            if (!$clicker) {
                $this->rollBack();
                return ['success' => false, 'message' => 'User tidak valid.'];
            }

            $isVip = (int)$clicker['is_vip'] === 1;

            // Batas Klik Harian: User biasa maks 50 klik/hari
            if (!$isVip && (int)$clicker['daily_clicks'] >= REGULAR_DAILY_MAX_CLICKS) {
                $this->rollBack();
                return [
                    'success' => false,
                    'message' => 'Anda telah mencapai batas 50 klik harian untuk Member Biasa. Upgrade ke VIP Sultan untuk klik tanpa batas!'
                ];
            }

            // Cek apakah sudah pernah menyelesaikan tugas ini (Anti-Toggle Effect)
            $checkComp = $this->db->prepare("SELECT id FROM task_history WHERE task_id = :tid AND user_id = :uid LIMIT 1");
            $checkComp->execute(['tid' => $taskId, 'uid' => $clickerId]);
            if ($checkComp->fetch()) {
                $this->rollBack();
                return ['success' => false, 'message' => 'Anda sudah pernah mengerjakan dan mengklaim koin tugas ini sebelumnya.'];
            }

            // Cek saldo koin pembuat tugas
            $ownerStmt = $this->db->prepare("SELECT id, username, coins FROM users WHERE id = :id LIMIT 1");
            $ownerStmt->execute(['id' => $task['user_id']]);
            $owner = $ownerStmt->fetch();

            $cpc = (int)$task['cpc'];
            $isPriority = ((int)($task['is_priority'] ?? 0) === 1);
            $costToOwner = $isPriority ? ($cpc * 2) : $cpc;

            if (!$owner || (int)$owner['coins'] < $costToOwner) {
                // Saldo pemilik habis -> otomatis pause tugas
                $this->execute("UPDATE tasks SET status = 'paused' WHERE id = :id", ['id' => $taskId]);
                $this->commit();
                return ['success' => false, 'message' => 'Kampanye ini dijeda sementara karena saldo koin pembuat kampanye sedang habis.'];
            }

            // Potong saldo koin pembuat kampanye sebesar $costToOwner (1 Dompet: coins)
            $deductOwner = $this->db->prepare("UPDATE users SET coins = coins - :cost WHERE id = :id");
            $deductOwner->execute(['cost' => $costToOwner, 'id' => $owner['id']]);

            // Catat transaksi pembuat kampanye (bayar_tugas)
            $descOwner = "Pembayaran tugas {$task['platform']} ({$task['task_type']}) #{$taskId}" . ($isPriority ? " (Prioritas Sematkan ke Atas 2x Lipat)" : "");
            $txOwner = $this->db->prepare("INSERT INTO transactions (user_id, type, amount, description, reference_id)
                                           VALUES (:uid, 'bayar_tugas', :amt, :desc, :ref)");
            $txOwner->execute([
                'uid'  => $owner['id'],
                'amt'  => -$costToOwner,
                'desc' => $descOwner,
                'ref'  => $taskId
            ]);

            // Pengklik menerima hadiah bersih (Pemotongan Siluman 20%)
            $hadiah_bersih = (int)floor($cpc * 0.8);
            $reward = $hadiah_bersih;
            $addClicker = $this->db->prepare("UPDATE users SET coins = coins + :reward, daily_clicks = daily_clicks + 1 WHERE id = :id");
            $addClicker->execute(['reward' => $reward, 'id' => $clickerId]);

            // Catat transaksi pengklik (klaim_tugas)
            $txClicker = $this->db->prepare("INSERT INTO transactions (user_id, type, amount, description, reference_id)
                                             VALUES (:uid, 'klaim_tugas', :amt, :desc, :ref)");
            $txClicker->execute([
                'uid'  => $clickerId,
                'amt'  => $reward,
                'desc' => "Hadiah tugas {$task['platform']} #{$taskId} (+{$reward} Koin)",
                'ref'  => $taskId
            ]);

            // Simpan riwayat pengerjaan di task_history (Anti-Toggle Permanen)
            $histStmt = $this->db->prepare("INSERT INTO task_history (user_id, task_id, completed_at) VALUES (:uid, :tid, NOW())");
            $histStmt->execute([
                'uid' => $clickerId,
                'tid' => $taskId
            ]);

            // Simpan log pengerjaan di task_completions
            $compSql = "INSERT INTO task_completions (task_id, user_id, task_owner_id, cpc, reward_received, tax_burned)
                        VALUES (:tid, :uid, :oid, :cpc, :reward, 0)";
            $compStmt = $this->db->prepare($compSql);
            $compStmt->execute([
                'tid'    => $taskId,
                'uid'    => $clickerId,
                'oid'    => $task['user_id'],
                'cpc'    => $cpc,
                'reward' => $reward
            ]);

            // Increment current_clicks pada tugas & cek target selesai
            $newClicks = (int)$task['current_clicks'] + 1;
            $statusUpdate = ($newClicks >= (int)$task['max_clicks']) ? 'completed' : 'active';
            
            $updateTask = $this->db->prepare("UPDATE tasks SET current_clicks = :clicks, status = :status WHERE id = :id");
            $updateTask->execute([
                'clicks' => $newClicks,
                'status' => $statusUpdate,
                'id'     => $taskId
            ]);

            $this->commit();

            return [
                'success'         => true,
                'reward_received' => $reward,
                'tax_burned'      => 0,
                'new_balance'     => (int)$clicker['coins'] + $reward,
                'is_vip'          => $isVip,
                'message'         => "Selamat! Anda menerima +{$reward} Koin ke saldo akun Anda."
            ];
        } catch (Exception $e) {
            $this->rollBack();
            return ['success' => false, 'message' => 'Gagal memproses klaim tugas: ' . $e->getMessage()];
        }
    }

    /**
     * Update status kampanye (pause/resume/delete)
     */
    public function updateStatus(int $taskId, int $userId, string $status): bool {
        $allowed = ['active', 'paused', 'completed'];
        if (!in_array($status, $allowed)) return false;

        $sql = "UPDATE tasks SET status = :status WHERE id = :tid AND user_id = :uid";
        return $this->execute($sql, ['status' => $status, 'tid' => $taskId, 'uid' => $userId]);
    }

    /**
     * Ambil semua kampanye untuk Admin dengan filter & pencarian
     */
    public function getAllTasksForAdmin(?string $platform = null, ?string $status = null, string $search = '', int $limit = 100): array {
        $sql = "SELECT t.*, u.username as owner_username, u.email as owner_email, u.is_vip as owner_is_vip, u.coins as owner_coins
                FROM tasks t
                JOIN users u ON t.user_id = u.id
                WHERE 1=1";
        $params = [];

        if (!empty($platform) && $platform !== 'all') {
            $sql .= " AND t.platform = :platform";
            $params['platform'] = $platform;
        }

        if (!empty($status) && $status !== 'all') {
            $sql .= " AND t.status = :status";
            $params['status'] = $status;
        }

        if (!empty($search)) {
            $sql .= " AND (u.username LIKE :q1 OR t.task_url LIKE :q2)";
            $params['q1'] = "%{$search}%";
            $params['q2'] = "%{$search}%";
        }

        $sql .= " ORDER BY t.id DESC LIMIT :lim";
        $params['lim'] = $limit;

        return $this->fetchAll($sql, $params);
    }

    /**
     * Update status, cpc, dan target klik kampanye oleh Admin
     */
    public function adminUpdateTask(int $taskId, string $status, int $cpc, int $maxClicks): bool {
        $allowed = ['active', 'paused', 'completed'];
        if (!in_array($status, $allowed)) return false;

        $sql = "UPDATE tasks SET status = :status, cpc = :cpc, max_clicks = :max WHERE id = :id";
        return $this->execute($sql, [
            'status' => $status,
            'cpc'    => $cpc,
            'max'    => $maxClicks,
            'id'     => $taskId
        ]);
    }

    /**
     * Hapus kampanye secara permanen oleh Admin
     */
    public function adminDeleteTask(int $taskId): bool {
        return $this->execute("DELETE FROM tasks WHERE id = :id", ['id' => $taskId]);
    }
}
