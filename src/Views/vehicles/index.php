<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Meine Fahrzeuge</h1>
    <a href="/vehicles/add" class="btn btn-primary">Fahrzeug hinzufügen</a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Bezeichnung</th>
                    <th>Kennzeichen</th>
                    <th>Erstzulassung</th>
                    <th class="text-end">Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($vehicles)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Keine Fahrzeuge registriert.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($vehicles as $vehicle): ?>
                        <tr>
                            <td><?= e($vehicle['name']) ?></td>
                            <td><?= e($vehicle['license_plate']) ?></td>
                            <td><?= e(date('d.m.Y', strtotime($vehicle['registration_date']))) ?></td>
                            <td class="text-end text-nowrap">
                                <a href="/vehicles/edit?id=<?= $vehicle['id'] ?>" class="btn btn-sm btn-outline-primary">Bearbeiten</a>
                                <form action="/vehicles/delete" method="POST" class="d-inline" onsubmit="return confirm('Möchten Sie dieses Fahrzeug wirklich löschen?');">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="id" value="<?= $vehicle['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Löschen</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
