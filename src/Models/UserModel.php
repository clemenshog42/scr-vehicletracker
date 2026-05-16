<?php

namespace App\Models;

class UserModel extends Model {
    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO users (firstname, lastname, email, password_hash, role) VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['firstname'],
            $data['lastname'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'] ?? 'registered'
        ]);
    }

    public function updateStatus(int $userId, string $status): bool {
        $stmt = $this->db->prepare("UPDATE users SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $userId]);
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT id, firstname, lastname, email, role, status, created_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
}
