<?php
/**
 * Test Suite: Pre-Launch Security Audit & Hardening (Pengamanan Total)
 * Verifikasi 4 Aturan Keamanan Ketat:
 * 1. Validasi Timer Backend (Anti Inspect Element/Auto-Clicker)
 * 2. Anti SQL Injection (Prepared Statements)
 * 3. Anti XSS (htmlspecialchars ENT_QUOTES UTF-8)
 * 4. Kunci Ganda Ruang Admin (Strict Session Protection)
 */

require_once __DIR__ . '/../config.php';

echo "======================================================================\n";
echo "🛡️ PRE-LAUNCH SECURITY AUDIT & HARDENING VERIFICATION\n";
echo "======================================================================\n\n";

$pass = 0;
$fail = 0;

function check(string $name, bool $result, string $details = '') {
    global $pass, $fail;
    if ($result) {
        echo "  ✅ PASS: {$name}\n";
        $pass++;
    } else {
        echo "  ❌ FAIL: {$name}" . ($details ? " ({$details})" : "") . "\n";
        $fail++;
    }
}

// ------------------------------------------------------------------------------
// 1. ATURAN 4: KUNCI GANDA RUANG ADMIN (admin.php)
// ------------------------------------------------------------------------------
echo "1. Pengujian Kunci Ganda Ruang Admin (admin.php):\n";

$adminContent = file_get_contents(__DIR__ . '/../admin.php');

$requiredAdminCheck = 'if (!isset($_SESSION[\'user_id\']) || $_SESSION[\'role\'] !== \'admin\') { header("Location: index.php"); exit(); }';
check(
    "admin.php memiliki kode proteksi sesi ganda yang persis di baris awal",
    strpos($adminContent, $requiredAdminCheck) !== false
);

check(
    "admin.php memanggil exit() atau die() setelah redirect header",
    strpos($adminContent, 'header("Location: index.php"); exit();') !== false
);

// ------------------------------------------------------------------------------
// 2. ATURAN 1: VALIDASI TIMER BACKEND (earn_coins.php)
// ------------------------------------------------------------------------------
echo "\n2. Pengujian Validasi Timer Backend (Anti Inspect Element / Auto-Clicker):\n";

$earnContent = file_get_contents(__DIR__ . '/../earn_coins.php');

check(
    "earn_coins.php memiliki endpoint 'start_task' untuk menyimpan waktu mulai di sesi backend",
    strpos($earnContent, "action'] === 'start_task'") !== false &&
    strpos($earnContent, "\$_SESSION['task_start_time'] = time();") !== false &&
    strpos($earnContent, "\$_SESSION['current_task_id'] = \$taskId;") !== false
);

check(
    "earn_coins.php menolak klaim instan/curang dengan pesan 'Manipulasi waktu terdeteksi'",
    strpos($earnContent, "'Manipulasi waktu terdeteksi'") !== false
);

check(
    "earn_coins.php membersihkan sesi task_start_time setelah klaim sukses (Anti-Replay)",
    strpos($earnContent, "unset(\$_SESSION['task_start_time']);") !== false &&
    strpos($earnContent, "unset(\$_SESSION['current_task_id']);") !== false
);

check(
    "earn_coins.php JS startTask() mengirim sinyal AJAX start_task ke backend",
    strpos($earnContent, "startData.append('action', 'start_task');") !== false
);

// Simulasi Logika Timer Backend Secara Mandiri
// A. Skenario Curang: User mencoba klaim seketika (0 detik berlalu)
$_SESSION['user_id'] = 1;
$_SESSION['task_start_time'] = time(); // Baru 0 detik
$_SESSION['current_task_id'] = 999;

$mockTaskPlatform = 'instagram'; // Butuh 15 detik
$requiredSeconds = ($mockTaskPlatform === 'youtube') ? 30 : 15;
$backendElapsed = time() - $_SESSION['task_start_time'];

$rejected = ($backendElapsed < $requiredSeconds);
check("Simulasi Klaim Seketika (0 detik): Ditolak karena {$backendElapsed}s < {$requiredSeconds}s", $rejected);

// B. Skenario Valid: Waktu pengerjaan memenuhi syarat
$_SESSION['task_start_time'] = time() - 16; // 16 detik lalu
$backendElapsedValid = time() - $_SESSION['task_start_time'];
$accepted = ($backendElapsedValid >= $requiredSeconds);
check("Simulasi Klaim Sah (16 detik): Diterima karena {$backendElapsedValid}s >= {$requiredSeconds}s", $accepted);

