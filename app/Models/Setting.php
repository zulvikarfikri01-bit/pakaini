<?php
/**
 * PAKAINI - Setting Model
 * Pengaturan Dinamis Website & Kebijakan Ekonomi Anti-Inflasi
 */

require_once ROOT_PATH . '/app/Core/Model.php';

class Setting extends Model {
    protected string $table = 'settings';

    /**
     * Dapatkan nilai pengaturan berdasarkan key
     */
    public function get(string $key, $default = null) {
        $stmt = $this->db->prepare("SELECT setting_value FROM {$this->table} WHERE setting_key = ? LIMIT 1");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return ($val !== false) ? $val : $default;
    }

    /**
     * Ambil semua pengaturan sistem sebagai array asosiatif
     */
    public function getAllSettings(): array {
        $defaults = [
            'site_name'                => 'PAKAINI',
            'site_tagline'             => 'Platform Social Media Exchange #1 di Indonesia',
            'wa_admin'                 => defined('ADMIN_WHATSAPP') ? ADMIN_WHATSAPP : '6285143253217',
            'coin_rate_idr'            => defined('COIN_RATE_IDR') ? COIN_RATE_IDR : 25,
            'regular_tax_rate'         => defined('REGULAR_TAX_RATE') ? REGULAR_TAX_RATE : 0.20,
            'regular_daily_max_clicks' => defined('REGULAR_DAILY_MAX_CLICKS') ? REGULAR_DAILY_MAX_CLICKS : 50,
            'regular_max_active_tasks' => defined('REGULAR_MAX_ACTIVE_TASKS') ? REGULAR_MAX_ACTIVE_TASKS : 2,
            'new_user_bonus_coins'     => defined('NEW_USER_BONUS_COINS') ? NEW_USER_BONUS_COINS : 500,
            'new_user_vip_days'        => defined('NEW_USER_VIP_DAYS') ? NEW_USER_VIP_DAYS : 3,
            'penalty_multiplier'       => defined('PENALTY_MULTIPLIER') ? PENALTY_MULTIPLIER : 3,
            'broadcast_announcement'   => ''
        ];

        try {
            $stmt = $this->db->query("SELECT setting_key, setting_value FROM {$this->table}");
            $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            return array_merge($defaults, $rows ?: []);
        } catch (PDOException $e) {
            return $defaults;
        }
    }

    /**
     * Simpan atau perbarui nilai pengaturan tunggal
     */
    public function set(string $key, string $value): bool {
        $driver = Database::getDriver();
        if ($driver === 'pgsql') {
            $sql = "INSERT INTO {$this->table} (setting_key, setting_value) VALUES (?, ?) 
                    ON CONFLICT (setting_key) DO UPDATE SET setting_value = EXCLUDED.setting_value";
        } elseif ($driver === 'sqlite') {
            $sql = "INSERT OR REPLACE INTO {$this->table} (setting_key, setting_value) VALUES (?, ?)";
        } else {
            $sql = "INSERT INTO {$this->table} (setting_key, setting_value) VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$key, $value]);
    }

    /**
     * Simpan banyak pengaturan sekaligus
     */
    public function setMany(array $settings): bool {
        foreach ($settings as $k => $v) {
            $this->set($k, (string)$v);
        }
        return true;
    }
}
