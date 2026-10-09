<?php
/**
 * PAKAINI - Admin Entry Point
 */
require_once __DIR__ . '/config.php';

$user = current_user($pdo);
if ($user && (($user['role'] ?? '') === 'admin' || in_array(strtolower($user['username'] ?? ''), ['admin', 'adminzul', 'superadmin']))) {
    $_SESSION['role'] = 'admin';
    $_SESSION['user_role'] = 'admin';
    redirect('panel_pusat_pakaini_99x.php');
} else {
    redirect('login.php');
}
