<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\AuditLogModel;

class AdminController extends Controller {
    private UserModel $userModel;
    private AuditLogModel $auditLogModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->auditLogModel = new AuditLogModel();
    }

    public function index(): void {
        $this->requireRole('administrator');
        $users = $this->userModel->getAll();
        $this->render('admin/users', [
            'title' => 'Benutzerverwaltung',
            'users' => $users
        ]);
    }

    public function updateStatus(): void {
        $this->requireRole('administrator');
        $this->validateCsrf();

        $userId = (int)($_POST['user_id'] ?? 0);
        $newStatus = $_POST['status'] ?? '';

        if ($userId === (int)$_SESSION['user_id']) {
            die("Sie können Ihren eigenen Account nicht deaktivieren.");
        }

        if (!in_array($newStatus, ['active', 'deactivated'])) {
            die("Ungültiger Status");
        }

        if ($this->userModel->updateStatus($userId, $newStatus)) {
            $this->auditLogModel->log($_SESSION['user_id'], "Benutzer-Status geändert (ID: $userId) auf: $newStatus");
            redirect('/admin/users');
        } else {
            die("Fehler beim Aktualisieren des Status");
        }
    }
}
