<?php

class CRUD
{
    private $db;

    public function __construct()
    {
        $this->db = DB::connect();
    }

    public function insert(string $table, array $data)
    {
        if (empty($data)) {
            return false;
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";

        try {
            $stmt = $this->db->prepare($sql);
            foreach ($data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("CRUD Insert Error: " . $e->getMessage());
            return false;
        }
    }

    public function select(string $table, array $conditions = [], array $columns = ['*'], string $orderBy = '', string $limit = '', bool $singleResult = false)
    {
        $cols = implode(', ', $columns);
        $sql = "SELECT {$cols} FROM {$table}";
        $params = [];

        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $key => $value) {
                $whereClause[] = "{$key} = :{$key}";
                $params[':' . $key] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }

        if (!empty($orderBy)) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if (!empty($limit)) {
            $sql .= " LIMIT {$limit}";
        }

        try {
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();

            if ($singleResult) {
                return $stmt->fetch(PDO::FETCH_OBJ);
            } else {
                return $stmt->fetchAll(PDO::FETCH_OBJ);
            }
        } catch (PDOException $e) {
            error_log("CRUD Select Error: " . $e->getMessage());
            return false;
        }
    }

    public function update(string $table, array $data, array $conditions)
    {
        if (empty($data) || empty($conditions)) {
            return false;
        }

        $setClause = [];
        $params = [];
        foreach ($data as $key => $value) {
            $setClause[] = "{$key} = :{$key}";
            $params[':' . $key] = $value;
        }

        $whereClause = [];
        foreach ($conditions as $key => $value) {
            $whereClause[] = "{$key} = :where_" . $key;
            $params[':where_' . $key] = $value;
        }

        $sql = "UPDATE {$table} SET " . implode(', ', $setClause) . " WHERE " . implode(' AND ', $whereClause);

        try {
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("CRUD Update Error: " . $e->getMessage());
            return false;
        }
    }

    public function delete(string $table, array $conditions)
    {
        if (empty($conditions)) {
            return false;
        }

        $whereClause = [];
        $params = [];
        foreach ($conditions as $key => $value) {
            $whereClause[] = "{$key} = :{$key}";
            $params[':' . $key] = $value;
        }

        $sql = "DELETE FROM {$table} WHERE " . implode(' AND ', $whereClause);

        try {
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("CRUD Delete Error: " . $e->getMessage());
            return false;
        }
    }

    public function query(string $sql, array $params = [])
    {
        try {
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            error_log("CRUD Query Error: " . $e->getMessage());
            return false;
        }
    }
}
