<?php
// Database Configuration
define('DB_HOST', 'sql208.infinityfree.com');
define('DB_USER', 'if0_41962454');
define('DB_PASS', 'Nehachanney123');
define('DB_NAME', 'if0_41962454_nctraders_db');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
mysqli_query($conn, "SET time_zone = '+02:00'");
date_default_timezone_set('Africa/Johannesburg');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Helper function to escape strings
function escapeString($string) {
    global $conn;
    return $conn->real_escape_string($string);
}

function getTableColumns($table) {
    global $conn;
    static $columnsCache = [];

    if (isset($columnsCache[$table])) {
        return $columnsCache[$table];
    }

    $result = $conn->query("SHOW COLUMNS FROM `$table`");
    $columns = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
    }

    $columnsCache[$table] = $columns;
    return $columns;
}

function getColumnName($table, $preferred, $fallback) {
    $columns = getTableColumns($table);
    if (in_array($preferred, $columns, true)) {
        return $preferred;
    }
    return in_array($fallback, $columns, true) ? $fallback : $preferred;
}

function normalizeProductRow($row) {
    if (!is_array($row)) {
        return $row;
    }

    if (isset($row['description']) && !isset($row['product_description'])) {
        $row['product_description'] = $row['description'];
    }
    if (isset($row['price']) && !isset($row['product_price'])) {
        $row['product_price'] = $row['price'];
    }
    if (isset($row['image_url']) && !isset($row['product_image'])) {
        $row['product_image'] = $row['image_url'];
    }

    return $row;
}

function normalizeRow($row) {
    return normalizeProductRow($row);
}

// Helper function to execute query
function executeQuery($sql) {
    global $conn;
    $result = $conn->query($sql);
    if (!$result) {
        error_log("Database Error: " . $conn->error . " - SQL: " . $sql);
        return false;
    }
    return $result;
}

// Helper function to execute prepared statements
function executePreparedQuery($sql, $params = []) {
    global $conn;
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Database Prepare Error: " . $conn->error . " - SQL: " . $sql);
        return false;
    }

    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $bindParams = array_merge([$types], $params);
        $refs = [];
        foreach ($bindParams as $key => $value) {
            $refs[$key] = &$bindParams[$key];
        }
        call_user_func_array([$stmt, 'bind_param'], $refs);
    }

    if (!$stmt->execute()) {
        error_log("Database Execute Error: " . $stmt->error . " - SQL: " . $sql);
        return false;
    }

    return $stmt;
}

// Helper function to get single row
function getRow($sql) {
    $result = executeQuery($sql);
    if ($result && $result->num_rows > 0) {
        return normalizeRow($result->fetch_assoc());
    }
    return null;
}

// Helper function to get all rows
function getAllRows($sql) {
    $result = executeQuery($sql);
    $rows = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = normalizeRow($row);
        }
    }
    return $rows;
}

// Helper function to insert data
function insert($table, $data) {
    global $conn;
    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";

    $stmt = executePreparedQuery($sql, array_values($data));
    if ($stmt) {
        $insertId = $stmt->insert_id;
        $stmt->close();
        return $insertId;
    }
    return false;
}

// Helper function to update data
function update($table, $data, $where) {
    $set = [];
    $params = [];
    foreach ($data as $key => $value) {
        $set[] = "$key = ?";
        $params[] = $value;
    }
    $whereString = [];
    foreach ($where as $key => $value) {
        $whereString[] = "$key = ?";
        $params[] = $value;
    }

    $sql = "UPDATE $table SET " . implode(', ', $set) . " WHERE " . implode(' AND ', $whereString);
    $stmt = executePreparedQuery($sql, $params);
    if ($stmt) {
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        return $affectedRows !== false;
    }
    return false;
}

// Helper function to delete data
function delete($table, $where) {
    $params = [];
    $whereString = [];
    foreach ($where as $key => $value) {
        $whereString[] = "$key = ?";
        $params[] = $value;
    }
    $sql = "DELETE FROM $table WHERE " . implode(' AND ', $whereString);
    $stmt = executePreparedQuery($sql, $params);
    if ($stmt) {
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        return $affectedRows !== false;
    }
    return false;
}
?>
