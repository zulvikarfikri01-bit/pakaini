<?php
/**
 * PAKAINI - Base Controller
 * Menangani render template, redirect, JSON response, flash messages, dan security checks
 */

defined('ROOT_PATH') or define('ROOT_PATH', dirname(__DIR__, 2));
require_once ROOT_PATH . '/app/Core/Auth.php';

abstract class Controller {
    protected ?array $user;

    public function __construct() {
        Auth::init();
        $this->user = Auth::user();
    }

    /**
     * Render view dengan layout wrapper
     */
    protected function render(string $view, array $data = [], string $layout = 'main'): void {
        // Ekstrak data variabel untuk view
        $data['user'] = $this->user;
        $data['flash'] = $this->getAllFlash();
        $data['csrf_token'] = $this->csrfToken();
        extract($data);

        // Buffer konten view spesifik
        $viewFile = ROOT_PATH . '/views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            die("View file tidak ditemukan: " . htmlspecialchars($view, ENT_QUOTES, 'UTF-8'));
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Render layout
        $layoutFile = ROOT_PATH . '/views/layouts/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    /**
     * Kirim respons JSON (untuk AJAX claim task, validasi, dll)
     */
    protected function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    /**
     * Redirect ke URL tertentu
     */
    protected function redirect(string $path): void {
        if (str_starts_with($path, 'http')) {
            header("Location: " . $path);
        } else {
            $url = BASE_URL . '/' . ltrim($path, '/');
            header("Location: " . $url);
        }
        exit;
    }

    /**
     * Pastikan user sudah login
     */
    protected function authCheck(): void {
        if (!Auth::check()) {
            $this->setFlash('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
            $this->redirect('index.php?page=login');
        }
    }

    /**
     * Pastikan user adalah admin
     */
    protected function adminCheck(): void {
        $this->authCheck();
        if (!Auth::isAdmin()) {
            $this->setFlash('error', 'Akses ditolak. Halaman ini hanya untuk Administrator.');
            $this->redirect('index.php?page=dashboard');
        }
    }

    /**
     * Set Flash message session
     */
    protected function setFlash(string $type, string $message): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash'][$type][] = $message;
    }

    /**
     * Ambil semua flash messages dan bersihkan
     */
    protected function getAllFlash(): array {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flash;
    }

    /**
     * Buat CSRF Token
     */
    protected function csrfToken(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validasi CSRF Token
     */
    protected function validateCsrf(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        return !empty($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }
}
