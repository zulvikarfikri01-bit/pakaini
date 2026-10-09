<?php
/**
 * PAKAINI - Referral Controller
 * Status: Coming Soon (MVP)
 */

require_once ROOT_PATH . '/app/Core/Controller.php';
require_once ROOT_PATH . '/app/Models/User.php';

class ReferralController extends Controller {
    private User $userModel;

    public function __construct() {
        parent::__construct();
        $this->authCheck();
        $this->userModel = new User();
    }

    public function index(): void {
        $user = Auth::user();
        $freshUser = $this->userModel->findById((int)$user['id']);
        $username = $freshUser['username'] ?? '';

        // Buat link afiliasi unik (?ref=username)
        $referralLink = BASE_URL . '/index.php?page=register&ref=' . urlencode($username);

        $this->render('referral/index', [
            'page_title'     => 'Sistem Referral (Bawa Teman) - COMING SOON!',
            'user'           => $freshUser,
            'referral_link'  => $referralLink
        ]);
    }

    public function claim(): void {
        $this->flash('info', 'Fitur klaim referral sedang dalam tahap pengembangan (Coming Soon). Nantikan pembaruannya!');
        $this->redirect('referral');
    }
}
