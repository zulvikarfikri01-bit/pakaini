<?php
/**
 * PAKAINI - Admin Controller
 * Pengawasan Ekonomi, Manajemen Akun Penuh, Manajemen Tugas,
 * Validasi Denda Kecurangan, Buku Kas Transaksi, dan Konfigurasi Web Global
 */

require_once ROOT_PATH . '/app/Core/Controller.php';
require_once ROOT_PATH . '/app/Models/User.php';
require_once ROOT_PATH . '/app/Models/Task.php';
require_once ROOT_PATH . '/app/Models/Report.php';
require_once ROOT_PATH . '/app/Models/Transaction.php';
require_once ROOT_PATH . '/app/Models/Setting.php';

class AdminController extends Controller {
    private User $userModel;
    private Task $taskModel;
    private Report $reportModel;
    private Transaction $txModel;
    private Setting $settingModel;

    public function __construct() {
        parent::__construct();
        $this->adminCheck(); // Proteksi ketat hanya untuk admin
        $this->userModel = new User();
        $this->taskModel = new Task();
        $this->reportModel = new Report();
        $this->txModel = new Transaction();
        $this->settingModel = new Setting();
    }

    /**
     * Dashboard Ringkasan Admin & Ekonomi Koin
     */
    public function index(): void {
        $economyStats = $this->txModel->getEconomyStats();
        $totalUsers = (int)$this->userModel->fetchColumn("SELECT COUNT(*) FROM users");
        $totalVip = (int)$this->userModel->fetchColumn("SELECT COUNT(*) FROM users WHERE is_vip = 1");
        $pendingReports = (int)$this->userModel->fetchColumn("SELECT COUNT(*) FROM reports WHERE status = 'pending'");
        $activeTasks = (int)$this->userModel->fetchColumn("SELECT COUNT(*) FROM tasks WHERE status = 'active'");
        $totalTasks = (int)$this->userModel->fetchColumn("SELECT COUNT(*) FROM tasks");
        $recentTransactions = $this->txModel->getAll(12);

        $this->render('admin/index', [
            'page_title'          => 'Admin Panel - Ringkasan Sistem',
            'stats'               => $economyStats,
            'total_users'         => $totalUsers,
            'total_vip'           => $totalVip,
            'pending_reports'     => $pendingReports,
            'active_tasks'        => $activeTasks,
            'total_tasks'         => $totalTasks,
            'recent_transactions' => $recentTransactions
        ], 'admin');
    }

    /**
     * =========================================================================
     * 1. MANAJEMEN PENGGUNA (USERS)
     * =========================================================================
     */

    /**
     * Halaman Kelola Pengguna
     */
    public function users(): void {
        $search = trim($_GET['q'] ?? '');
        $users = $this->userModel->search($search, 60);

        $this->render('admin/users', [
            'page_title' => 'Admin - Kelola Pengguna & Akun',
            'users'      => $users,
            'search'     => $search
        ], 'admin');
    }

    /**
     * Tambah Pengguna Baru Langsung oleh Admin
     */
    public function create_user(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=admin&action=users');
        }

        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Token keamanan CSRF tidak valid.');
            $this->redirect('index.php?page=admin&action=users');
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role = in_array($_POST['role'] ?? '', ['user', 'admin']) ? $_POST['role'] : 'user';
        $coins = (int)($_POST['coins'] ?? 500);
        $isVip = !empty($_POST['is_vip']) ? 1 : 0;
        $vipDays = (int)($_POST['vip_days'] ?? 30);

        if (empty($username) || empty($email) || empty($password)) {
            $this->setFlash('error', 'Username, email, dan password wajib diisi.');
            $this->redirect('index.php?page=admin&action=users');
        }

        if (strlen($password) < 6) {
            $this->setFlash('error', 'Password minimal 6 karakter.');
            $this->redirect('index.php?page=admin&action=users');
        }

        if ($this->userModel->findByUsername($username)) {
            $this->setFlash('error', "Username '{$username}' sudah digunakan pengguna lain.");
            $this->redirect('index.php?page=admin&action=users');
        }

        if ($this->userModel->findByEmail($email)) {
            $this->setFlash('error', "Email '{$email}' sudah terdaftar.");
            $this->redirect('index.php?page=admin&action=users');
        }

