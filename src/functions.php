<?php

/**
 * Escape HTML for output
 */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * CSRF Token generation
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRF Token verification
 */
function verify_csrf_token(?string $token): bool
{
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirect helper
 */
function redirect(string $path): void
{
    header("Location: $path");
    exit;
}

/**
 * Get current user role
 */
function get_user_role(): string
{
    return $_SESSION['user_role'] ?? 'anonymous';
}

/**
 * Check if user is logged in
 */
function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}
