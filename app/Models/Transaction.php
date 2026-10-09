<?php
/**
 * PAKAINI - Transaction Model
 * Pencatatan mutasi koin (klaim, bayar, pajak sistem, denda, topup, bonus daftar)
 */

require_once ROOT_PATH . '/app/Core/Model.php';

class Transaction extends Model {

    public function getByUser(int $userId, int $limit = 50): array {
        $sql = "SELECT * FROM transactions WHERE user_id = :uid ORDER BY id DESC LIMIT :lim";
        return $this->fetchAll($sql, ['uid' => $userId, 'lim' => $limit]);
    }

    public function getAll(int $limit = 100): array {
        $sql = "SELECT t.*, u.username, u.is_vip 
                FROM transactions t
                JOIN users u ON t.user_id = u.id
                ORDER BY t.id DESC LIMIT :lim";
        return $this->fetchAll($sql, ['lim' => $limit]);
    }

    /**
     * Cari & Filter Transaksi untuk Admin Buku Kas
     */
    public function searchAndFilter(?string $type = null, string $search = '', int $limit = 100): array {
        $sql = "SELECT t.*, u.username, u.email, u.is_vip 
                FROM transactions t
                JOIN users u ON t.user_id = u.id
                WHERE 1=1";
        $params = [];

        if (!empty($type) && $type !== 'all') {
            $sql .= " AND t.type = :type";
            $params['type'] = $type;
        }

        if (!empty($search)) {
            $sql .= " AND (u.username LIKE :q OR u.email LIKE :q OR t.description LIKE :q)";
            $params['q'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY t.id DESC LIMIT " . (int)$limit;
        return $this->fetchAll($sql, $params);
    }

    /**
     * Dapatkan daftar tipe transaksi yang ada
     */
    public function getDistinctTypes(): array {
        return [
            'all'            => 'Semua Jenis Mutasi',
            'topup'          => 'Top Up & Bonus Admin',
            'klaim_tugas'    => 'Klaim Tugas (Reward)',
            'bayar_tugas'    => 'Bayar Kampanye (Pasang Tugas)',
            'pajak_sistem'   => 'Pajak Sistem (Anti-Inflasi)',
            'denda'          => 'Denda Kecurangan / Potongan',
            'bonus_daftar'   => 'Bonus Registrasi Akun',
            'refund_laporan' => 'Refund Laporan Pelapor'
        ];
    }

    /**
     * Statistik Ekonomi Sistem untuk Admin Dashboard
     */
    public function getEconomyStats(): array {
        $totalCoinsInCirculation = (int)$this->fetchColumn("SELECT SUM(coins) FROM users WHERE coins > 0");
        $totalDebt = (int)$this->fetchColumn("SELECT ABS(SUM(coins)) FROM users WHERE coins < 0");
        $totalTaxBurned = (int)$this->fetchColumn("SELECT ABS(SUM(amount)) FROM transactions WHERE type = 'pajak_sistem'");
        $totalPenalties = (int)$this->fetchColumn("SELECT ABS(SUM(amount)) FROM transactions WHERE type = 'denda'");
        $totalTopups = (int)$this->fetchColumn("SELECT SUM(amount) FROM transactions WHERE type = 'topup'");
        $totalTasksCompleted = (int)$this->fetchColumn("SELECT COUNT(*) FROM task_completions");

        return [
            'coins_in_circulation' => $totalCoinsInCirculation ?: 0,
            'user_debt'            => $totalDebt ?: 0,
            'tax_burned'           => $totalTaxBurned ?: 0,
            'penalties_issued'     => $totalPenalties ?: 0,
            'topups_processed'     => $totalTopups ?: 0,
            'tasks_completed'      => $totalTasksCompleted ?: 0
        ];
    }
}