        try {
            $newId = $this->userModel->createByAdmin($username, $email, $password, $role, $coins, $isVip, $vipDays);
            $this->setFlash('success', "Akun baru '{$username}' berhasil dibuat dengan ID #{$newId}.");
        } catch (Exception $e) {
            $this->setFlash('error', 'Gagal membuat pengguna: ' . $e->getMessage());
        }

        $this->redirect('index.php?page=admin&action=users');
    }

    /**
     * Top Up Koin, Perpanjang VIP, atau Potong Koin
     */
    public function manual_action(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=admin&action=users');
        }

        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Token keamanan CSRF tidak valid.');
            $this->redirect('index.php?page=admin&action=users');
        }

        $targetUserId = (int)($_POST['user_id'] ?? 0);
        $actionType = trim($_POST['action_type'] ?? '');
        $amount = (int)($_POST['amount'] ?? 0);
        $notes = trim($_POST['notes'] ?? 'Divalidasi langsung oleh Admin');

        $targetUser = $this->userModel->findById($targetUserId);
        if (!$targetUser) {
            $this->setFlash('error', 'User tujuan tidak ditemukan.');
            $this->redirect('index.php?page=admin&action=users');
        }

        if ($actionType === 'add_coins') {
            if ($amount <= 0) {
                $this->setFlash('error', 'Jumlah koin harus lebih dari 0.');
            } else {
                $desc = "Top Up Koin Manual Admin: +{$amount} Koin ({$notes})";
                $this->userModel->updateCoins($targetUserId, $amount, $desc, 'topup', Auth::id());
                $this->setFlash('success', "Berhasil menambahkan +{$amount} Koin ke akun {$targetUser['username']}.");
            }
        } elseif ($actionType === 'extend_vip') {
            if ($amount <= 0) {
                $this->setFlash('error', 'Jumlah hari VIP harus lebih dari 0.');
            } else {
                $this->userModel->extendVip($targetUserId, $amount);
                $this->setFlash('success', "Berhasil mengaktifkan/memperpanjang status VIP {$amount} hari untuk {$targetUser['username']}.");
            }
        } elseif ($actionType === 'deduct_coins') {
            if ($amount <= 0) {
                $this->setFlash('error', 'Jumlah koin yang dipotong harus lebih dari 0.');
            } else {
                $desc = "Penyesuaian Koin Admin: -{$amount} Koin ({$notes})";
                $this->userModel->updateCoins($targetUserId, -$amount, $desc, 'denda', Auth::id());
                $this->setFlash('success', "Berhasil memotong -{$amount} Koin dari akun {$targetUser['username']}.");
            }
        }

        $this->redirect('index.php?page=admin&action=users');
    }

    /**
     * Setel Saldo Koin Tepat (Exact Coins Balance)
     */
    public function set_coins(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=admin&action=users');
        }

        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Token keamanan CSRF tidak valid.');
            $this->redirect('index.php?page=admin&action=users');
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $newCoins = (int)($_POST['coins'] ?? 0);
        $reason = trim($_POST['reason'] ?? 'Penyesuaian saldo langsung oleh Admin');

        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->setFlash('error', 'Pengguna tidak ditemukan.');
            $this->redirect('index.php?page=admin&action=users');
        }

        $this->userModel->setExactCoins($userId, $newCoins, $reason, Auth::id());
        $this->setFlash('success', "Saldo akun {$user['username']} berhasil diatur menjadi {$newCoins} koin.");
        $this->redirect('index.php?page=admin&action=users');
    }

    /**
     * Toggle / Cabut Status VIP Pengguna
     */
    public function toggle_vip(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=admin&action=users');
        }

        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Token keamanan CSRF tidak valid.');
            $this->redirect('index.php?page=admin&action=users');
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $action = trim($_POST['vip_action'] ?? '');
        $days = (int)($_POST['days'] ?? 30);

        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->setFlash('error', 'Pengguna tidak ditemukan.');
            $this->redirect('index.php?page=admin&action=users');
        }

        if ($action === 'deactivate') {
            $this->userModel->deactivateVip($userId);
            $this->setFlash('success', "Status VIP akun {$user['username']} telah dinonaktifkan.");
        } elseif ($action === 'activate') {
            $this->userModel->extendVip($userId, $days);
            $this->setFlash('success', "Status VIP akun {$user['username']} aktif selama {$days} hari.");
        }

        $this->redirect('index.php?page=admin&action=users');
    }

    /**
     * Ubah Role Pengguna (Admin <-> User)
     */
    public function change_role(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=admin&action=users');
        }

        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Token keamanan CSRF tidak valid.');
            $this->redirect('index.php?page=admin&action=users');
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $role = trim($_POST['role'] ?? 'user');

        if ($userId === Auth::id()) {
            $this->setFlash('error', 'Anda tidak dapat mengubah role akun admin Anda sendiri.');
            $this->redirect('index.php?page=admin&action=users');
        }

        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->setFlash('error', 'Pengguna tidak ditemukan.');
            $this->redirect('index.php?page=admin&action=users');
        }

        $this->userModel->updateRole($userId, $role);
        $label = ($role === 'admin') ? 'ADMINISTRATOR' : 'MEMBER BIASA';
        $this->setFlash('success', "Role akun {$user['username']} berhasil diubah menjadi {$label}.");
        $this->redirect('index.php?page=admin&action=users');
    }

    /**
     * Reset Password Pengguna Secara Langsung
     */
    public function reset_password(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=admin&action=users');
        }

        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Token keamanan CSRF tidak valid.');
            $this->redirect('index.php?page=admin&action=users');
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $newPass = trim($_POST['new_password'] ?? '');

        if (strlen($newPass) < 6) {
            $this->setFlash('error', 'Password baru minimal 6 karakter.');
            $this->redirect('index.php?page=admin&action=users');
        }

        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->setFlash('error', 'Pengguna tidak ditemukan.');
            $this->redirect('index.php?page=admin&action=users');
        }

        $this->userModel->resetPassword($userId, $newPass);
        $this->setFlash('success', "Password untuk akun {$user['username']} berhasil diperbarui.");
        $this->redirect('index.php?page=admin&action=users');
    }

    /**
     * Reset Password Pengguna ke Password Default 123456
     */
    public function reset_password_default(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=admin&action=users');
        }

        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Token keamanan CSRF tidak valid.');
            $this->redirect('index.php?page=admin&action=users');
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->setFlash('error', 'Pengguna tidak ditemukan.');
            $this->redirect('index.php?page=admin&action=users');
        }

        $defaultPassword = '123456';
        $this->userModel->resetPassword($userId, $defaultPassword);
        $this->setFlash('success', "Kata sandi berhasil direset menjadi: 123456. Arahkan pengguna untuk segera menggantinya di Profil.");
        $this->redirect('index.php?page=admin&action=users');
    }

    /**
     * Reset Kuota Klik Harian Pengguna
     */
    public function reset_clicks(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=admin&action=users');
        }

        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Token keamanan CSRF tidak valid.');
            $this->redirect('index.php?page=admin&action=users');
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $user = $this->userModel->findById($userId);
        if ($user) {
            $this->userModel->resetDailyClicks($userId);
            $this->setFlash('success', "Kuota klik harian untuk {$user['username']} telah di-reset ke 0.");
        }

        $this->redirect('index.php?page=admin&action=users');
    }

    /**
     * Hapus Pengguna Secara Permanen
     */
    public function delete_user(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=admin&action=users');
        }

        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Token keamanan CSRF tidak valid.');
            $this->redirect('index.php?page=admin&action=users');
        }

        $userId = (int)($_POST['user_id'] ?? 0);

        if ($userId === Auth::id()) {
            $this->setFlash('error', 'Anda tidak dapat menghapus akun admin Anda sendiri.');
            $this->redirect('index.php?page=admin&action=users');
        }

        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->setFlash('error', 'Pengguna tidak ditemukan.');
            $this->redirect('index.php?page=admin&action=users');
        }

        $username = $user['username'];
        $this->userModel->deleteUser($userId);
        $this->setFlash('success', "Akun pengguna '{$username}' (ID #{$userId}) beserta seluruh datanya berhasil dihapus secara permanen.");
        $this->redirect('index.php?page=admin&action=users');
    }

    /**
     * =========================================================================
     * 2. MANAJEMEN TUGAS & KAMPANYE (TASKS)
     * =========================================================================
     */

    /**
     * Halaman Kelola Semua Kampanye & Tugas
     */
    public function tasks(): void {
        $platform = trim($_GET['platform'] ?? 'all');
        $status = trim($_GET['status'] ?? 'all');
        $search = trim($_GET['q'] ?? '');

        $tasks = $this->taskModel->getAllTasksForAdmin($platform, $status, $search, 100);

        // Ringkasan status tugas
        $totalAll = (int)$this->taskModel->fetchColumn("SELECT COUNT(*) FROM tasks");
        $totalActive = (int)$this->taskModel->fetchColumn("SELECT COUNT(*) FROM tasks WHERE status = 'active'");
        $totalPaused = (int)$this->taskModel->fetchColumn("SELECT COUNT(*) FROM tasks WHERE status = 'paused'");
        $totalCompleted = (int)$this->taskModel->fetchColumn("SELECT COUNT(*) FROM tasks WHERE status = 'completed'");

        $this->render('admin/tasks', [
            'page_title'      => 'Admin - Kelola Semua Kampanye & Tugas',
            'tasks'           => $tasks,
            'selected_plat'   => $platform,
            'selected_status' => $status,
            'search'          => $search,
            'total_all'       => $totalAll,
            'total_active'    => $totalActive,
            'total_paused'    => $totalPaused,
            'total_completed' => $totalCompleted
        ], 'admin');
    }

    /**
     * Update Kampanye (Status, CPC, Max Clicks) oleh Admin
     */
    public function update_task(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=admin&action=tasks');
        }

        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Token keamanan CSRF tidak valid.');
            $this->redirect('index.php?page=admin&action=tasks');
        }

        $taskId = (int)($_POST['task_id'] ?? 0);
        $status = trim($_POST['status'] ?? 'active');
        $cpc = max(2, (int)($_POST['cpc'] ?? 10));
        $maxClicks = max(1, (int)($_POST['max_clicks'] ?? 100));

        $task = $this->taskModel->findById($taskId);
        if (!$task) {
            $this->setFlash('error', 'Tugas tidak ditemukan.');
            $this->redirect('index.php?page=admin&action=tasks');
        }

        $this->taskModel->adminUpdateTask($taskId, $status, $cpc, $maxClicks);
        $this->setFlash('success', "Kampanye #{$taskId} ({$task['platform']}) berhasil diperbarui (Status: {$status}, CPC: {$cpc}, Target: {$maxClicks}).");
        $this->redirect('index.php?page=admin&action=tasks');
    }

    /**
     * Hapus Kampanye oleh Admin
     */
    public function delete_task(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=admin&action=tasks');
        }

        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Token keamanan CSRF tidak valid.');
            $this->redirect('index.php?page=admin&action=tasks');
        }

        $taskId = (int)($_POST['task_id'] ?? 0);
        $task = $this->taskModel->findById($taskId);
        if (!$task) {
            $this->setFlash('error', 'Tugas tidak ditemukan.');
            $this->redirect('index.php?page=admin&action=tasks');
        }

        $this->taskModel->adminDeleteTask($taskId);
        $this->setFlash('success', "Kampanye #{$taskId} ({$task['platform']}) berhasil dihapus secara permanen.");
        $this->redirect('index.php?page=admin&action=tasks');
    }

    /**
     * =========================================================================
     * 3. MANAJEMEN LAPORAN KECURANGAN (REPORTS / ANTI-UNFOLLOW)
     * =========================================================================
     */

    /**
     * Kelola Laporan Kecurangan
     */
    public function reports(): void {
        $status = trim($_GET['status'] ?? 'all');
        $reports = $this->reportModel->getAllReports($status);

        $this->render('admin/reports', [
            'page_title'      => 'Admin - Validasi Laporan Kecurangan',
            'reports'         => $reports,
            'selected_status' => $status
        ], 'admin');
    }

    /**
     * Eksekusi Denda Pelanggar 3x Lipat & Refund ke Pelapor
     */
    public function penalize(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=admin&action=reports');
        }

        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Token keamanan CSRF tidak valid.');
            $this->redirect('index.php?page=admin&action=reports');
        }

        $reportId = (int)($_POST['report_id'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');

        $result = $this->reportModel->penalizeCheater($reportId, $notes);

        if ($result['success']) {
            $this->setFlash('success', $result['message']);
        } else {
            $this->setFlash('error', $result['message']);
        }

        $this->redirect('index.php?page=admin&action=reports');
    }

    /**
     * Tolak Laporan
     */
    public function reject_report(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=admin&action=reports');
        }

        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Token keamanan CSRF tidak valid.');
            $this->redirect('index.php?page=admin&action=reports');
        }

        $reportId = (int)($_POST['report_id'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');

        $result = $this->reportModel->rejectReport($reportId, $notes);

        if ($result['success']) {
            $this->setFlash('success', $result['message']);
        } else {
            $this->setFlash('error', $result['message']);
        }

        $this->redirect('index.php?page=admin&action=reports');
    }

    /**
     * =========================================================================
     * 4. BUKU KAS & AUDIT TRANSAKSI KOIN (TRANSACTIONS)
     * =========================================================================
     */

    /**
     * Halaman Buku Kas & Log Seluruh Mutasi Koin
     */
    public function transactions(): void {
        $type = trim($_GET['type'] ?? 'all');
        $search = trim($_GET['q'] ?? '');
        $limit = max(20, min(200, (int)($_GET['limit'] ?? 100)));

        $transactions = $this->txModel->searchAndFilter($type, $search, $limit);
        $types = $this->txModel->getDistinctTypes();
        $economyStats = $this->txModel->getEconomyStats();

        $this->render('admin/transactions', [
            'page_title'    => 'Admin - Buku Kas & Mutasi Koin',
            'transactions'  => $transactions,
            'types'         => $types,
            'selected_type' => $type,
            'search'        => $search,
            'limit'         => $limit,
            'stats'         => $economyStats
        ], 'admin');
    }

    /**
     * =========================================================================
     * 5. PENGATURAN GLOBAL WEBSITE & SISTEM (SETTINGS)
     * =========================================================================
     */

    /**
     * Halaman Konfigurasi Web Global
     */
    public function settings(): void {
        $settings = $this->settingModel->getAllSettings();

        $this->render('admin/settings', [
            'page_title' => 'Admin - Konfigurasi & Pengaturan Sistem',
            'settings'   => $settings
        ], 'admin');
    }

    /**
     * Simpan Konfigurasi Web
     */
    public function save_settings(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=admin&action=settings');
        }

        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Token keamanan CSRF tidak valid.');
            $this->redirect('index.php?page=admin&action=settings');
        }

        $data = [
            'site_name'                => trim($_POST['site_name'] ?? 'PAKAINI'),
            'site_tagline'             => trim($_POST['site_tagline'] ?? ''),
            'wa_admin'                 => preg_replace('/[^0-9]/', '', $_POST['wa_admin'] ?? '6285143253217'),
            'coin_rate_idr'            => max(1, (int)($_POST['coin_rate_idr'] ?? 25)),
            'regular_tax_rate'         => max(0.0, min(0.9, (float)($_POST['regular_tax_rate'] ?? 0.20))),
            'regular_daily_max_clicks' => max(5, (int)($_POST['regular_daily_max_clicks'] ?? 50)),
            'regular_max_active_tasks' => max(1, (int)($_POST['regular_max_active_tasks'] ?? 2)),
            'new_user_bonus_coins'     => max(0, (int)($_POST['new_user_bonus_coins'] ?? 500)),
            'new_user_vip_days'        => max(0, (int)($_POST['new_user_vip_days'] ?? 3)),
            'penalty_multiplier'       => max(1, (int)($_POST['penalty_multiplier'] ?? 3)),
            'broadcast_announcement'   => trim($_POST['broadcast_announcement'] ?? '')
        ];

        $this->settingModel->setMany($data);
        $this->setFlash('success', 'Konfigurasi website dan parameter ekonomi berhasil disimpan!');
        $this->redirect('index.php?page=admin&action=settings');
    }
}
