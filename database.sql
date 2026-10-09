-- ==============================================================================
-- PAKAINI - Social Media Exchange Platform
-- Skema Database PostgreSQL (Siap Import ke Supabase SQL Editor & Railway)
-- ==============================================================================

-- ------------------------------------------------------------------------------
-- 1. Tabel users (Sistem Akun, Koin, VIP Trial & Streak)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    whatsapp_number VARCHAR(30) NULL,
    password VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    coins INT NOT NULL DEFAULT 0,
    is_vip SMALLINT NOT NULL DEFAULT 0,
    vip_expired_at TIMESTAMP NULL,
    has_claimed_wa_vip SMALLINT NOT NULL DEFAULT 0,
    referred_by VARCHAR(50) NULL,
    claimed_referral_count INT NOT NULL DEFAULT 0,
    daily_clicks INT NOT NULL DEFAULT 0,
    active_days_streak INT NOT NULL DEFAULT 1,
    strike_count SMALLINT NOT NULL DEFAULT 0,
    is_banned SMALLINT NOT NULL DEFAULT 0,
    is_active SMALLINT NOT NULL DEFAULT 1,
    last_active_date DATE NULL DEFAULT CURRENT_DATE,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_users_vip ON users(is_vip);
CREATE INDEX IF NOT EXISTS idx_users_banned ON users(is_banned);
CREATE INDEX IF NOT EXISTS idx_users_streak ON users(active_days_streak);
CREATE INDEX IF NOT EXISTS idx_users_referred_by ON users(referred_by);
CREATE INDEX IF NOT EXISTS idx_users_ip ON users(ip_address);

