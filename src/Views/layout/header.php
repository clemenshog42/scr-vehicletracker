<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Fahrzeugkosten-Tracker') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 60px; }
    </style>
</head>
<body>
    <?php $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/">Fahrzeugkosten-Tracker</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExampleDefault">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">
                    <?php if (is_logged_in()): ?>
                        <li class="nav-item"><a class="nav-link <?= $currentPath === '/dashboard' ? 'active' : '' ?>" href="/dashboard">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPath, '/vehicles') ? 'active' : '' ?>" href="/vehicles">Fahrzeuge</a></li>
                        <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPath, '/entries') ? 'active' : '' ?>" href="/entries">Buchungen</a></li>
                        <?php if (get_user_role() === 'administrator'): ?>
                            <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPath, '/admin/users') ? 'active' : '' ?>" href="/admin/users">User-Management</a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if (is_logged_in()): ?>
                        <li class="nav-item"><span class="nav-link text-light">Hallo, <?= e($_SESSION['user_name']) ?></span></li>
                        <li class="nav-item">
                            <form action="/logout" method="POST" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <button type="submit" class="btn nav-link border-0 bg-transparent">Logout</button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link <?= $currentPath === '/login' ? 'active' : '' ?>" href="/login">Login</a></li>
                        <li class="nav-item"><a class="nav-link <?= $currentPath === '/register' ? 'active' : '' ?>" href="/register">Registrieren</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container">
