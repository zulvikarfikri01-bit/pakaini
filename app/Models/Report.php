<?php
/**
 * PAKAINI - Report Model (Sistem Laporan Anti-Unfollow & Curang)
 * Penalti 3x CPC bagi pelanggar (saldo bisa minus/hutang) & Pengembalian koin ke pembuat tugas
 */

require_once ROOT_PATH . '/app/Core/Model.php';

class Report extends Model {

    /**
     * Buat laporan curang baru oleh pembuat kampanye
     */
    public function createReport(int $reporterId, int $reportedUserId, int $taskId, string $reason = 'Unfollow / Unsubscribe / Unlike'): array {
        // Cek apakah tugas memang milik reporter
        $taskCheck = $this->fetch("SELECT id FROM tasks WHERE id = :tid AND user_id = :uid", [
            'tid' => $taskId,
            'uid' => $reporterId
        ]);

        if (!$taskCheck) {
            return ['success' => false, 'message' => 'Anda tidak memiliki hak untuk melaporkan tugas ini.'];
        }

        // Cek apakah pengerja memang pernah klaim tugas ini
        $compCheck = $this->fetch("SELECT id FROM task_completions WHERE task_id = :tid AND user_id = :uid", [
            'tid' => $taskId,
            'uid' => $reportedUserId
        ]);

        if (!$compCheck) {
            return ['success' => false, 'message' => 'User yang dilaporkan tidak tercatat mengerjakan tugas ini.'];
        }

        // Cek apakah sudah pernah dilaporkan sebelumnya
        $existing = $this->fetch("SELECT id, status FROM reports WHERE reporter_id = :rid AND reported_user_id = :ruid AND task_id = :tid", [
            'rid'  => $reporterId,
            'ruid' => $reportedUserId,
            'tid'  => $taskId
        ]);

        if ($existing) {
            return ['success' => false, 'message' => "Laporan untuk user ini sudah ada (Status: {$existing['status']})."];
        }

        $sql = "INSERT INTO reports (reporter_id, reported_user_id, task_id, status, reason)
                VALUES (:rid, :ruid, :tid, 'pending', :reason)";
        $this->execute($sql, [
            'rid'    => $reporterId,
            'ruid'   => $reportedUserId,
            'tid'    => $taskId,
            'reason' => $reason
        ]);

        return ['success' => true, 'message' => 'Laporan kecurangan berhasil dikirim. Admin akan segera memverifikasi bukti.'];
    }

    /**
     * Dapatkan semua laporan untuk Admin Panel
     */
    public function getAllReports(?string $status = null): array {
        $sql = "SELECT r.*, 
                       u_rep.username as reporter_username,
                       u_bad.username as reported_username,
                       u_bad.coins as reported_coins,
                       t.platform,
                       t.task_type,
                       t.task_url,
                       t.cpc
                FROM reports r
                JOIN users u_rep ON r.reporter_id = u_rep.id
                JOIN users u_bad ON r.reported_user_id = u_bad.id
                JOIN tasks t ON r.task_id = t.id";

        $params = [];
        if (!empty($status) && $status !== 'all') {
            $sql .= " WHERE r.status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY r.id DESC";
        return $this->fetchAll($sql, $params);
    }

    /**
     * Validasi Laporan oleh Admin:
     * - Status diubah menjadi 'didenda'
     * - Potong saldo reported_user_id sebesar 3x lipat CPC (dapat bernilai MINUS / hutang)
     * - Kembalikan nilai asli CPC koin ke reporter_id
     * - Catat transaksi denda & refund
     */
    public function penalizeCheater(int $reportId, string $adminNotes = ''): array {
        $this->beginTransaction();

        try {
            $sql = "SELECT r.*, t.cpc, t.platform, u_bad.username as cheater_name, u_rep.username as reporter_name
                    FROM reports r
                    JOIN tasks t ON r.task_id = t.id
                    JOIN users u_bad ON r.reported_user_id = u_bad.id
                    JOIN users u_rep ON r.reporter_id = u_rep.id
                    WHERE r.id = :id LIMIT 1";
            $report = $this->fetch($sql, ['id' => $reportId]);

            if (!$report) {
                $this->rollBack();
                return ['success' => false, 'message' => 'Laporan tidak ditemukan.'];
            }

            if ($report['status'] !== 'pending') {
                $this->rollBack();
                return ['success' => false, 'message' => "Laporan ini sudah diproses sebelumnya (Status: {$report['status']})."];
            }

            $cpc = (int)$report['cpc'];
            $penaltyAmount = $cpc * PENALTY_MULTIPLIER; // 3x Lipat CPC

            // 1. Potong saldo reported_user_id sebesar 3x CPC (bisa minus/hutang!)
            $penalizeStmt = $this->db->prepare("UPDATE users SET coins = coins - :penalty WHERE id = :id");
            $penalizeStmt->execute([
                'penalty' => $penaltyAmount,
                'id'      => $report['reported_user_id']
            ]);

            // Catat transaksi denda pada pelaku
            $txCheater = $this->db->prepare("INSERT INTO transactions (user_id, type, amount, description, reference_id)
                                             VALUES (:uid, 'denda', :amt, :desc, :ref)");
            $txCheater->execute([
                'uid'  => $report['reported_user_id'],
                'amt'  => -$penaltyAmount,
                'desc' => "Denda 3x lipat kecurangan unfollow/unsubscribe tugas #{$report['task_id']} ({$report['platform']})",
                'ref'  => $reportId
            ]);

            // 2. Kembalikan nilai asli koin (1x CPC) ke pelapor (pembuat kampanye)
            $refundStmt = $this->db->prepare("UPDATE users SET coins = coins + :refund WHERE id = :id");
            $refundStmt->execute([
                'refund' => $cpc,
                'id'     => $report['reporter_id']
            ]);

            // Catat transaksi refund pada pelapor
            $txReporter = $this->db->prepare("INSERT INTO transactions (user_id, type, amount, description, reference_id)
                                              VALUES (:uid, 'refund_laporan', :amt, :desc, :ref)");
            $txReporter->execute([
                'uid'  => $report['reporter_id'],
                'amt'  => $cpc,
                'desc' => "Pengembalian koin dari laporan valid kecurangan pengerja tugas #{$report['task_id']}",
                'ref'  => $reportId
            ]);

            // 3. Update status laporan menjadi 'didenda'
            $updateRep = $this->db->prepare("UPDATE reports SET status = 'didenda', notes = :notes WHERE id = :id");
            $updateRep->execute([
                'notes' => $adminNotes ?: 'Laporan terbukti valid. Pelaku dikenakan sanksi denda 3x lipat CPC.',
                'id'    => $reportId
            ]);

            $this->commit();

            return [
                'success' => true,
                'message' => "Laporan berhasil divalidasi! Pelaku ({$report['cheater_name']}) telah didenda {$penaltyAmount} Koin (3x lipat) dan saldo pelapor ({$report['reporter_name']}) dikembalikan +{$cpc} Koin."
            ];
        } catch (Exception $e) {
            $this->rollBack();
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }

    /**
     * Tolak Laporan oleh Admin
     */
    public function rejectReport(int $reportId, string $adminNotes = ''): array {
        $sql = "UPDATE reports SET status = 'ditolak', notes = :notes WHERE id = :id";
        $success = $this->execute($sql, [
            'notes' => $adminNotes ?: 'Laporan ditolak oleh Admin setelah peninjauan bukti.',
            'id'    => $reportId
        ]);

        return [
            'success' => $success,
            'message' => $success ? 'Laporan berhasil ditolak.' : 'Gagal memproses penolakan.'
        ];
    }
}
