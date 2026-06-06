<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\AuditLogModel;

class AuthController extends Controller
{
    private UserModel $userModel;
    private AuditLogModel $auditLogModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->auditLogModel = new AuditLogModel();
    }

    public function showLogin(): void
    {
        if (is_logged_in()) {
            redirect('/dashboard');
        }
        $this->render('auth/login');
    }

    public function login(): void
    {
        $this->validateCsrf();
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($this->auditLogModel->countRecentFailedLogins($email) >= 15) {
            $this->auditLogModel->log(null, "Login blocked due to rate limit: $email");
            $this->render('auth/login', ['error' => 'Too many failed login attempts. Please try again in 15 minutes.']);
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['status'] === 'deactivated') {
                $this->auditLogModel->log($user['id'], "Failed login attempt: Account deactivated");
                $this->render('auth/login', ['error' => 'Your account has been deactivated. Please contact an administrator.']);
                return;
            }

            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['firstname'] . ' ' . $user['lastname'];

            $this->auditLogModel->log($user['id'], "User logged in");
            redirect('/dashboard');
        } else {
            $this->auditLogModel->log(null, "Failed login attempt (Invalid credentials) for email: $email");
            $this->render('auth/login', ['error' => 'Invalid email or password']);
        }
    }

    public function showRegister(): void
    {
        $this->render('auth/register');
    }

    public function register(): void
    {
        $this->validateCsrf();
        $data = [
            'firstname' => $_POST['firstname'] ?? '',
            'lastname' => $_POST['lastname'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
        ];

        if (empty($data['firstname']) || empty($data['lastname']) || empty($data['email']) || empty($data['password'])) {
            $this->render('auth/register', ['error' => 'All fields are required']);
            return;
        }

        if ($this->userModel->findByEmail($data['email'])) {
            $this->render('auth/register', ['error' => 'Email already exists']);
            return;
        }

        if ($this->userModel->create($data)) {
            $this->auditLogModel->log(null, "New user registered: {$data['email']}");
            redirect('/login');
        } else {
            $this->render('auth/register', ['error' => 'Registration failed']);
        }
    }

    public function logout(): void
    {
        $this->validateCsrf();
        $userId = $_SESSION['user_id'] ?? null;
        $this->auditLogModel->log($userId, "User logged out");
        session_destroy();
        redirect('/login');
    }
}
