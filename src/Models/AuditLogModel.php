<?php

namespace App\Models;

class AuditLogModel extends Model {
    public function log(?int $userId, string $action): bool {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $stmt = $this->db->prepare(
            "INSERT INTO audit_logs (user_id, ip_address, action) VALUES (?, ?, ?)"
        );
        return $stmt->execute([$userId, $ip, $action]);
    }

    public function getAll(): array {
        $stmt = $this->db->query(
            "SELECT a.*, u.email FROM audit_logs a LEFT JOIN users u ON a.user_id = u.id ORDER BY timestamp DESC"
        );
        return $stmt->fetchAll();
    }
}
