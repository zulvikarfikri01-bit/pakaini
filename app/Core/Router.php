<?php
/**
 * PAKAINI - Router
 * Fleksibel mendukung URL bersih (.htaccess) dan query parameter (?page=...)
 * Menjamin kompatibilitas 100% pada semua shared hosting
 */

defined('ROOT_PATH') or define('ROOT_PATH', dirname(__DIR__, 2));

class Router {
    private array $routes = [];

    public function register(string $page, string $controllerClass, string $actionMethod = 'index'): void {
        $this->routes[$page] = [
            'controller' => $controllerClass,
            'action'     => $actionMethod
        ];
    }

    public function dispatch(): void {
        // Ambil penentu halaman dari GET param atau PATH_INFO / REQUEST_URI
        $page = $_GET['page'] ?? '';
        $action = $_GET['action'] ?? '';

        if (empty($page)) {
            // Cek path URL jika .htaccess aktif
            $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
            $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
            
            // Hapus prefix subfolder
            $relative = trim(substr($requestUri, strlen($scriptDir)), '/');
            // Hapus index.php jika ada
            $relative = preg_replace('/^index\.php(\/)?/', '', $relative);
            $relative = trim($relative, '/');

            if (!empty($relative)) {
                $segments = explode('/', $relative);
                $page = $segments[0] ?? 'dashboard';
                if (!empty($segments[1]) && empty($action)) {
                    $action = $segments[1];
                }
            } else {
                $page = 'dashboard';
            }
        }

        // Default route jika kosong
        if (empty($page) || $page === 'index') {
            $page = 'dashboard';
        }

        // Normalisasi nama page
        $routeKey = strtolower($page);

        if (!isset($this->routes[$routeKey])) {
            http_response_code(404);
            require_once ROOT_PATH . '/views/404.php';
            exit;
        }

        $route = $this->routes[$routeKey];
        $controllerName = $route['controller'];
        $controllerFile = ROOT_PATH . '/app/Controllers/' . $controllerName . '.php';

        if (!file_exists($controllerFile)) {
            die("Controller file {$controllerName}.php tidak ditemukan!");
        }

        require_once $controllerFile;
        $controller = new $controllerName();

        // Tentukan method action
        $targetAction = !empty($action) ? $action : $route['action'];

        if (!method_exists($controller, $targetAction)) {
            $targetAction = 'index';
        }

        $controller->$targetAction();
    }
}
