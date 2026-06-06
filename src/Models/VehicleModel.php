<?php

namespace App\Models;

class VehicleModel extends Model
{
    public function getAllByUser(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM vehicles WHERE user_id = ? AND deleted_at IS NULL");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id, int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM vehicles WHERE id = ? AND user_id = ? AND deleted_at IS NULL");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO vehicles (user_id, name, license_plate, registration_date) VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['user_id'],
            $data['name'],
            $data['license_plate'],
            $data['registration_date']
        ]);
    }

    public function update(int $id, int $userId, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE vehicles SET name = ?, license_plate = ?, registration_date = ? WHERE id = ? AND user_id = ?"
        );
        return $stmt->execute([
            $data['name'],
            $data['license_plate'],
            $data['registration_date'],
            $id,
            $userId
        ]);
    }

    public function softDelete(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE vehicles SET deleted_at = NOW() WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }
}
