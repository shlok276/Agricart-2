<?php

// Database connection configuration for Agricart
// LOCAL DEVELOPMENT: Using XAMPP MySQL (127_0_0_1.sql imported)
// For Vercel/Supabase deployment, set environment variables accordingly

$db_host = getenv('DB_HOST') ?: '127.0.0.1';
$db_port = getenv('DB_PORT') ?: '3306';
$db_name = getenv('DB_NAME') ?: 'agricart';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: '';
$db_type = getenv('DB_TYPE') ?: 'mysql'; // Local XAMPP uses MySQL

try {
    if ($db_type === 'pgsql') {
        $dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name";
    } else {
        $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name";
    }
    
    $conn = new PDO($dsn, $db_user, $db_pass);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>
