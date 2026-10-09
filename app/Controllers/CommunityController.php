<?php
/**
 * PAKAINI - Community Controller
 * Ruang Komunitas & Chat Multi-Tab (Global, YouTube, TikTok, Instagram, Facebook)
 * Wadah responsif siap dipasangi embed Minnit Chat / Chatango
 */

require_once ROOT_PATH . '/app/Core/Controller.php';

class CommunityController extends Controller {

    public function __construct() {
        parent::__construct();
        // Izinkan guest melihat room chat & link komunitas WA
    }

    public function index(): void {
        $user = Auth::user();
        $this->render('community/index', [
            'page_title' => 'Komunitas & Ruang Diskusi',
            'user'       => $user
        ]);
    }
}
