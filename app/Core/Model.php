<?php
/**
 * PAKAINI - Base Model
 * Menyediakan abstraksi query PDO yang aman dari SQL Injection
 */

defined('ROOT_PATH') or define('ROOT_PATH', dirname(__DIR__, 2));
require_once ROOT_PATH . '/config/database.php';

abstract class Model {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Eksekusi query SELECT dan kembalikan semua baris
     */
    public function fetchAll(string $sql, array $params = []): array {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Eksekusi query SELECT dan kembalikan satu baris
     */
    public function fetch(string $sql, array $params = []): ?array {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }

    /**
     * Eksekusi query SELECT dan kembalikan nilai satu kolom (scalar)
     */
    public function fetchColumn(string $sql, array $params = []): mixed {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    /**
     * Eksekusi INSERT, UPDATE, atau DELETE
     */
    public function execute(string $sql, array $params = []): bool {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Ambil ID terakhir dari INSERT (Kompatibel dengan PostgreSQL sequence, MySQL & SQLite)
     */
    public function lastInsertId(?string $sequence = null): string {
        try {
            if ($sequence !== null && Database::getDriver() === 'pgsql') {
                $val = $this->db->lastInsertId($sequence);
                if ($val !== false && $val !== '0' && $val !== '') {
                    return (string)$val;
                }
            }
            $val = $this->db->lastInsertId();
            if ($val !== false && $val !== '0' && $val !== '') {
                return (string)$val;
            }
            if ($sequence !== null) {
                return (string)$this->db->lastInsertId($sequence);
            }
            return (string)$val;
        } catch (Exception $e) {
            if ($sequence !== null) {
                try {
                    return (string)$this->db->lastInsertId($sequence);
                } catch (Exception $e2) {}
            }
            return '0';
        }
    }

    /**
     * Mulai Database Transaction
     */
    public function beginTransaction(): bool {
        return $this->db->beginTransaction();
    }

    /**
     * Commit Database Transaction
     */
    public function commit(): bool {
        return $this->db->commit();
    }

    /**
     * Rollback Database Transaction
     */
    public function rollBack(): bool {
        if ($this->db->inTransaction()) {
            return $this->db->rollBack();
        }
        return false;
    }
}
