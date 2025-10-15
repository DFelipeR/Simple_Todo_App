<?php
// config.php - Database Configuration File
// This file contains database connection settings

// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');  // Default XAMPP username
define('DB_PASSWORD', '');      // Default XAMPP password (empty)
define('DB_DATABASE', 'simple_todo');

// Attempt to connect to MySQL database
function getDBConnection() {
    try {
        $connection = new PDO(
            "mysql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE,
            DB_USERNAME,
            DB_PASSWORD
        );
        
        // Set the PDO error mode to exception
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $connection;
    } catch(PDOException $e) {
        die("❌ Connection failed: " . $e->getMessage());
    }
}

// Test the connection
function testConnection() {
    try {
        $connection = getDBConnection();
        echo "✅ Database connection successful!<br>";
        echo "📊 Connected to database: " . DB_DATABASE . "<br>";
        return true;
    } catch(Exception $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
        return false;
    }
}
?>