<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli(
    'sql208.infinityfree.com',
    'if0_41962454',
    'Nehachanney123',
    'if0_41962454_nctraders_db'
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "DATABASE CONNECTED";