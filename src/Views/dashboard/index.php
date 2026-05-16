<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row">
    <div class="col-12">
        <h1 class="mb-4">Willkommen, <?= e($_SESSION['user_name']) ?>!</h1>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm border-primary">
            <div class="card-body text-center">
                <h5 class="card-title">Schnellzugriff</h5>
                <div class="d-grid gap-2 mt-4">
                    <a href="/entries/add" class="btn btn-success">Neue Buchung</a>
                    <a href="/vehicles/add" class="btn btn-primary">Fahrzeug hinzufügen</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-light fw-bold">Verbrauchsauswertung (ø l/100 km)</div>
            <div class="card-body">
                <?php if (empty($consumptionStats)): ?>
                    <p class="text-muted text-center py-3">Noch nicht genügend Tankdaten für eine Auswertung vorhanden.</p>
                <?php else: ?>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Fahrzeug</th>
                                <th>Zeitraum (km)</th>
                                <th>Gesamt Liter</th>
                                <th>Durchschnitt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($consumptionStats as $stat): ?>
                                <tr>
                                    <td><?= e($stat['vehicle_name']) ?></td>
                                    <td><?= number_format($stat['start_km']) ?> - <?= number_format($stat['end_km']) ?></td>
                                    <td><?= number_format($stat['total_liters'], 2) ?> L</td>
                                    <td class="fw-bold text-success"><?= number_format($stat['avg_consumption'], 2) ?> l/100km</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light fw-bold">Monatliche Kostenübersicht</div>
            <div class="card-body">
                <?php if (empty($monthlyStats)): ?>
                    <p class="text-muted text-center py-3">Noch keine Buchungen vorhanden.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Zeitraum</th>
                                    <th>Fahrzeug</th>
                                    <th class="text-end">Gesamtkosten</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($monthlyStats as $stat): ?>
                                    <tr>
                                        <td><?= date('F Y', mktime(0, 0, 0, $stat['month'], 1, $stat['year'])) ?></td>
                                        <td><?= e($stat['vehicle_name']) ?></td>
                                        <td class="text-end fw-bold"><?= number_format($stat['total_amount'], 2, ',', '.') ?> €</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
