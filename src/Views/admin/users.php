<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Benutzerverwaltung</h1>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Rolle</th>
                    <th>Status</th>
                    <th class="text-end">Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= e($user['firstname'] . ' ' . $user['lastname']) ?></td>
                        <td><?= e($user['email']) ?></td>
                        <td><span class="badge bg-secondary"><?= e($user['role']) ?></span></td>
                        <td>
                            <?php if ($user['status'] === 'active'): ?>
                                <span class="badge bg-success">Aktiv</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Deaktiviert</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <form action="/admin/users/status" method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <?php if ($user['status'] === 'active'): ?>
                                        <input type="hidden" name="status" value="deactivated">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Deaktivieren</button>
                                    <?php else: ?>
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" class="btn btn-sm btn-outline-success">Aktivieren</button>
                                    <?php endif; ?>
                                </form>
                            <?php else: ?>
                                <small class="text-muted">Aktueller Benutzer</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
