<?php
/**
 * PAKAINI - Dashboard Controller
 * Tampilan Utama Member, Saldo Koin, Kasta VIP, Streak Harian, dan Ringkasan Aktivitas
 */

require_once ROOT_PATH . '/app/Core/Controller.php';
require_once ROOT_PATH . '/app/Models/User.php';
require_once ROOT_PATH . '/app/Models/Task.php';
require_once ROOT_PATH . '/app/Models/Transaction.php';

require_once ROOT_PATH . '/app/Models/Setting.php';

class DashboardController extends Controller {
    private User $userModel;
    private Task $taskModel;
    private Transaction $txModel;
    private Setting $settingModel;

    public function __construct() {
        parent::__construct();
        $this->authCheck();
        $this->userModel = new User();
        $this->taskModel = new Task();
        $this->txModel = new Transaction();
        $this->settingModel = new Setting();
    }

    public function index(): void {
        $userId = Auth::id();
        $user = Auth::user();

        $activeTasksCount = $this->taskModel->countUserActiveTasks($userId);
        $availableTasks = $this->taskModel->getAvailableTasks($userId);
        $recentTransactions = $this->txModel->getByUser($userId, 7);
        $referralData = $this->userModel->getReferralNetwork($user['username']);

        $settings = $this->settingModel->getAllSettings();
        $coinRate = (int)($settings['coin_rate_idr'] ?? COIN_RATE_IDR);
        $idrEquivalent = max(0, (int)$user['coins']) * $coinRate;

        // Cek sisa hari VIP
        $vipDaysLeft = 0;
        if (!empty($user['vip_expired_at']) && (int)$user['is_vip'] === 1) {
            $diff = strtotime($user['vip_expired_at']) - time();
            $vipDaysLeft = max(0, ceil($diff / 86400));
        }

        $this->render('dashboard/index', [
            'page_title'             => 'Dashboard Member',
            'user'                   => $user,
            'idr_equivalent'         => $idrEquivalent,
            'vip_days_left'          => $vipDaysLeft,
            'active_tasks_count'     => $activeTasksCount,
            'available_count'        => count($availableTasks),
            'available_tasks'        => array_slice($availableTasks, 0, 3),
            'recent_transactions'    => $recentTransactions,
            'referral_data'          => $referralData,
            'broadcast_announcement' => trim($settings['broadcast_announcement'] ?? '')
        ]);
    }

    /**
     * Klaim Uji Coba VIP 3 Hari & 500 Koin via Grup WhatsApp
     */
    public function claimVip(): void {
        $userId = Auth::id();
        $this->userModel->claimWaVipBonus($userId);
        header("Location: https://chat.whatsapp.com/J6M0TwrjuvoGm4Mq1eJz2G");
        exit;
    }
}
