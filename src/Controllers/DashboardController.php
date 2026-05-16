<?php

namespace App\Controllers;

class DashboardController extends Controller {
    public function index(): void {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];
        
        $entryModel = new \App\Models\EntryModel();
        $monthlyStats = $entryModel->getMonthlyStats($userId);
        $consumptionStats = $entryModel->getFuelConsumptionStats($userId);

        $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'monthlyStats' => $monthlyStats,
            'consumptionStats' => $consumptionStats
        ]);
    }
}
