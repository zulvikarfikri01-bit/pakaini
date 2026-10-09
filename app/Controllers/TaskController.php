<?php
/**
 * PAKAINI - Task Controller
 * Manajemen Kampanye, Pencarian Koin, Timer Validasi 15s/30s, dan Klaim Pajak Anti-Inflasi
 */

require_once ROOT_PATH . '/app/Core/Controller.php';
require_once ROOT_PATH . '/app/Models/Task.php';
require_once ROOT_PATH . '/app/Models/User.php';

class TaskController extends Controller {
    private Task $taskModel;
    private User $userModel;

    public function __construct() {
        parent::__construct();
        $this->authCheck();
        $this->taskModel = new Task();
        $this->userModel = new User();
    }

    /**
     * Halaman Pencarian Koin (Earning / Kerjakan Tugas)
     * Diurutkan berdasarkan:
     * 1. is_vip DESC (Tugas milik VIP selalu di atas)
     * 2. cpc DESC (Harga koin termahal di atas)
     */
    public function index(): void {
        $userId = Auth::id();
        $platform = trim($_GET['platform'] ?? 'all');
        $type = trim($_GET['type'] ?? 'all');

        $tasks = $this->taskModel->getAvailableTasks($userId, $platform, $type);
        $user = Auth::user();

        // Cek sisa batas klik harian user biasa
        $isVip = (int)$user['is_vip'] === 1;
        $dailyClicks = (int)$user['daily_clicks'];
        $remainingClicks = $isVip ? 'Unlimited' : max(0, REGULAR_DAILY_MAX_CLICKS - $dailyClicks);

        $this->render('tasks/index', [
            'page_title'       => 'Cari Koin (Kerjakan Tugas)',
            'tasks'            => $tasks,
            'selected_platform'=> $platform,
            'selected_type'    => $type,
            'is_vip'           => $isVip,
            'daily_clicks'     => $dailyClicks,
            'remaining_clicks' => $remainingClicks
        ]);
    }

    /**
     * Mulai Timer Validasi Tugas (AJAX)
     * Mencatat waktu mulai pengerjaan tugas di session untuk mencegah bypass timer client
     */
    public function start_timer(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $taskId = (int)($_POST['task_id'] ?? 0);
        $task = $this->taskModel->findById($taskId);

        if (!$task) {
            $this->json(['success' => false, 'message' => 'Tugas tidak ditemukan'], 404);
        }

        // Tentukan durasi: YouTube Views minimal 30 detik, tugas lain 15 detik
        $duration = ($task['platform'] === 'youtube' && $task['task_type'] === 'views') 
            ? TASK_YT_VIEW_TIMER_SECONDS 
            : TASK_DEFAULT_TIMER_SECONDS;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['task_timer'][$taskId] = [
            'start_time' => time(),
            'duration'   => $duration
        ];

        $this->json([
            'success'  => true,
            'task_id'  => $taskId,
            'duration' => $duration,
            'url'      => $task['task_url']
        ]);
    }

    /**
     * Klaim Koin Tugas (AJAX)
     * Validasi timer 15s/30s di sisi server, potong saldo pemilik, terapkan pajak 20% / 0% VIP
     */
    public function claim(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Metode request tidak diizinkan.'], 405);
        }

        $taskId = (int)($_POST['task_id'] ?? 0);
        $userId = Auth::id();

        if (empty($taskId)) {
            $this->json(['success' => false, 'message' => 'ID Tugas tidak valid.'], 400);
        }

        // Validasi Timer Server-Side
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $timerData = $_SESSION['task_timer'][$taskId] ?? null;
        if (!$timerData) {
            $this->json(['success' => false, 'message' => 'Sesi pengerjaan tugas tidak ditemukan. Silakan klik tombol "Buka Link Tugas" terlebih dahulu.'], 400);
        }

        $startTime = $timerData['start_time'];
        $requiredDuration = $timerData['duration'];
        $elapsed = time() - $startTime;

        // Toleransi 1 detik untuk latency jaringan
        if ($elapsed < ($requiredDuration - 1)) {
            $sisa = $requiredDuration - $elapsed;
            $this->json([
                'success' => false,
                'message' => "Harap tunggu hingga timer selesai ({$sisa} detik lagi) untuk memastikan Anda benar-benar membuka tautan!"
            ], 400);
        }

        // Eksekusi klaim tugas dan pajak anti-inflasi
        $result = $this->taskModel->claimTask($taskId, $userId);

        if ($result['success']) {
            // Hapus session timer setelah berhasil diklaim
            unset($_SESSION['task_timer'][$taskId]);
            // Refresh data user di session
            Auth::syncUser($userId);
        }

