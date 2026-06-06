<?php

namespace App\Controllers;

use App\Models\VehicleModel;
use App\Models\AuditLogModel;

class VehicleController extends Controller
{
    private VehicleModel $vehicleModel;
    private AuditLogModel $auditLogModel;

    public function __construct()
    {
        $this->vehicleModel = new VehicleModel();
        $this->auditLogModel = new AuditLogModel();
    }

    public function index(): void
    {
        $this->requireLogin();
        $vehicles = $this->vehicleModel->getAllByUser($_SESSION['user_id']);
        $this->render('vehicles/index', [
            'title' => 'Meine Fahrzeuge',
            'vehicles' => $vehicles
        ]);
    }

    public function showAdd(): void
    {
        $this->requireLogin();
        $this->render('vehicles/add', ['title' => 'Fahrzeug hinzufügen']);
    }

    public function add(): void
    {
        $this->requireLogin();
        $this->validateCsrf();

        $data = [
            'user_id' => $_SESSION['user_id'],
            'name' => $_POST['name'] ?? '',
            'license_plate' => $_POST['license_plate'] ?? '',
            'registration_date' => $_POST['registration_date'] ?? '',
        ];

        if (empty($data['name']) || empty($data['license_plate']) || empty($data['registration_date'])) {
            $this->render('vehicles/add', ['error' => 'Alle Felder sind erforderlich', 'data' => $data]);
            return;
        }

        if ($this->vehicleModel->create($data)) {
            $this->auditLogModel->log($_SESSION['user_id'], "Fahrzeug hinzugefügt: {$data['name']}");
            redirect('/vehicles');
        } else {
            $this->render('vehicles/add', ['error' => 'Fehler beim Speichern', 'data' => $data]);
        }
    }

    public function delete(): void
    {
        $this->requireLogin();
        $this->validateCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        if ($this->vehicleModel->softDelete($id, $_SESSION['user_id'])) {
            $this->auditLogModel->log($_SESSION['user_id'], "Fahrzeug gelöscht (ID: $id)");
            redirect('/vehicles');
        } else {
            die("Fehler beim Löschen");
        }
    }

    public function showEdit(): void
    {
        $this->requireLogin();
        $id = (int) ($_GET['id'] ?? 0);

        $vehicle = $this->vehicleModel->findById($id, $_SESSION['user_id']);
        if (!$vehicle) {
            die("Fahrzeug nicht gefunden");
        }

        $this->render('vehicles/edit', ['title' => 'Fahrzeug bearbeiten', 'data' => $vehicle]);
    }

    public function edit(): void
    {
        $this->requireLogin();
        $this->validateCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $userId = $_SESSION['user_id'];

        if (!$this->vehicleModel->findById($id, $userId)) {
            die("Fahrzeug nicht gefunden");
        }

        $data = [
            'name' => $_POST['name'] ?? '',
            'license_plate' => $_POST['license_plate'] ?? '',
            'registration_date' => $_POST['registration_date'] ?? '',
        ];

        if (empty($data['name']) || empty($data['license_plate']) || empty($data['registration_date'])) {
            $data['id'] = $id;
            $this->render('vehicles/edit', ['error' => 'Alle Felder sind erforderlich', 'data' => $data]);
            return;
        }

        if ($this->vehicleModel->update($id, $userId, $data)) {
            $this->auditLogModel->log($userId, "Fahrzeug bearbeitet (ID: $id)");
            redirect('/vehicles');
        } else {
            $data['id'] = $id;
            $this->render('vehicles/edit', ['error' => 'Fehler beim Speichern', 'data' => $data]);
        }
    }
}