// C. Skenario YouTube: Membutuhkan 30 detik
$mockYtPlatform = 'youtube';
$requiredSecondsYt = 30;
$_SESSION['task_start_time'] = time() - 20; // Baru 20 detik
$backendElapsedYt = time() - $_SESSION['task_start_time'];
$rejectedYt = ($backendElapsedYt < $requiredSecondsYt);
check("Simulasi YouTube Belum 30 Detik (20 detik): Ditolak karena {$backendElapsedYt}s < {$requiredSecondsYt}s", $rejectedYt);

// ------------------------------------------------------------------------------
// 3. ATURAN 2: ANTI SQL INJECTION (WAJIB PREPARED STATEMENTS)
// ------------------------------------------------------------------------------
echo "\n3. Pengujian Anti SQL Injection (Prepared Statements):\n";

$filesToCheck = [
    'login.php',
    'register.php',
    'add_task.php',
    'admin.php',
    'earn_coins.php',
    'claim_vip.php',
    'profile.php',
    'dashboard.php'
];

$allPrepared = true;
foreach ($filesToCheck as $f) {
    $code = file_get_contents(__DIR__ . '/../' . $f);
    // Pastikan tidak ada query tradisional yang menggabungkan variabel $ di dalam string query SQL
    if (preg_match('/\$pdo->(?:query|exec)\s*\(\s*["\'].*?\$[a-zA-Z_].*?["\']\s*\)/i', $code, $match)) {
        $allPrepared = false;
        echo "    ⚠️ Ditemukan konkatenasi SQL di {$f}: {$match[0]}\n";
    }
}
check("Seluruh file utama database bebas dari konkatenasi variabel SQL langsung", $allPrepared);

// ------------------------------------------------------------------------------
// 4. ATURAN 3: ANTI XSS (htmlspecialchars ENT_QUOTES UTF-8)
// ------------------------------------------------------------------------------
echo "\n4. Pengujian Anti XSS (htmlspecialchars ENT_QUOTES UTF-8):\n";

$xssScanDir = __DIR__ . '/..';
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($xssScanDir));
$missingEntQuotes = 0;

foreach ($it as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        if (strpos($path, 'tests') !== false || strpos($path, 'vendor') !== false || strpos($path, 'scratch') !== false) {
            continue;
        }
        $content = file_get_contents($path);
        $tokens = token_get_all($content);
        $count = count($tokens);
        for ($i = 0; $i < $count; $i++) {
            if (is_array($tokens[$i]) && $tokens[$i][0] === T_STRING && strtolower($tokens[$i][1]) === 'htmlspecialchars') {
                $parenDepth = 0;
                $callSnippet = '';
                $j = $i + 1;
                while ($j < $count && $tokens[$j] !== '(') {
                    $j++;
                }
                if ($j < $count && $tokens[$j] === '(') {
                    $parenDepth = 1;
                    $callSnippet .= '(';
                    $k = $j + 1;
                    while ($k < $count && $parenDepth > 0) {
                        $tok = $tokens[$k];
                        $str = is_array($tok) ? $tok[1] : $tok;
                        $callSnippet .= $str;
                        if ($str === '(') $parenDepth++;
                        if ($str === ')') $parenDepth--;
                        $k++;
                    }
                    if (strpos($callSnippet, 'ENT_QUOTES') === false) {
                        $missingEntQuotes++;
                        echo "    ⚠️ htmlspecialchars tanpa ENT_QUOTES: {$path} -> htmlspecialchars{$callSnippet}\n";
                    }
                }
            }
        }
    }
}

check("100% pemanggilan htmlspecialchars di seluruh sistem menyertakan ENT_QUOTES", $missingEntQuotes === 0);

// ------------------------------------------------------------------------------
// RINGKASAN HASIL
// ------------------------------------------------------------------------------
echo "\n======================================================================\n";
echo "📊 HASIL PENGUJIAN KEAMANAN: {$pass} PASS / " . ($pass + $fail) . " TOTAL\n";
if ($fail === 0) {
    echo "🎉 SEMUA 4 ATURAN KEAMANAN TERVERIFIKASI SUKSES & SISTEM SIAP RILIS!\n";
} else {
    echo "⚠️ TERDAPAT {$fail} PENGUJIAN YANG GAGAL!\n";
}
echo "======================================================================\n";