        $this->json($result);
    }

    /**
     * Halaman Pasang Kampanye Baru (Add Task)
     */
    public function create(): void {
        global $PLATFORM_MIN_CPC;
        $user = Auth::user();
        $isVip = (int)$user['is_vip'] === 1;
        $activeCount = $this->taskModel->countUserActiveTasks(Auth::id());

        $this->render('tasks/create', [
            'page_title'       => 'Pasang Kampanye Baru',
            'user'             => $user,
            'is_vip'           => $isVip,
            'active_count'     => $activeCount,
            'platform_min_cpc' => $PLATFORM_MIN_CPC
        ]);
    }

    /**
     * Simpan Kampanye Baru
     */
    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=tasks&action=create');
        }

        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('index.php?page=tasks&action=create');
        }

        $userId = Auth::id();
        $user = Auth::user();
        $isVip = (int)$user['is_vip'] === 1;

        $platform = trim($_POST['platform'] ?? '');
        $taskType = trim($_POST['task_type'] ?? '');
        $url = trim($_POST['task_url'] ?? '');
        $cpc = (int)($_POST['cpc'] ?? 0);
        $maxClicks = (int)($_POST['max_clicks'] ?? 100);

        // Auto-Format: Jika pengguna lupa memasukkan http:// atau https:// di awal link
        if (!empty($url) && !preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }

        if (empty($platform) || empty($taskType) || empty($url) || $cpc <= 0 || $maxClicks <= 0) {
            $this->setFlash('error', 'Semua kolom wajib diisi dengan nilai yang benar.');
            $this->redirect('index.php?page=tasks&action=create');
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $this->setFlash('error', 'URL tautan tugas tidak valid. Harap gunakan format link yang benar.');
            $this->redirect('index.php?page=tasks&action=create');
        }

        $urlLower = strtolower($url);
        if ($platform === 'youtube' && strpos($urlLower, 'youtube.com') === false && strpos($urlLower, 'youtu.be') === false) {
            $this->setFlash('error', 'URL YouTube tidak cocok! Tautan wajib mengandung domain "youtube.com" atau "youtu.be".');
            $this->redirect('index.php?page=tasks&action=create');
        } elseif ($platform === 'instagram' && strpos($urlLower, 'instagram.com') === false) {
            $this->setFlash('error', 'URL Instagram tidak cocok! Tautan wajib mengandung domain "instagram.com".');
            $this->redirect('index.php?page=tasks&action=create');
        } elseif ($platform === 'tiktok' && strpos($urlLower, 'tiktok.com') === false && strpos($urlLower, 'vt.tiktok.com') === false) {
            $this->setFlash('error', 'URL TikTok tidak cocok! Tautan wajib mengandung domain "tiktok.com" atau "vt.tiktok.com".');
            $this->redirect('index.php?page=tasks&action=create');
        }

        $result = $this->taskModel->createTask($userId, $platform, $taskType, $url, $cpc, $maxClicks, $isVip);

        if ($result['success']) {
            $this->setFlash('success', $result['message']);
            $this->redirect('index.php?page=tasks&action=my_tasks');
        } else {
            $this->setFlash('error', $result['message']);
            $this->redirect('index.php?page=tasks&action=create');
        }
    }

    /**
     * Halaman Kampanye Saya & Riwayat Pengerjaan (Fitur Lapor Curang)
     */
    public function my_tasks(): void {
        $userId = Auth::id();
        $tasks = $this->taskModel->getUserTasks($userId);

        // Jika user mengklik detail riwayat salah satu tugas
        $selectedTaskId = (int)($_GET['task_id'] ?? 0);
        $completions = [];
        $selectedTask = null;

        if ($selectedTaskId > 0) {
            $selectedTask = $this->taskModel->findById($selectedTaskId);
            if ($selectedTask && $selectedTask['user_id'] == $userId) {
                $completions = $this->taskModel->getTaskCompletions($selectedTaskId, $userId);
            }
        } elseif (!empty($tasks)) {
            // Default pilih task pertama jika ada
            $selectedTask = $tasks[0];
            $completions = $this->taskModel->getTaskCompletions($selectedTask['id'], $userId);
        }

        $this->render('tasks/my_tasks', [
            'page_title'    => 'Manajemen Kampanye Saya',
            'tasks'         => $tasks,
            'selected_task' => $selectedTask,
            'completions'   => $completions
        ]);
    }

    /**
     * Toggle status kampanye (Aktif / Jeda)
     */
    public function toggle_status(): void {
        $taskId = (int)($_GET['id'] ?? 0);
        $status = trim($_GET['status'] ?? 'paused');
        $userId = Auth::id();

        if ($this->taskModel->updateStatus($taskId, $userId, $status)) {
            $this->setFlash('success', "Status kampanye berhasil diubah menjadi: {$status}.");
        } else {
            $this->setFlash('error', 'Gagal mengubah status kampanye.');
        }

        $this->redirect('index.php?page=tasks&action=my_tasks');
    }
}
