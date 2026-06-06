<?php

namespace App\Models;

use PDO;

class EntryModel extends Model
{
    public function getAllByUser(int $userId, array $filters = []): array
    {
        $sql = "SELECT e.*, v.name as vehicle_name, r.liters, r.price_per_liter, 
                GROUP_CONCAT(c.name SEPARATOR ', ') as categories
                FROM cost_entries e
                JOIN vehicles v ON e.vehicle_id = v.id
                LEFT JOIN refuel_entries r ON e.id = r.entry_id
                LEFT JOIN entry_category ec ON e.id = ec.entry_id
                LEFT JOIN categories c ON ec.category_id = c.id
                WHERE v.user_id = ? AND e.deleted_at IS NULL";

        $params = [$userId];

        if (!empty($filters['vehicle_id'])) {
            $sql .= " AND e.vehicle_id = ?";
            $params[] = $filters['vehicle_id'];
        }

        if (!empty($filters['month'])) {
            $sql .= " AND MONTH(e.date) = ?";
            $params[] = $filters['month'];
        }

        if (!empty($filters['year'])) {
            $sql .= " AND YEAR(e.date) = ?";
            $params[] = $filters['year'];
        }

        $sql .= " GROUP BY e.id ORDER BY e.date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $this->db->beginTransaction();
        try {
            // 1. Insert into cost_entries
            $stmt = $this->db->prepare(
                "INSERT INTO cost_entries (vehicle_id, date, amount, description, mileage) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $data['vehicle_id'],
                $data['date'],
                $data['amount'],
                $data['description'],
                $data['mileage']
            ]);
            $entryId = (int) $this->db->lastInsertId();

            // 2. If it's a refuel entry, insert into refuel_entries
            if (!empty($data['is_refuel'])) {
                $stmt = $this->db->prepare(
                    "INSERT INTO refuel_entries (entry_id, liters, price_per_liter) VALUES (?, ?, ?)"
                );
                $stmt->execute([$entryId, $data['liters'], $data['price_per_liter']]);
            }

            // 3. Handle categories (m:n)
            if (!empty($data['categories'])) {
                $stmt = $this->db->prepare("INSERT INTO entry_category (entry_id, category_id) VALUES (?, ?)");
                foreach ($data['categories'] as $categoryId) {
                    $stmt->execute([$entryId, $categoryId]);
                }
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("EntryModel::create Error: " . $e->getMessage());
            return false;
        }
    }

    public function findById(int $id, int $userId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT e.*, r.liters, r.price_per_liter 
             FROM cost_entries e
             JOIN vehicles v ON e.vehicle_id = v.id
             LEFT JOIN refuel_entries r ON e.id = r.entry_id
             WHERE e.id = ? AND v.user_id = ? AND e.deleted_at IS NULL"
        );
        $stmt->execute([$id, $userId]);
        $entry = $stmt->fetch();
        if ($entry) {
            $entry['is_refuel'] = !empty($entry['liters']);
        }
        return $entry ?: null;
    }

    public function getCategoriesForEntry(int $entryId): array
    {
        $stmt = $this->db->prepare("SELECT category_id FROM entry_category WHERE entry_id = ?");
        $stmt->execute([$entryId]);
        return $stmt->fetchAll();
    }

    public function update(int $id, array $data): bool
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "UPDATE cost_entries SET vehicle_id = ?, date = ?, amount = ?, description = ?, mileage = ? WHERE id = ?"
            );
            $stmt->execute([
                $data['vehicle_id'],
                $data['date'],
                $data['amount'],
                $data['description'],
                $data['mileage'],
                $id
            ]);

            // Delete old refuel entry
            $this->db->prepare("DELETE FROM refuel_entries WHERE entry_id = ?")->execute([$id]);

            if (!empty($data['is_refuel'])) {
                $stmt = $this->db->prepare(
                    "INSERT INTO refuel_entries (entry_id, liters, price_per_liter) VALUES (?, ?, ?)"
                );
                $stmt->execute([$id, $data['liters'], $data['price_per_liter']]);
            }

            // Update categories (delete old, insert new)
            $this->db->prepare("DELETE FROM entry_category WHERE entry_id = ?")->execute([$id]);
            if (!empty($data['categories'])) {
                $stmt = $this->db->prepare("INSERT INTO entry_category (entry_id, category_id) VALUES (?, ?)");
                foreach ($data['categories'] as $categoryId) {
                    $stmt->execute([$id, $categoryId]);
                }
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("EntryModel::update Error: " . $e->getMessage());
            return false;
        }
    }

    public function softDelete(int $id, int $userId): bool
    {
        // Ensure the entry belongs to a vehicle of the user
        $stmt = $this->db->prepare(
            "UPDATE cost_entries e 
             JOIN vehicles v ON e.vehicle_id = v.id 
             SET e.deleted_at = NOW() 
             WHERE e.id = ? AND v.user_id = ?"
        );
        return $stmt->execute([$id, $userId]);
    }

    public function getMonthlyStats(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT v.name as vehicle_name, MONTH(e.date) as month, YEAR(e.date) as year, SUM(e.amount) as total_amount
             FROM cost_entries e
             JOIN vehicles v ON e.vehicle_id = v.id
             WHERE v.user_id = ? AND e.deleted_at IS NULL
             GROUP BY e.vehicle_id, year, month
             ORDER BY year DESC, month DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getFuelConsumptionStats(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT v.name as vehicle_name, 
                    SUM(r.liters) as total_liters,
                    MIN(e.mileage) as start_km,
                    MAX(e.mileage) as end_km,
                    (SUM(r.liters) / (MAX(e.mileage) - MIN(e.mileage)) * 100) as avg_consumption
             FROM cost_entries e
             JOIN vehicles v ON e.vehicle_id = v.id
             JOIN refuel_entries r ON e.id = r.entry_id
             WHERE v.user_id = ? AND e.deleted_at IS NULL
             GROUP BY e.vehicle_id
             HAVING (MAX(e.mileage) - MIN(e.mileage)) > 0"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
