<?php

namespace App\Controllers;

use App\Models\EntryModel;
use App\Models\VehicleModel;
use App\Models\CategoryModel;
use App\Models\AuditLogModel;

class EntryController extends Controller
{
    private EntryModel $entryModel;
    private VehicleModel $vehicleModel;
    private CategoryModel $categoryModel;
    private AuditLogModel $auditLogModel;

    public function __construct()
    {
        $this->entryModel = new EntryModel();
        $this->vehicleModel = new VehicleModel();
        $this->categoryModel = new CategoryModel();
        $this->auditLogModel = new AuditLogModel();
    }

    public function index(): void
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];

        $filters = [
            'vehicle_id' => $_GET['vehicle_id'] ?? null,
            'month' => $_GET['month'] ?? null,
            'year' => $_GET['year'] ?? null,
        ];

        $entries = $this->entryModel->getAllByUser($userId, $filters);
        $vehicles = $this->vehicleModel->getAllByUser($userId);

        $this->render('entries/index', [
            'title' => 'Buchungen',
            'entries' => $entries,
            'vehicles' => $vehicles,
            'filters' => $filters
        ]);
    }

    public function showAdd(): void
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];

        $vehicles = $this->vehicleModel->getAllByUser($userId);
        $categories = $this->categoryModel->getAll();

        $this->render('entries/add', [
            'title' => 'Buchung hinzufügen',
            'vehicles' => $vehicles,
            'categories' => $categories
        ]);
    }

    public function add(): void
    {
        $this->requireLogin();
        $this->validateCsrf();

        $userId = $_SESSION['user_id'];
        $amountStr = str_replace(',', '.', $_POST['amount'] ?? '0');
        $litersStr = str_replace(',', '.', $_POST['liters'] ?? '0');
        $priceStr = str_replace(',', '.', $_POST['price_per_liter'] ?? '0');

        $data = [
            'vehicle_id' => (int) ($_POST['vehicle_id'] ?? 0),
            'date' => $_POST['date'] ?? '',
            'amount' => (float) $amountStr,
            'description' => $_POST['description'] ?? '',
            'mileage' => (int) ($_POST['mileage'] ?? 0),
            'is_refuel' => !empty($_POST['is_refuel']),
            'liters' => (float) $litersStr,
            'price_per_liter' => (float) $priceStr,
            'categories' => isset($_POST['category_id']) ? [$_POST['category_id']] : []
        ];

        // Recalculate amount if it's a refuel entry to ensure consistency
        if ($data['is_refuel']) {
            $data['amount'] = round($data['liters'] * $data['price_per_liter'], 2);
        }

        // Validation
        if (!$this->vehicleModel->findById($data['vehicle_id'], $userId)) {
            die("Invalid vehicle");
        }

        if (empty($data['date']) || $data['amount'] <= 0 || $data['mileage'] < 0) {
            $this->showError('Bitte füllen Sie alle Pflichtfelder korrekt aus.', $data);
            return;
        }

        if ($data['is_refuel'] && ($data['liters'] <= 0 || $data['price_per_liter'] <= 0)) {
            $this->showError('Für Tankbuchungen müssen Liter und Preis angegeben werden.', $data);
            return;
        }

        if ($this->entryModel->create($data)) {
            $this->auditLogModel->log($userId, "Buchung hinzugefügt für Fahrzeug ID: {$data['vehicle_id']}");
            redirect('/entries');
        } else {
            $this->showError('Fehler beim Speichern der Buchung.', $data);
        }
    }

    private function showError(string $message, array $data): void
    {
        $userId = $_SESSION['user_id'];
        $this->render('entries/add', [
            'error' => $message,
            'data' => $data,
            'vehicles' => $this->vehicleModel->getAllByUser($userId),
            'categories' => $this->categoryModel->getAll()
        ]);
    }

    public function showEdit(): void
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];
        $id = (int) ($_GET['id'] ?? 0);

        $entry = $this->entryModel->findById($id, $userId);
        if (!$entry) {
            die("Buchung nicht gefunden");
        }

        // Fetch categories for this entry
        $entryCategories = $this->entryModel->getCategoriesForEntry($id);
        $entry['categories'] = array_column($entryCategories, 'category_id');

        $this->render('entries/edit', [
            'title' => 'Buchung bearbeiten',
            'data' => $entry,
            'vehicles' => $this->vehicleModel->getAllByUser($userId),
            'categories' => $this->categoryModel->getAll()
        ]);
    }

    public function edit(): void
    {
        $this->requireLogin();
        $this->validateCsrf();

        $userId = $_SESSION['user_id'];
        $id = (int) ($_POST['id'] ?? 0);

        if (!$this->entryModel->findById($id, $userId)) {
            die("Buchung nicht gefunden");
        }

        $amountStr = str_replace(',', '.', $_POST['amount'] ?? '0');
        $litersStr = str_replace(',', '.', $_POST['liters'] ?? '0');
        $priceStr = str_replace(',', '.', $_POST['price_per_liter'] ?? '0');

        $data = [
            'vehicle_id' => (int) ($_POST['vehicle_id'] ?? 0),
            'date' => $_POST['date'] ?? '',
            'amount' => (float) $amountStr,
            'description' => $_POST['description'] ?? '',
            'mileage' => (int) ($_POST['mileage'] ?? 0),
            'is_refuel' => !empty($_POST['is_refuel']),
            'liters' => (float) $litersStr,
            'price_per_liter' => (float) $priceStr,
            'categories' => isset($_POST['category_id']) ? [$_POST['category_id']] : []
        ];

        // Recalculate amount if it's a refuel entry to ensure consistency
        if ($data['is_refuel']) {
            $data['amount'] = round($data['liters'] * $data['price_per_liter'], 2);
        }

        // Validation
        if (!$this->vehicleModel->findById($data['vehicle_id'], $userId)) {
            die("Invalid vehicle");
        }

        if (empty($data['date']) || $data['amount'] <= 0 || $data['mileage'] < 0) {
            $data['id'] = $id;
            $this->showEditError('Bitte füllen Sie alle Pflichtfelder korrekt aus.', $data);
            return;
        }

        if ($data['is_refuel'] && ($data['liters'] <= 0 || $data['price_per_liter'] <= 0)) {
            $data['id'] = $id;
            $this->showEditError('Für Tankbuchungen müssen Liter und Preis angegeben werden.', $data);
            return;
        }

        if ($this->entryModel->update($id, $data)) {
            $this->auditLogModel->log($userId, "Buchung bearbeitet (ID: $id)");
            redirect('/entries');
        } else {
            $data['id'] = $id;
            $this->showEditError('Fehler beim Speichern der Buchung.', $data);
        }
    }

    private function showEditError(string $message, array $data): void
    {
        $userId = $_SESSION['user_id'];
        $this->render('entries/edit', [
            'error' => $message,
            'data' => $data,
            'vehicles' => $this->vehicleModel->getAllByUser($userId),
            'categories' => $this->categoryModel->getAll()
        ]);
    }

    public function delete(): void
    {
        $this->requireLogin();
        $this->validateCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        if ($this->entryModel->softDelete($id, $_SESSION['user_id'])) {
            $this->auditLogModel->log($_SESSION['user_id'], "Buchung gelöscht (ID: $id)");
            redirect('/entries');
        } else {
            die("Fehler beim Löschen");
        }
    }
}
