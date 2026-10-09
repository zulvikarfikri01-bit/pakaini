<?php
/**
 * PAKAINI - Report Controller
 * Menangani pengiriman laporan kecurangan (Anti-Unfollow) oleh pemilik kampanye
 */

require_once ROOT_PATH . '/app/Core/Controller.php';
require_once ROOT_PATH . '/app/Models/Report.php';

class ReportController extends Controller {
    private Report $reportModel;

    public function __construct() {
        parent::__construct();
        $this->authCheck();
        $this->reportModel = new Report();
    }

    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=tasks&action=my_tasks');
        }

        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Token keamanan CSRF tidak valid.');
            $this->redirect('index.php?page=tasks&action=my_tasks');
        }

        $reporterId = Auth::id();
        $reportedUserId = (int)($_POST['reported_user_id'] ?? 0);
        $taskId = (int)($_POST['task_id'] ?? 0);
        $reason = trim($_POST['reason'] ?? 'Unfollow / Unsubscribe / Unlike setelah klaim koin');

        if (empty($reportedUserId) || empty($taskId)) {
            $this->setFlash('error', 'Data laporan tidak lengkap.');
            $this->redirect('index.php?page=tasks&action=my_tasks');
        }

        $result = $this->reportModel->createReport($reporterId, $reportedUserId, $taskId, $reason);

        if ($result['success']) {
            $this->setFlash('success', $result['message']);
        } else {
            $this->setFlash('error', $result['message']);
        }

        $this->redirect("index.php?page=tasks&action=my_tasks&task_id={$taskId}");
    }
}