-- ------------------------------------------------------------------------------
-- 2. Tabel tasks (Manajemen Kampanye / Tambah Tugas)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tasks (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    platform VARCHAR(50) NOT NULL,
    task_type VARCHAR(50) NOT NULL DEFAULT 'follow',
    task_url TEXT NOT NULL,
    cpc INT NOT NULL,
    is_priority SMALLINT NOT NULL DEFAULT 0,
    is_admin_task SMALLINT NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    target_clicks INT NOT NULL DEFAULT 100,
    max_clicks INT NOT NULL DEFAULT 100,
    current_clicks INT NOT NULL DEFAULT 0,
    report_count INT NOT NULL DEFAULT 0,
    is_active SMALLINT NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_tasks_user ON tasks(user_id);
CREATE INDEX IF NOT EXISTS idx_tasks_status ON tasks(status);
CREATE INDEX IF NOT EXISTS idx_tasks_active ON tasks(is_active);
CREATE INDEX IF NOT EXISTS idx_tasks_cpc ON tasks(cpc);
CREATE INDEX IF NOT EXISTS idx_tasks_priority ON tasks(is_priority);
CREATE INDEX IF NOT EXISTS idx_tasks_admin ON tasks(is_admin_task);

-- ------------------------------------------------------------------------------
-- 3. Tabel task_completions (Anti-Double Claim & Riwayat Pengerjaan Tugas)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS task_completions (
    id SERIAL PRIMARY KEY,
    task_id INT NOT NULL REFERENCES tasks(id) ON DELETE CASCADE,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    task_owner_id INT NOT NULL DEFAULT 0,
    cpc INT NOT NULL DEFAULT 0,
    reward_received INT NOT NULL DEFAULT 0,
    tax_burned INT NOT NULL DEFAULT 0,
    completed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_task_user UNIQUE (task_id, user_id)
);

CREATE INDEX IF NOT EXISTS idx_tc_user ON task_completions(user_id);
CREATE INDEX IF NOT EXISTS idx_tc_task ON task_completions(task_id);
CREATE INDEX IF NOT EXISTS idx_tc_owner ON task_completions(task_owner_id);

-- ------------------------------------------------------------------------------
-- 4. Tabel task_history (Anti-Toggle Effect Permanen)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS task_history (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    task_id INT NOT NULL REFERENCES tasks(id) ON DELETE CASCADE,
    is_reported SMALLINT NOT NULL DEFAULT 0,
    completed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_task_history_user_task UNIQUE (task_id, user_id)
);

CREATE INDEX IF NOT EXISTS idx_th_user ON task_history(user_id);
CREATE INDEX IF NOT EXISTS idx_th_task ON task_history(task_id);

-- ------------------------------------------------------------------------------
-- 5. Tabel transactions (Buku Kas & Log Mutasi Koin)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS transactions (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    type VARCHAR(50) NOT NULL,
    amount INT NOT NULL,
    reference_id INT NULL,
    date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    description VARCHAR(255) NULL
);

CREATE INDEX IF NOT EXISTS idx_tx_user ON transactions(user_id);
CREATE INDEX IF NOT EXISTS idx_tx_type ON transactions(type);

-- ------------------------------------------------------------------------------
-- 6. Tabel reports (Sistem Laporan Anti-Unfollow / Kecurangan)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS reports (
    id SERIAL PRIMARY KEY,
    reporter_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    reported_user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    task_id INT NOT NULL REFERENCES tasks(id) ON DELETE CASCADE,
    status VARCHAR(30) NOT NULL DEFAULT 'pending',
    reason TEXT NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_reports_reporter ON reports(reporter_id);
CREATE INDEX IF NOT EXISTS idx_reports_reported ON reports(reported_user_id);
CREATE INDEX IF NOT EXISTS idx_reports_task ON reports(task_id);
CREATE INDEX IF NOT EXISTS idx_reports_status ON reports(status);

-- ------------------------------------------------------------------------------
-- 7. Tabel settings (Pengaturan Global & Kebijakan Ekonomi)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS settings (
    id SERIAL PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_settings_key ON settings(setting_key);

-- ------------------------------------------------------------------------------
-- 8. Tabel referral_claims (Klaim Bonus Referral & Validasi Medsos)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS referral_claims (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    reward_type VARCHAR(50) NOT NULL,
    reward_amount INT NOT NULL,
    promo_url TEXT NOT NULL,
    claimed_referrals_count INT NOT NULL DEFAULT 1,
    status VARCHAR(30) NOT NULL DEFAULT 'completed',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_rc_user ON referral_claims(user_id);
CREATE INDEX IF NOT EXISTS idx_rc_status ON referral_claims(status);

-- ==============================================================================
-- SEED DATA AWAL (Akun Demo & Tugas Bawaan)
-- Password untuk semua akun demo bawaan adalah: admin123
-- ==============================================================================

-- Akun Demo
INSERT INTO users (id, username, email, password, password_hash, role, coins, is_vip, vip_expired_at, referred_by, daily_clicks, active_days_streak, last_active_date)
VALUES
(1, 'adminzul', 'adminzul@pakaini.com', '$2y$10$QGYfru58QFigNjOw3zI5iuvqePtjLibDefZw8yVGxIP0DJdOdzEUq', '$2y$10$QGYfru58QFigNjOw3zI5iuvqePtjLibDefZw8yVGxIP0DJdOdzEUq', 'admin', 50000, 1, NOW() + INTERVAL '365 days', NULL, 0, 15, CURRENT_DATE),
(2, 'admin', 'admin@pakaini.com', '$2y$10$QGYfru58QFigNjOw3zI5iuvqePtjLibDefZw8yVGxIP0DJdOdzEUq', '$2y$10$QGYfru58QFigNjOw3zI5iuvqePtjLibDefZw8yVGxIP0DJdOdzEUq', 'admin', 50000, 1, NOW() + INTERVAL '365 days', NULL, 0, 15, CURRENT_DATE),
(3, 'sultan_vip', 'sultan@pakaini.com', '$2y$10$QGYfru58QFigNjOw3zI5iuvqePtjLibDefZw8yVGxIP0DJdOdzEUq', '$2y$10$QGYfru58QFigNjOw3zI5iuvqePtjLibDefZw8yVGxIP0DJdOdzEUq', 'user', 12500, 1, NOW() + INTERVAL '30 days', 'admin', 5, 12, CURRENT_DATE),
(4, 'member_biasa', 'member@pakaini.com', '$2y$10$QGYfru58QFigNjOw3zI5iuvqePtjLibDefZw8yVGxIP0DJdOdzEUq', '$2y$10$QGYfru58QFigNjOw3zI5iuvqePtjLibDefZw8yVGxIP0DJdOdzEUq', 'user', 850, 0, NULL, 'admin', 12, 10, CURRENT_DATE)
ON CONFLICT (username) DO UPDATE SET 
    role = EXCLUDED.role,
    is_vip = EXCLUDED.is_vip,
    coins = EXCLUDED.coins;

-- Kampanye Tugas Awal
INSERT INTO tasks (id, user_id, platform, task_type, task_url, cpc, is_priority, is_admin_task, status, target_clicks, max_clicks, current_clicks, is_active)
VALUES
(1, 1, 'youtube', 'subscribers', 'https://www.youtube.com/@PakainiOfficial?sub_confirmation=1', 150, 1, 1, 'active', 500, 500, 8, 1),
(2, 1, 'instagram', 'followers', 'https://www.instagram.com/pakaini.id/', 50, 1, 1, 'active', 500, 500, 12, 1),
(3, 2, 'tiktok', 'followers', 'https://www.tiktok.com/@pakaini.id', 50, 1, 0, 'active', 200, 200, 18, 1),
(4, 3, 'youtube', 'likes', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 25, 0, 0, 'active', 80, 80, 14, 1)
ON CONFLICT (id) DO NOTHING;

-- Riwayat Transaksi Demo
INSERT INTO transactions (user_id, type, amount, date, description)
VALUES
(1, 'bonus_daftar', 50000, NOW(), 'Inisialisasi Saldo Utama Admin (Saldo Koin)'),
(2, 'bonus_daftar', 500, NOW(), 'Bonus Pendaftaran Akun Baru + Trial VIP'),
(2, 'topup', 12000, NOW(), 'Topup Saldo Koin via WhatsApp Admin'),
(3, 'bonus_daftar', 500, NOW(), 'Bonus Pendaftaran Akun Baru + Trial VIP'),
(3, 'klaim_tugas', 350, NOW(), 'Akumulasi Pengerjaan Tugas (Saldo Koin)');

-- Pengaturan Bawaan Sistem
INSERT INTO settings (setting_key, setting_value)
VALUES
('site_name', 'PAKAINI'),
('site_tagline', 'Platform Social Media Exchange #1 di Indonesia'),
('wa_admin', '6285143253217'),
('coin_rate_idr', '30'),
('regular_tax_rate', '0.0'),
('regular_daily_max_clicks', '50'),
('regular_max_active_tasks', '2'),
('new_user_bonus_coins', '500'),
('new_user_vip_days', '3'),
('penalty_multiplier', '3')
ON CONFLICT (setting_key) DO UPDATE SET setting_value = EXCLUDED.setting_value;

-- ==============================================================================
-- SINKRONISASI SEQUENCE POSTGRESQL (Mencegah Collision ID Setelah Seed)
-- ==============================================================================
SELECT setval('users_id_seq', (SELECT COALESCE(MAX(id), 1) FROM users));
SELECT setval('tasks_id_seq', (SELECT COALESCE(MAX(id), 1) FROM tasks));
SELECT setval('task_completions_id_seq', (SELECT COALESCE(MAX(id), 1) FROM task_completions));
SELECT setval('task_history_id_seq', (SELECT COALESCE(MAX(id), 1) FROM task_history));
SELECT setval('transactions_id_seq', (SELECT COALESCE(MAX(id), 1) FROM transactions));
SELECT setval('reports_id_seq', (SELECT COALESCE(MAX(id), 1) FROM reports));
SELECT setval('settings_id_seq', (SELECT COALESCE(MAX(id), 1) FROM settings));
SELECT setval('referral_claims_id_seq', (SELECT COALESCE(MAX(id), 1) FROM referral_claims));
