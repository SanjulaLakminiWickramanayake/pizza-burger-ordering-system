<?php
// Database Class for all database operations

class Database {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Execute SELECT query
     */
    public function select($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            return ['success' => false, 'message' => 'Query preparation failed: ' . $this->conn->error];
        }
        
        if (!empty($params)) {
            $types = '';
            $values = [];
            
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
                $values[] = $param;
            }
            
            array_unshift($values, $types);
            $refs = [];
            foreach ($values as $key => $value) {
                $refs[$key] = &$values[$key];
            }
            call_user_func_array([$stmt, 'bind_param'], $refs);
        }
        
        if (!$stmt->execute()) {
            return ['success' => false, 'message' => 'Query execution failed: ' . $stmt->error];
        }
        
        $result = $stmt->get_result();
        $data = [];
        
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        $stmt->close();
        return ['success' => true, 'data' => $data];
    }
    
    /**
     * Execute SELECT query with single result
     */
    public function selectOne($query, $params = []) {
        $result = $this->select($query, $params);
        if ($result['success'] && !empty($result['data'])) {
            return ['success' => true, 'data' => $result['data'][0]];
        }
        return ['success' => false, 'message' => 'No data found'];
    }
    
    /**
     * Execute INSERT query
     */
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            return ['success' => false, 'message' => 'Query preparation failed: ' . $this->conn->error];
        }
        
        $types = '';
        $values = [];
        
        foreach (array_values($data) as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
            $values[] = $value;
        }
        
        array_unshift($values, $types);
        $refs = [];
        foreach ($values as $key => $value) {
            $refs[$key] = &$values[$key];
        }
        
        call_user_func_array([$stmt, 'bind_param'], $refs);
        
        if (!$stmt->execute()) {
            return ['success' => false, 'message' => 'Query execution failed: ' . $stmt->error];
        }
        
        $insert_id = $this->conn->insert_id;
        $stmt->close();
        
        return ['success' => true, 'id' => $insert_id];
    }
    
    /**
     * Execute UPDATE query
     */
    public function update($table, $data, $where, $where_params = []) {
        $set_clause = implode(', ', array_map(function($key) { return "$key = ?"; }, array_keys($data)));
        $query = "UPDATE $table SET $set_clause WHERE $where";
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            return ['success' => false, 'message' => 'Query preparation failed: ' . $this->conn->error];
        }
        
        $types = '';
        $values = array_values($data);
        
        foreach ($values as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        
        foreach ($where_params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
            $values[] = $param;
        }
        
        array_unshift($values, $types);
        $refs = [];
        foreach ($values as $key => $value) {
            $refs[$key] = &$values[$key];
        }
        
        call_user_func_array([$stmt, 'bind_param'], $refs);
        
        if (!$stmt->execute()) {
            return ['success' => false, 'message' => 'Query execution failed: ' . $stmt->error];
        }
        
        $stmt->close();
        return ['success' => true, 'affected_rows' => $this->conn->affected_rows];
    }
    
    /**
     * Execute DELETE query
     */
    public function delete($table, $where, $where_params = []) {
        $query = "DELETE FROM $table WHERE $where";
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            return ['success' => false, 'message' => 'Query preparation failed: ' . $this->conn->error];
        }
        
        if (!empty($where_params)) {
            $types = '';
            $values = [];
            
            foreach ($where_params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
                $values[] = $param;
            }
            
            array_unshift($values, $types);
            $refs = [];
            foreach ($values as $key => $value) {
                $refs[$key] = &$values[$key];
            }
            
            call_user_func_array([$stmt, 'bind_param'], $refs);
        }
        
        if (!$stmt->execute()) {
            return ['success' => false, 'message' => 'Query execution failed: ' . $stmt->error];
        }
        
        $stmt->close();
        return ['success' => true, 'affected_rows' => $this->conn->affected_rows];
    }
    
    /**
     * Execute raw query
     */
    public function query($query) {
        $result = $this->conn->query($query);
        
        if (!$result) {
            return ['success' => false, 'message' => 'Query failed: ' . $this->conn->error];
        }
        
        if (is_object($result)) {
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            return ['success' => true, 'data' => $data];
        }
        
        return ['success' => true, 'affected_rows' => $this->conn->affected_rows];
    }
    
    /**
     * Get last insert ID
     */
    public function lastInsertId() {
        return $this->conn->insert_id;
    }
}
?>
