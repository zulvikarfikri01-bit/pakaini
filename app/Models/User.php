<?php
/**
 * PAKAINI - User Model
 * Manajemen akun, saldo koin, referral, dan sistem kasta VIP
 */

require_once ROOT_PATH . '/app/Core/Model.php';

class User extends Model {

    public function findById(int $id): ?array {
        return $this->fetch("SELECT * FROM users WHERE id = :id LIMIT 1", ['id' => $id]);
    }

    public function findByUsername(string $username): ?array {
        return $this->fetch("SELECT * FROM users WHERE username = :u LIMIT 1", ['u' => $username]);
    }

    public function findByEmail(string $email): ?array {
        return $this->fetch("SELECT * FROM users WHERE email = :e LIMIT 1", ['e' => $email]);
    }

    /**
     * Pendaftaran Pengguna Baru:
     * - Bonus Awal 500 Koin
     * - Trial VIP 3 Hari
     * - Otomatis mencatat mutasi transaksi bonus_daftar
     */
    public function register(string $username, string $email, string $password, ?string $referredBy = null): int {
        $this->beginTransaction();

        try {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            
            $today = date('Y-m-d');

            // Validasi apakah referrer memang ada
            $validReferrer = null;
            if (!empty($referredBy)) {
                $refCheck = $this->findByUsername($referredBy);
                if ($refCheck && $refCheck['username'] !== $username) {
                    $validReferrer = $refCheck['username'];
                }
            }

            // Akun pertama yang mendaftar otomatis mendapat role = 'admin' (Aturan 1)
            $countUsers = (int)$this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
            $assignedRole = ($countUsers === 0) ? 'admin' : 'user';
            $initialCoins = ($assignedRole === 'admin') ? 10000 : 0;
            $isVip = ($assignedRole === 'admin') ? 1 : 0;

            try {
                $sql = "INSERT INTO users (username, email, password, password_hash, role, coins, is_vip, vip_expired_at, has_claimed_wa_vip, referred_by, claimed_referral_count, daily_clicks, last_active_date, active_days_streak)
                        VALUES (:username, :email, :password, :password_hash, :role, :coins, :is_vip, NULL, 0, :referred_by, 0, 0, :today, 1)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    'username'      => $username,
                    'email'         => $email,
                    'password'      => $passwordHash,
                    'password_hash' => $passwordHash,
                    'role'          => $assignedRole,
                    'coins'         => $initialCoins,
                    'is_vip'        => $isVip,
                    'referred_by'   => $validReferrer,
                    'today'         => $today
                ]);
            } catch (Exception $eBoth) {
                try {
                    $sql = "INSERT INTO users (username, email, password_hash, role, coins, is_vip, vip_expired_at, has_claimed_wa_vip, referred_by, claimed_referral_count, daily_clicks, last_active_date, active_days_streak)
                            VALUES (:username, :email, :password_hash, :role, :coins, :is_vip, NULL, 0, :referred_by, 0, 0, :today, 1)";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([
                        'username'      => $username,
                        'email'         => $email,
                        'password_hash' => $passwordHash,
                        'role'          => $assignedRole,
                        'coins'         => $initialCoins,
                        'is_vip'        => $isVip,
                        'referred_by'   => $validReferrer,
                        'today'         => $today
                    ]);
                } catch (Exception $eHash) {
                    $sql = "INSERT INTO users (username, email, password, role, coins, is_vip, vip_expired_at, has_claimed_wa_vip, referred_by, claimed_referral_count, daily_clicks, last_active_date, active_days_streak)
                            VALUES (:username, :email, :password, :role, :coins, :is_vip, NULL, 0, :referred_by, 0, 0, :today, 1)";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([
                        'username'      => $username,
                        'email'         => $email,
                        'password'      => $passwordHash,
                        'role'          => $assignedRole,
                        'coins'         => $initialCoins,
                        'is_vip'        => $isVip,
                        'referred_by'   => $validReferrer,
                        'today'         => $today
                    ]);
                }
            }

            $userId = (int)$this->lastInsertId('users_id_seq');

            $this->commit();
            return $userId;
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Modifikasi Koin User (Bisa bernilai positif atau negatif)
     * Beroperasi murni pada kolom tunggal `coins`
     */
    public function updateCoins(int $userId, int $amount, string $description, string $type, ?int $refId = null): bool {
        $this->beginTransaction();

        try {
            $user = $this->findById($userId);
            if (!$user) {
                $this->rollBack();
                return false;
            }

            // Sistem 1 Dompet Tunggal: coins murni
            $sql = "UPDATE users SET coins = coins + :amt WHERE id = :id";
            $this->execute($sql, ['amt' => $amount, 'id' => $userId]);

            $txSql = "INSERT INTO transactions (user_id, type, amount, description, reference_id)
                      VALUES (:user_id, :type, :amount, :description, :ref_id)";
            $this->execute($txSql, [
                'user_id'     => $userId,
                'type'        => $type,
                'amount'      => $amount,
                'description' => $description,
                'ref_id'      => $refId
            ]);

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollBack();
            return false;
        }
    }

    /**
     * Sistem 1 Dompet Tunggal (Koin Murni)
     * Tidak diperlukan transfer antar dompet karena saldo koin sudah terintegrasi
     */
    public function convertPenghasilanToIklan(int $userId, int $coins): array {
        return [
            'success' => true,
            'message' => 'Sistem menggunakan 1 Dompet Tunggal (Koin Murni). Seluruh saldo koin sudah otomatis siap digunakan untuk beriklan!'
        ];
    }

    /**
     * Pengajuan Penarikan Dana Tunai Ditiadakan (Versi MVP 1 Dompet Koin Murni)
     */
    public function requestWithdrawal(int $userId, int $coins, string $method, string $accountNumber, string $accountName): array {
        return [
            'success' => false,
            'message' => 'Fitur pencairan tunai telah ditiadakan. Koin digunakan murni untuk ekosistem beriklan.'
        ];
    }

    /**
     * Perpanjang / Berikan Status VIP
     */
    public function extendVip(int $userId, int $days): bool {
        $user = $this->findById($userId);
        if (!$user) return false;

        $isSqlite = Database::getDriver() === 'sqlite';
        $currentExpiry = !empty($user['vip_expired_at']) ? strtotime($user['vip_expired_at']) : 0;
        $baseTime = ($currentExpiry > time()) ? $currentExpiry : time();
        $newExpiry = date('Y-m-d H:i:s', $baseTime + ($days * 86400));

        $sql = "UPDATE users SET is_vip = 1, vip_expired_at = :new_expiry WHERE id = :id";
        return $this->execute($sql, ['new_expiry' => $newExpiry, 'id' => $userId]);
    }

    /**
     * Klaim Hadiah Grup WhatsApp:
     * - Status is_vip = 1
     * - vip_expired_at = +3 hari dari sekarang
     * - has_claimed_wa_vip = 1
     * - Bonus Saldo 500 Koin Iklan
     */
    public function claimWaVipBonus(int $userId): bool {
        $user = $this->findById($userId);
        if (!$user) return false;
        if ((int)($user['has_claimed_wa_vip'] ?? 0) === 1) {
            return false; // Sudah pernah klaim
        }

        $this->beginTransaction();
        try {
            $vipExpiredAt = date('Y-m-d H:i:s', strtotime('+3 days'));

            $sql = "UPDATE users 
                    SET is_vip = 1, 
                        vip_expired_at = :exp, 
                        has_claimed_wa_vip = 1, 
                        coins = coins + 500
                    WHERE id = :id";
            $this->execute($sql, ['exp' => $vipExpiredAt, 'id' => $userId]);

            // Catat mutasi transaksi
            $txSql = "INSERT INTO transactions (user_id, type, amount, description, reference_id)
                      VALUES (:user_id, 'bonus_wa_vip', 500, 'Bonus Gabung Grup WhatsApp (+500 Koin Iklan + VIP Sultan 3 Hari)', :ref_id)";
            $txStmt = $this->db->prepare($txSql);
            $txStmt->execute([
                'user_id' => $userId,
                'ref_id'  => $userId
            ]);

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollBack();
            error_log("claimWaVipBonus error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ambil data referral untuk pengguna tertentu & kalkulasi status Anti-Tuyul:
     * - LULUS: Jika is_vip = 1 ATAU active_days_streak >= 10
     * - PROSES: Jika active_days_streak 1 - 9 & aktif dalam 3 hari terakhir
     * - GAGAL: Jika sudah lewat 3 hari dari last_active_date dan streak belum 10
     */
    public function getReferralNetwork(string $username): array {
        $sql = "SELECT id, username, email, is_vip, vip_expired_at, last_active_date, active_days_streak, created_at
                FROM users 
                WHERE referred_by = :u 
                ORDER BY id DESC";
        $referrals = $this->fetchAll($sql, ['u' => $username]);

        $today = new DateTime();
        $lulusCount = 0;
        $prosesCount = 0;
        $gagalCount = 0;

        $analyzed = [];
        foreach ($referrals as $ref) {
            $lastActiveDate = $ref['last_active_date'] ? new DateTime($ref['last_active_date']) : new DateTime($ref['created_at']);
            $diffDays = (int)$today->diff($lastActiveDate)->format('%a');
            $streak = (int)$ref['active_days_streak'];

            // SYARAT MUTLAK: Hanya dihitung sah jika active_days_streak minimal 10 hari (Status VIP diabaikan)
            if ($streak >= REFERRAL_MIN_ACTIVE_DAYS) {
                $status = 'Lulus (10 Hari)';
                $badgeClass = 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30';
                $lulusCount++;
            } elseif ($diffDays > REFERRAL_INACTIVE_LIMIT_DAYS && $streak < REFERRAL_MIN_ACTIVE_DAYS) {
                $status = 'Gagal (Inaktif)';
                $badgeClass = 'bg-rose-500/10 text-rose-400 border border-rose-500/30';
                $gagalCount++;
            } else {
                $status = "Proses (Hari ke-{$streak})";
                $badgeClass = 'bg-amber-500/10 text-amber-400 border border-amber-500/30';
                $prosesCount++;
            }

            $ref['validation_status'] = $status;
            $ref['badge_class'] = $badgeClass;
            $ref['days_inactive'] = $diffDays;
            $analyzed[] = $ref;
        }

        // Ambil data user untuk kuota klaim
        $userRow = $this->fetch("SELECT claimed_referral_count FROM users WHERE username = :u LIMIT 1", ['u' => $username]);
        $claimedCount = (int)($userRow['claimed_referral_count'] ?? 0);
        $maxQuota = defined('REFERRAL_MAX_QUOTA') ? REFERRAL_MAX_QUOTA : 50;
        $remainingQuota = max(0, $maxQuota - $lulusCount);

        // Ambil riwayat klaim referral
        $claims = [];
        try {
            $userObj = $this->findByUsername($username);
            if ($userObj) {
                $claims = $this->fetchAll("SELECT * FROM referral_claims WHERE user_id = :uid ORDER BY id DESC LIMIT 10", ['uid' => $userObj['id']]);
            }
        } catch (Exception $e) {}

        return [
            'list'            => $analyzed,
            'total'           => count($analyzed),
            'lulus_count'     => $lulusCount,
            'proses_count'    => $prosesCount,
            'gagal_count'     => $gagalCount,
            'max_quota'       => $maxQuota,
            'claimed_count'   => $claimedCount,
            'remaining_quota' => $remainingQuota,
            'target'          => $maxQuota,
            'progress_pct'    => min(100, round(($lulusCount / $maxQuota) * 100)),
            'claims_history'  => $claims
        ];
    }

    /**
     * Klaim Hadiah Referral FOMO:
     * - Batas maksimal 50 referral per user
     * - Sisa kuota dihitung: max(0, 50 - claimed_referral_count)
     * - Syarat: Teman harus lulus (streak >= 10 hari)
     * - Syarat Wajib: Link postingan promosi di medsos harus diisi & valid URL
     * - Opsi Hadiah:
     *   1. 'coins' (1.500 Koin) -> langsung tambah 1.500 Koin ke saldo coins
     */
    public function claimReferralBounty(int $userId, string $rewardType, string $promoUrl): array {
        $promoUrl = trim($promoUrl);
        if (empty($promoUrl) || !filter_var($promoUrl, FILTER_VALIDATE_URL)) {
            return [
                'success' => false,
                'message' => 'Link Postingan Promosi di Medsos wajib diisi dengan alamat tautan (URL) yang valid.'
            ];
        }

        $user = $this->findById($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'Pengguna tidak ditemukan.'];
        }

        $network = $this->getReferralNetwork($user['username']);
        $qualifiedCount = $network['lulus_count'];
        $claimedCount = (int)($user['claimed_referral_count'] ?? 0);

        $maxQuota = defined('REFERRAL_MAX_QUOTA') ? REFERRAL_MAX_QUOTA : 50;
        $remainingQuota = max(0, $maxQuota - $claimedCount);

        if ($remainingQuota <= 0) {
            return [
                'success' => false,
                'message' => 'Batas kuota maksimal 50 referral telah tercapai untuk akun Anda. Terima kasih atas partisipasi Anda!'
            ];
        }

        $unclaimedQualified = $qualifiedCount - $claimedCount;
        if ($unclaimedQualified <= 0) {
            return [
                'success' => false,
                'message' => 'Anda belum memiliki teman referral berstatus LULUS (streak 10 hari) yang dapat diklaim.'
            ];
        }

        $batchClaim = min($unclaimedQualified, $remainingQuota);

        $this->beginTransaction();
        try {
            $newClaimedCount = $claimedCount + $batchClaim;
            $this->execute("UPDATE users SET claimed_referral_count = :cnt WHERE id = :id", [
                'cnt' => $newClaimedCount,
                'id'  => $userId
            ]);

            if ($rewardType === 'coins' || $rewardType === 'koin_iklan') {
                $coins = defined('REFERRAL_BOUNTY_ADS_COIN') ? REFERRAL_BOUNTY_ADS_COIN : 1500;
                $this->execute("UPDATE users SET coins = coins + :c WHERE id = :id", [
                    'c'  => $coins,
                    'id' => $userId
                ]);

                $this->execute("INSERT INTO transactions (user_id, type, amount, description, reference_id)
                                VALUES (:uid, 'hadiah_referral', :amt, :desc, :ref)", [
                    'uid'  => $userId,
                    'amt'  => $coins,
                    'desc' => "Klaim Bonus Referral (+{$coins} Koin) - Bukti Medsos: " . substr($promoUrl, 0, 80),
                    'ref'  => $userId
                ]);

                $this->execute("INSERT INTO referral_claims (user_id, reward_type, reward_amount, promo_url, claimed_referrals_count, status)
                                VALUES (:uid, 'coins', :amt, :url, :batch, 'completed')", [
                    'uid'   => $userId,
                    'amt'   => $coins,
                    'url'   => $promoUrl,
                    'batch' => $batchClaim
                ]);

                $this->commit();
                return [
                    'success' => true,
                    'reward_type' => 'coins',
                    'amount' => $coins,
                    'message' => "🎉 Selamat! {$coins} Koin telah otomatis masuk ke akun Anda!"
                ];
            } else {
                $cashAmount = defined('REFERRAL_BOUNTY_CASH') ? REFERRAL_BOUNTY_CASH : 50000;
                $this->execute("INSERT INTO referral_claims (user_id, reward_type, reward_amount, promo_url, claimed_referrals_count, status)
                                VALUES (:uid, 'cash', :amt, :url, :batch, 'pending')", [
                    'uid'   => $userId,
                    'amt'   => $cashAmount,
                    'url'   => $promoUrl,
                    'batch' => $batchClaim
                ]);

                $claimId = (int)$this->lastInsertId('referral_claims_id_seq');

                $this->commit();
                return [
                    'success' => true,
                    'reward_type' => 'cash',
                    'claim_id' => $claimId,
                    'amount' => $cashAmount,
                    'promo_url' => $promoUrl,
                    'message' => "🎉 Berhasil mengajukan klaim Uang Tunai Rp " . number_format($cashAmount, 0, ',', '.') . "! Silakan konfirmasi ke WhatsApp Admin untuk proses pencairan."
                ];
            }
        } catch (Exception $e) {
            $this->rollBack();
            return ['success' => false, 'message' => 'Gagal memproses klaim referral: ' . $e->getMessage()];
        }
    }

    /**
     * Cari user (Untuk Admin Dashboard)
     */
    public function search(string $query = '', int $limit = 50): array {
        if (empty($query)) {
            return $this->fetchAll("SELECT * FROM users ORDER BY id DESC LIMIT :lim", ['lim' => $limit]);
        }
        $like = "%{$query}%";
        return $this->fetchAll("SELECT * FROM users WHERE username LIKE :q1 OR email LIKE :q2 ORDER BY id DESC LIMIT :lim", [
            'q1' => $like,
            'q2' => $like,
            'lim' => $limit
        ]);
    }

    /**
     * Setel koin user ke angka tertentu (Admin Action)
     */
    public function setExactCoins(int $userId, int $newCoins, string $reason, ?int $adminId = null): bool {
        $user = $this->findById($userId);
        if (!$user) return false;

        $diff = $newCoins - (int)$user['coins'];
        $type = ($diff >= 0) ? 'topup' : 'denda';
        $desc = "Penyesuaian Saldo Admin ke {$newCoins} Koin: " . ($diff >= 0 ? "+{$diff}" : "{$diff}") . " ({$reason})";

        $this->beginTransaction();
        try {
            $this->execute("UPDATE users SET coins = :c WHERE id = :id", ['c' => $newCoins, 'id' => $userId]);
            $this->execute("INSERT INTO transactions (user_id, type, amount, description, reference_id) 
                            VALUES (:uid, :type, :amt, :desc, :ref)", [
                'uid'  => $userId,
                'type' => $type,
                'amt'  => $diff,
                'desc' => $desc,
                'ref'  => $adminId
            ]);
            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollBack();
            return false;
        }
    }

    /**
     * Nonaktifkan status VIP (Admin Action)
     */
    public function deactivateVip(int $userId): bool {
        return $this->execute("UPDATE users SET is_vip = 0, vip_expired_at = NULL WHERE id = :id", ['id' => $userId]);
    }

    /**
     * Ubah role akun (Admin / User biasa)
     */
    public function updateRole(int $userId, string $role): bool {
        if (!in_array($role, ['user', 'admin'])) return false;
        return $this->execute("UPDATE users SET role = :r WHERE id = :id", ['r' => $role, 'id' => $userId]);
    }

    /**
     * Reset password user secara instan oleh Admin
     */
    public function resetPassword(int $userId, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        try {
            return $this->execute("UPDATE users SET password = :h, password_hash = :h WHERE id = :id", ['h' => $hash, 'id' => $userId]);
        } catch (Exception $e) {
            try {
                return $this->execute("UPDATE users SET password = :h WHERE id = :id", ['h' => $hash, 'id' => $userId]);
            } catch (Exception $e2) {
                return $this->execute("UPDATE users SET password_hash = :h WHERE id = :id", ['h' => $hash, 'id' => $userId]);
            }
        }
    }

    /**
     * Reset kuota klik harian user
     */
    public function resetDailyClicks(int $userId): bool {
        return $this->execute("UPDATE users SET daily_clicks = 0 WHERE id = :id", ['id' => $userId]);
    }

    /**
     * Hapus akun pengguna secara permanen
     */
    public function deleteUser(int $userId): bool {
        return $this->execute("DELETE FROM users WHERE id = :id", ['id' => $userId]);
    }

    /**
     * Buat user baru langsung dari Admin Panel
     */
    public function createByAdmin(string $username, string $email, string $password, string $role = 'user', int $coins = 500, int $isVip = 0, int $vipDays = 30): int {
        $today = date('Y-m-d');
        $vipExpired = $isVip ? date('Y-m-d H:i:s', time() + ($vipDays * 86400)) : null;

        try {
            $sql = "INSERT INTO users (username, email, password, password_hash, role, coins, is_vip, vip_expired_at, daily_clicks, last_active_date, active_days_streak)
                    VALUES (:u, :e, :p, :p, :r, :c, :v, :exp, 0, :today, 1)";
            $this->execute($sql, [
                'u'     => $username, 'e' => $email, 'p' => $passwordHash,
                'r'     => $role, 'c' => $coins, 'v' => $isVip, 'exp' => $vipExpired,
                'today' => $today
            ]);
        } catch (Exception $eBoth) {
            try {
                $sql = "INSERT INTO users (username, email, password_hash, role, coins, is_vip, vip_expired_at, daily_clicks, last_active_date, active_days_streak)
                        VALUES (:u, :e, :p, :r, :c, :v, :exp, 0, :today, 1)";
                $this->execute($sql, [
                    'u'     => $username, 'e' => $email, 'p' => $passwordHash,
                    'r'     => $role, 'c' => $coins, 'v' => $isVip, 'exp' => $vipExpired,
                    'today' => $today
                ]);
            } catch (Exception $eHash) {
                $sql = "INSERT INTO users (username, email, password, role, coins, is_vip, vip_expired_at, daily_clicks, last_active_date, active_days_streak)
                        VALUES (:u, :e, :p, :r, :c, :v, :exp, 0, :today, 1)";
                $this->execute($sql, [
                    'u'     => $username, 'e' => $email, 'p' => $passwordHash,
                    'r'     => $role, 'c' => $coins, 'v' => $isVip, 'exp' => $vipExpired,
                    'today' => $today
                ]);
            }
        }

        $newId = (int)$this->lastInsertId('users_id_seq');

        if ($coins > 0) {
            $this->execute("INSERT INTO transactions (user_id, type, amount, description, reference_id) 
                            VALUES (:uid, 'topup', :amt, 'Saldo Awal Pembuatan Akun oleh Admin', :ref)", [
                'uid' => $newId,
                'amt' => $coins,
                'ref' => $newId
            ]);
        }

        return $newId;
    }

    /**
     * Tambah strike kecurangan pada user (Aturan 2: Sistem Strike 3x Banned)
     * Jika strike mencapai 3: is_banned = 1 dan saldo koin dihanguskan (coins = 0)
     */
    public function addStrike(int $userId): array {
        $user = $this->findById($userId);
        if (!$user) return ['success' => false];

        $currentStrikes = (int)($user['strike_count'] ?? 0);
        $newStrikes = $currentStrikes + 1;
        $isBanned = ($newStrikes >= 3) ? 1 : 0;

        if ($isBanned) {
            $this->execute("UPDATE users SET strike_count = :s, is_banned = 1, coins = 0 WHERE id = :id", [
                's'  => $newStrikes,
                'id' => $userId
            ]);
        } else {
            $this->execute("UPDATE users SET strike_count = :s WHERE id = :id", [
                's'  => $newStrikes,
                'id' => $userId
            ]);
        }

        return [
            'success'      => true,
            'strike_count' => $newStrikes,
            'is_banned'    => $isBanned
        ];
    }

    /**
     * Cek apakah akun pengguna diblokir
     */
    public function isBanned(int $userId): bool {
        $user = $this->findById($userId);
        return $user && ((int)($user['is_banned'] ?? 0) === 1);
    }
}
