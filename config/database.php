<?php
// Mengambil kunci rahasia langsung dari Railway
$database_url = getenv("DATABASE_URL");

if (!$database_url) {
    die("Kunci DATABASE_URL belum terdeteksi dari Railway.");
}

// Memecah teks kunci agar bisa dibaca sistem
$db = parse_url($database_url);
$host = $db['host'];
$port = $db['port'];
$user = $db['user'];
$pass = $db['pass'];
$dbname = ltrim($db['path'], '/');

// Merakit ulang untuk mesin PostgreSQL
$dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";

try {
    $conn = new PDO($dsn, $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi Supabase Gagal: " . $e->getMessage());
}
?>
