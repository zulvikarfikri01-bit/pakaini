<?php
/**
 * PAKAINI - Shop Controller
 * Toko Koin & Upgrade VIP (Monetisasi Manual Direct WhatsApp)
 */

require_once ROOT_PATH . '/app/Core/Controller.php';

class ShopController extends Controller {

    public function __construct() {
        parent::__construct();
        $this->authCheck();
    }

    /**
     * Dapatkan nomor WhatsApp admin yang dinormalisasi ke format internasional (misal: 6285143253217)
     */
    private function getCleanWaNumber(): string {
        $rawNumber = defined('ADMIN_WHATSAPP') ? ADMIN_WHATSAPP : '6285143253217';
        $clean = preg_replace('/[^0-9]/', '', (string)$rawNumber);
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        }
        return !empty($clean) ? $clean : '6285143253217';
    }

    /**
     * Format tampilan nomor WhatsApp lokal (misal: 0851-4325-3217)
     */
    private function getFormattedWaNumber(string $cleanNumber): string {
        $local = $cleanNumber;
        if (str_starts_with($local, '62')) {
            $local = '0' . substr($local, 2);
        }
        // Format 0851-4325-3217
        if (strlen($local) === 12) {
            return substr($local, 0, 4) . '-' . substr($local, 4, 4) . '-' . substr($local, 8);
        }
        return $local;
    }

    /**
     * Buat pesan WhatsApp otomatis yang mendetail memberitahu admin apa yang diorder
     */
    private function buildWhatsAppOrderMessage(array $pkg, array $user): string {
        $isVip = ($pkg['coins'] === 0 || isset($pkg['days']));
        $formattedPrice = "Rp " . number_format($pkg['price'], 0, ',', '.');
        $username = $user['username'] ?? 'User';
        $email = $user['email'] ?? '-';

        if ($isVip) {
            $days = $pkg['days'] ?? 30;
            $lines = [
                "Halo Admin PAKAINI,",
                "Saya ingin konfirmasi ORDER LANGGANAN VIP SULTAN:",
                "",
                "👑 Kategori: Langganan VVIP Sultan",
                "📦 Paket: " . $pkg['name'] . " (" . $days . " Hari)",
                "💰 Total Transfer: " . $formattedPrice,
                "👤 Akun Pemesan: " . $username,
                "📧 Email: " . $email,
                "",
                "✨ Benefit VIP: Bebas Pajak 0%, Klik Harian Unlimited, Kampanye Unlimited & Border Emas.",
                "",
                "Mohon kirimkan nomor rekening tujuan transfer (BCA/Mandiri/BRI) atau QRIS. Bukti transfer akan segera saya kirimkan ke sini setelah selesai transfer. Terima kasih!"
            ];
        } else {
            $coins = number_format($pkg['coins'], 0, ',', '.');
            $bonusInfo = !empty($pkg['bonus']) ? " (" . $pkg['bonus'] . ")" : "";
            $lines = [
                "Halo Admin PAKAINI,",
                "Saya ingin konfirmasi ORDER TOP UP KOIN:",
                "",
                "🪙 Kategori: Top Up Koin Virtual",
                "📦 Paket: " . $pkg['name'] . $bonusInfo,
                "🪙 Total Koin: " . $coins . " Koin",
                "💰 Total Transfer: " . $formattedPrice,
                "👤 Akun Pemesan: " . $username,
                "📧 Email: " . $email,
                "",
                "Mohon kirimkan nomor rekening tujuan transfer (BCA/Mandiri/BRI) atau QRIS. Bukti transfer akan segera saya kirimkan ke sini setelah selesai transfer. Terima kasih!"
            ];
        }

        return implode("\n", $lines);
    }

    /**
     * Halaman Toko Koin & VIP
     */
    public function index(): void {
        global $TOPUP_PACKAGES;
        $user = Auth::user();

        $waNumber = $this->getCleanWaNumber();
        $formattedWa = $this->getFormattedWaNumber($waNumber);
        $packages = [];

        foreach ($TOPUP_PACKAGES as $key => $pkg) {
            $msg = $this->buildWhatsAppOrderMessage($pkg, $user);
            $pkg['wa_url'] = "https://wa.me/{$waNumber}?text=" . urlencode($msg);
            $packages[$key] = $pkg;
        }

        $this->render('shop/index', [
            'page_title'          => 'Toko Koin & Upgrade VIP',
            'user'                => $user,
            'packages'            => $packages,
            'wa_number'           => $waNumber,
            'formatted_wa_number' => $formattedWa
        ]);
    }

    /**
     * Direct order redirect (Otomatis arahkan langsung ke WhatsApp dengan pesan order terisi)
     */
    public function order(): void {
        global $TOPUP_PACKAGES;
        $user = Auth::user();
        $pkgKey = $_GET['package'] ?? 'pemula';

        if (!isset($TOPUP_PACKAGES[$pkgKey])) {
            $pkgKey = 'pemula';
        }

        $pkg = $TOPUP_PACKAGES[$pkgKey];
        $waNumber = $this->getCleanWaNumber();
        $msg = $this->buildWhatsAppOrderMessage($pkg, $user);

        $waUrl = "https://wa.me/{$waNumber}?text=" . urlencode($msg);
        header("Location: " . $waUrl);
        exit;
    }
}
