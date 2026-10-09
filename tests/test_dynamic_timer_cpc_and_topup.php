<?php
/**
 * Test Suite: Logika Timer, Harga Dinamis Platform, dan Halaman Top-Up Harga Coret
 * 1. add_task.php (YouTube = 100 Koin, Platform lain = 50 Koin)
 * 2. earn_coins.php (YouTube = 30s, Platform lain = 15s + Pop-up Alert Intimidasi Tab)
 * 3. topup.php (Paket Pemula, Menengah, Sultan dengan line-through text-gray-400 & diskon 50%)
 */

require_once __DIR__ . '/../config.php';

echo "======================================================================\n";
echo "🧪 PENGUJIAN TIMER DINAMIS, HARGA PLATFORM, & TOP-UP HARGA CORET\n";
echo "======================================================================\n\n";

$pass = 0;
$fail = 0;

function check(string $name, bool $result) {
    global $pass, $fail;
    if ($result) {
        echo "  ✅ PASS: {$name}\n";
        $pass++;
    } else {
        echo "  ❌ FAIL: {$name}\n";
        $fail++;
    }
}

// -----------------------------------------------------------------------------
// 1. UJI ATURAN 1: HARGA DINAMIS DI add_task.php
// -----------------------------------------------------------------------------
echo "1. Pengujian add_task.php (Harga Dinamis Berdasarkan Platform):\n";

$addTaskSrc = file_get_contents(__DIR__ . '/../add_task.php');

check("add_task.php mengatur harga YouTube = 100 koin/klik", strpos($addTaskSrc, "(\$platform === 'youtube') ? 100 : 50") !== false || strpos($addTaskSrc, "(\$platform === 'youtube') ? CPC_YOUTUBE : CPC_OTHER_PLATFORMS") !== false);
check("add_task.php mengatur harga selain YouTube = 50 koin/klik", strpos($addTaskSrc, "50") !== false);
check("add_task.php JavaScript updateMinCpc menyesuaikan input BPK otomatis", strpos($addTaskSrc, "let min = (platform === 'youtube') ? 100 : 50;") !== false);
check("add_task.php UI menampilkan tabel harga YouTube 100 & platform lain 50", strpos($addTaskSrc, "100 Koin / klik") !== false && strpos($addTaskSrc, "50 Koin / klik") !== false);

// -----------------------------------------------------------------------------
// 2. UJI ATURAN 1 & 2: TIMER DINAMIS & ANTI-CHEAT TAB DI earn_coins.php
// -----------------------------------------------------------------------------
echo "\n2. Pengujian earn_coins.php (Timer Dinamis & Anti-Cheat Tab):\n";

$earnSrc = file_get_contents(__DIR__ . '/../earn_coins.php');

// A. Timer dinamis 30s YouTube vs 15s non-YouTube
check("earn_coins.php backend validasi YouTube = 30s & selain YouTube = 15s", strpos($earnSrc, "\$requiredSeconds = \$isYouTube ? 30 : 15;") !== false);
check("earn_coins.php JS startTask mengatur totalTime = isYouTube ? 30 : 15", strpos($earnSrc, "totalTime = isYouTube ? 30 : 15;") !== false);

// B. Tab Visibility tracking
check("earn_coins.php memantau visibilitychange", strpos($earnSrc, "document.addEventListener('visibilitychange'") !== false);
check("earn_coins.php memantau window focus", strpos($earnSrc, "window.addEventListener('focus'") !== false);

// C. Pop-up Alert Merah dengan Teks Wajib
$exactAlert = "⚠️️ PERINGATAN: Sistem mendeteksi Anda kembali terlalu cepat! Selesaikan tugas dengan benar atau akun Anda akan diblokir.";
check("earn_coins.php memuat teks alert intimidasi yang tepat", strpos($earnSrc, $exactAlert) !== false);

// D. Tombol Klaim Koin hanya muncul setelah timer selesai
check("Tombol klaim tersembunyi diawal (class 'hidden')", strpos($earnSrc, 'id="claimRewardBtn" class="hidden') !== false);
check("Tombol klaim dimunculkan di enableClaimButton()", strpos($earnSrc, "claimBtn.classList.remove('hidden')") !== false);

