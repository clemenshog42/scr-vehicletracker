<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="card-title h3 mb-4">Buchung bearbeiten</h1>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endif; ?>

                <form action="/entries/edit" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="id" value="<?= e($data['id']) ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_id" class="form-label">Fahrzeug *</label>
                            <select name="vehicle_id" id="vehicle_id" class="form-select" required>
                                <?php foreach ($vehicles as $vehicle): ?>
                                    <option value="<?= $vehicle['id'] ?>" <?= (($data['vehicle_id'] ?? 0) == $vehicle['id']) ? 'selected' : '' ?>>
                                        <?= e($vehicle['name']) ?> (<?= e($vehicle['license_plate']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date" class="form-label">Datum *</label>
                            <input type="date" class="form-control" id="date" name="date" value="<?= e($data['date'] ?? date('Y-m-d')) ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="category_id" class="form-label">Kategorie *</label>
                            <select name="category_id" id="category_id" class="form-select" required onchange="handleCategoryChange(this)">
                                <option value="">-- Bitte wählen --</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" data-name="<?= e($category['name']) ?>"
                                        <?= (($data['category_id'] ?? (is_array($data['categories'] ?? null) ? ($data['categories'][0] ?? 0) : 0)) == $category['id']) ? 'selected' : '' ?>>
                                        <?= e($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="amount" class="form-label">Betrag (€) *</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="<?= e($data['amount'] ?? '') ?>" required>
                            </div>
                            <small id="amount_hint" class="form-text text-muted" style="display: none;">Wird automatisch aus Litern und Preis berechnet.</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="mileage" class="form-label">Kilometerstand *</label>
                            <input type="number" class="form-control" id="mileage" name="mileage" value="<?= e($data['mileage'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div id="refuel_section" style="display: none;" class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title">Tankdetails</h6>
                            <input type="hidden" name="is_refuel" id="is_refuel_hidden" value="0">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="liters" class="form-label">Getankte Liter</label>
                                    <input type="number" step="0.01" class="form-control" id="liters" name="liters" value="<?= e($data['liters'] ?? '') ?>" oninput="calculateAmount()">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="price_per_liter" class="form-label">Preis pro Liter (€)</label>
                                    <input type="number" step="0.001" class="form-control" id="price_per_liter" name="price_per_liter" value="<?= e($data['price_per_liter'] ?? '') ?>" oninput="calculateAmount()">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Beschreibung / Notiz</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= e($data['description'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="/entries" class="btn btn-outline-secondary">Abbrechen</a>
                        <button type="submit" class="btn btn-success">Buchung speichern</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function handleCategoryChange(select) {
    const selectedOption = select.options[select.selectedIndex];
    const categoryName = selectedOption ? selectedOption.getAttribute('data-name') : '';
    const section = document.getElementById('refuel_section');
    const hiddenInput = document.getElementById('is_refuel_hidden');
    const amountInput = document.getElementById('amount');
    const amountHint = document.getElementById('amount_hint');

    if (categoryName === 'Tanken') {
        section.style.display = 'block';
        hiddenInput.name = 'is_refuel';
        hiddenInput.value = '1';
        amountInput.readOnly = true;
        amountInput.classList.add('bg-light');
        amountHint.style.display = 'block';
        calculateAmount();
    } else {
        section.style.display = 'none';
        hiddenInput.name = 'is_refuel_disabled';
        hiddenInput.value = '0';
        amountInput.readOnly = false;
        amountInput.classList.remove('bg-light');
        amountHint.style.display = 'none';
    }
}

function calculateAmount() {
    const liters = parseFloat(document.getElementById('liters').value) || 0;
    const price = parseFloat(document.getElementById('price_per_liter').value) || 0;
    const amountInput = document.getElementById('amount');
    
    const categorySelect = document.getElementById('category_id');
    const selectedOption = categorySelect.options[categorySelect.selectedIndex];
    const categoryName = selectedOption ? selectedOption.getAttribute('data-name') : '';

    if (categoryName === 'Tanken') {
        const total = (liters * price).toFixed(2);
        amountInput.value = total;
    }
}

// Initial state check
window.onload = function() {
    handleCategoryChange(document.getElementById('category_id'));
};
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
