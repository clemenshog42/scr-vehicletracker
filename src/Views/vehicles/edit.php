<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="card-title h3 mb-4">Fahrzeug bearbeiten</h1>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endif; ?>

                <form action="/vehicles/edit" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="id" value="<?= e($data['id']) ?>">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Bezeichnung (Marke, Modell)</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= e($data['name'] ?? '') ?>" required placeholder="z.B. VW Golf">
                    </div>

                    <div class="mb-3">
                        <label for="license_plate" class="form-label">Kennzeichen</label>
                        <input type="text" class="form-control" id="license_plate" name="license_plate" value="<?= e($data['license_plate'] ?? '') ?>" required placeholder="z.B. W-12345B">
                    </div>

                    <div class="mb-3">
                        <label for="registration_date" class="form-label">Erstzulassung</label>
                        <input type="date" class="form-control" id="registration_date" name="registration_date" value="<?= e($data['registration_date'] ?? '') ?>" required>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="/vehicles" class="btn btn-outline-secondary">Abbrechen</a>
                        <button type="submit" class="btn btn-primary">Fahrzeug speichern</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
