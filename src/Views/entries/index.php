<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Buchungen</h1>
    <a href="/entries/add" class="btn btn-success">Neue Buchung</a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="/entries" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="vehicle_id" class="form-label">Fahrzeug</label>
                <select name="vehicle_id" id="vehicle_id" class="form-select">
                    <option value="">Alle Fahrzeuge</option>
                    <?php foreach ($vehicles as $vehicle): ?>
                        <option value="<?= $vehicle['id'] ?>" <?= ($filters['vehicle_id'] == $vehicle['id']) ? 'selected' : '' ?>>
                            <?= e($vehicle['name']) ?> (<?= e($vehicle['license_plate']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="month" class="form-label">Monat</label>
                <select name="month" id="month" class="form-select">
                    <option value="">Alle Monate</option>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= ($filters['month'] == $m) ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="year" class="form-label">Jahr</label>
                <select name="year" id="year" class="form-select">
                    <option value="">Alle Jahre</option>
                    <?php for ($y = date('Y'); $y >= 2000; $y--): ?>
                        <option value="<?= $y ?>" <?= ($filters['year'] == $y) ? 'selected' : '' ?>>
                            <?= $y ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary w-100">Filtern</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Datum</th>
                    <th>Fahrzeug</th>
                    <th>Kategorien</th>
                    <th>Betrag</th>
                    <th>KM-Stand</th>
                    <th>Details</th>
                    <th class="text-end">Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($entries)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Keine Buchungen gefunden.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($entries as $entry): ?>
                        <tr>
                            <td><?= e(date('d.m.Y', strtotime($entry['date']))) ?></td>
                            <td><?= e($entry['vehicle_name']) ?></td>
                            <td><span class="badge bg-info text-dark"><?= e($entry['categories'] ?? 'Unkategorisiert') ?></span></td>
                            <td class="fw-bold text-primary"><?= number_format((float)$entry['amount'], 2, ',', '.') ?> €</td>
                            <td><?= number_format((int)$entry['mileage'], 0, ',', '.') ?> km</td>
                            <td>
                                <?php if ($entry['liters']): ?>
                                    <small class="text-muted">
                                        ⛽ <?= number_format((float)$entry['liters'], 2, ',', '.') ?> L @ <?= number_format((float)$entry['price_per_liter'], 3, ',', '.') ?> €/L
                                    </small><br>
                                <?php endif; ?>
                                <small class="text-truncate d-inline-block" style="max-width: 150px;"><?= e($entry['description']) ?></small>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="/entries/edit?id=<?= $entry['id'] ?>" class="btn btn-sm btn-outline-primary">Bearbeiten</a>
                                <form action="/entries/delete" method="POST" class="d-inline" onsubmit="return confirm('Buchung wirklich löschen?');">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="id" value="<?= $entry['id'] ?>">
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
