<?php
/**
 * PAKAINI - Authentication & Daily Routine Service
 * Menangani Session, Daily Reset Clicks, Active Days Streak, dan Expired VIP Check
 */

defined('ROOT_PATH') or define('ROOT_PATH', dirname(__DIR__, 2));
require_once ROOT_PATH . '/app/Core/Model.php';

class Auth {
    private static ?array $user = null;

    public static function init(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            self::syncUser($_SESSION['user_id']);
        }
    }

    /**
     * Sinkronisasi data user dari DB, jalankan Daily Reset & VIP Check
     */
    public static function syncUser(int $userId): ?array {
        $db = Database::getConnection();
        
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        if (!$user || (int)($user['is_banned'] ?? 0) === 1) {
            unset($_SESSION['user_id']);
            self::$user = null;
            return null;
        }

        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $lastActive = $user['last_active_date'] ? date('Y-m-d', strtotime($user['last_active_date'])) : null;
        
        $updates = [];
        $params = ['id' => $userId];

        // 1. Cek & Reset Daily Clicks serta Perbarui Active Days Streak
        if ($lastActive !== $today) {
            // Reset klik harian ke 0
            $updates[] = "daily_clicks = 0";
            $updates[] = "last_active_date = :today";
            $params['today'] = $today;

            if ($lastActive === $yesterday) {
                // Login berturut-turut hari berikutnya -> tambah streak 1
                $updates[] = "active_days_streak = active_days_streak + 1";
                $user['active_days_streak'] = (int)$user['active_days_streak'] + 1;
            } elseif ($lastActive === null) {
                // Pengguna baru pertama kali aktif
                $updates[] = "active_days_streak = 1";
                $user['active_days_streak'] = 1;
            } else {
                // Bolong / streak terputus lebih dari 1 hari -> reset streak ke 1
                $updates[] = "active_days_streak = 1";
                $user['active_days_streak'] = 1;
            }
            $user['daily_clicks'] = 0;
            $user['last_active_date'] = $today;
        }

        // 2. Cek Masa Berlaku VIP
        if ((int)$user['is_vip'] === 1 && !empty($user['vip_expired_at'])) {
            $now = time();
            $expiredTime = strtotime($user['vip_expired_at']);
            if ($expiredTime < $now) {
                // VIP telah kadaluwarsa -> turun kasta ke reguler
                $updates[] = "is_vip = 0";
                $user['is_vip'] = 0;
            }
        }

        // Jalankan update jika ada perubahan status harian atau VIP
        if (!empty($updates)) {
            $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :id";
            $updateStmt = $db->prepare($sql);
            $updateStmt->execute($params);
        }

        self::$user = $user;
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'] ?? 'user';
        $_SESSION['user_role'] = $user['role'] ?? 'user';

        return self::$user;
    }

    public static function user(): ?array {
        return self::$user;
    }

    public static function id(): ?int {
        return self::$user['id'] ?? null;
    }

    public static function check(): bool {
        return self::$user !== null;
    }

    public static function isVip(): bool {
        return self::check() && ((int)self::$user['is_vip'] === 1);
    }

    public static function isAdmin(): bool {
        return self::check() && (($userRole = self::$user['role'] ?? 'user') === 'admin');
    }

    public static function login(int $userId): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        $_SESSION['user_id'] = $userId;
        self::syncUser($userId);
    }

    public static function logout(): void {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        self::$user = null;
    }
}
