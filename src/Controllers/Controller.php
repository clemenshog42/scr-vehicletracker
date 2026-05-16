<?php

namespace App\Controllers;

abstract class Controller {
    protected function render(string $view, array $data = []): void {
        extract($data);
        $viewFile = __DIR__ . "/../Views/{$view}.php";
        
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            die("View $view not found");
        }
    }

    protected function validateCsrf(): void {
        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            die("Invalid CSRF token");
        }
    }

    protected function requireLogin(): void {
        if (!is_logged_in()) {
            redirect('/login');
        }
    }

    protected function requireRole(string $role): void {
        $this->requireLogin();
        if (get_user_role() !== $role) {
            die("Unauthorized - $role role required");
        }
    }
}