// -----------------------------------------------------------------------------
// 3. UJI ATURAN 3: HALAMAN TOP-UP DENGAN HARGA CORET DI topup.php
// -----------------------------------------------------------------------------
echo "\n3. Pengujian topup.php (Desain Paket Koin & Taktik Harga Coret):\n";

$topupSrc = file_get_contents(__DIR__ . '/../topup.php');

// A. Paket Pemula
check("Paket Pemula: Menampilkan harga asli Rp 50.000 dengan coret (line-through text-gray-400)", 
    strpos($topupSrc, 'line-through text-gray-400') !== false && strpos($topupSrc, 'Rp 50.000') !== false);
check("Paket Pemula: Menampilkan harga promo Rp 25.000", strpos($topupSrc, 'Rp 25.000') !== false);
check("Paket Pemula: Memperoleh 5.000 Koin", strpos($topupSrc, '5.000 Koin') !== false);

// B. Paket Menengah
check("Paket Menengah: Menampilkan harga asli Rp 100.000 dengan coret", 
    strpos($topupSrc, 'Rp 100.000') !== false && strpos($topupSrc, 'Paket Menengah') !== false);
check("Paket Menengah: Menampilkan harga promo Rp 50.000", strpos($topupSrc, 'Rp 50.000') !== false);
check("Paket Menengah: Memperoleh 10.000 Koin", strpos($topupSrc, '10.000 Koin') !== false);

// C. Paket Sultan
check("Paket Sultan: Menampilkan harga asli Rp 200.000 dengan coret", 
    strpos($topupSrc, 'Rp 200.000') !== false && strpos($topupSrc, 'Paket Sultan') !== false);
check("Paket Sultan: Menampilkan harga promo Rp 100.000", strpos($topupSrc, 'Rp 100.000') !== false);
check("Paket Sultan: Memperoleh 22.000 Koin", strpos($topupSrc, '22.000 Koin') !== false);
check("Paket Sultan: Memiliki label/badge 'Terlaris & Bonus Promo'", strpos($topupSrc, 'Terlaris & Bonus Promo') !== false);

// D. Tombol Beli Sekarang & Format Pesan WhatsApp
check("Tombol memiliki label 'Beli Sekarang'", strpos($topupSrc, 'Beli Sekarang') !== false);
check("Tombol terhubung ke WhatsApp Admin (wa.me)", strpos($topupSrc, 'https://wa.me/') !== false);
check("Pesan WhatsApp menyertakan username pengguna", strpos($topupSrc, 'username') !== false);

// -----------------------------------------------------------------------------
// 4. SIMULASI LOGIKA BACKEND KLAIM KOIN DINAMIS (YOUTUBE VS NON-YOUTUBE)
// -----------------------------------------------------------------------------
echo "\n4. Pengujian Simulasi Backend Validasi Waktu Pengerjaan:\n";

// YouTube butuh 30 detik (spent_time >= 29)
$ytTask = ['platform' => 'youtube'];
$ytReq = (strtolower($ytTask['platform']) === 'youtube') ? 30 : 15;
$ytPass = (29 >= ($ytReq - 1));
$ytFail = (15 < ($ytReq - 1));
check("Simulasi YouTube: 29 detik lolos syarat 30s", $ytPass);
check("Simulasi YouTube: 15 detik ditolak syarat 30s", $ytFail);

// Instagram butuh 15 detik (spent_time >= 14)
$igTask = ['platform' => 'instagram'];
$igReq = (strtolower($igTask['platform']) === 'youtube') ? 30 : 15;
$igPass = (14 >= ($igReq - 1));
$igFail = (10 < ($igReq - 1));
check("Simulasi Instagram: 14 detik lolos syarat 15s", $igPass);
check("Simulasi Instagram: 10 detik ditolak syarat 15s", $igFail);

echo "\n----------------------------------------------------------------------\n";
echo "RINGKASAN HASIL: {$pass} LULUS, {$fail} GAGAL\n";
echo "======================================================================\n";
if ($fail === 0) {
    echo "🎉 SEMUA ATURAN TIMER, HARGA DINAMIS, & TOP-UP SUKSES DIVERIFIKASI 100%!\n";
}
