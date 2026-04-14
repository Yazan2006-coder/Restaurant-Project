<?php

$host = "db";
$db = "mydatabase";
$user = "user";
$password = "password";
$charset = "utf8mb4";

//opties voor PDO
$opties = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

//dsn
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    //create connection
    $pdo = new PDO ($dsn, $user, $password, $opties);
    //success melding
    // echo "Verbinding succesvol!";
    
    // Create users table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );");
    
    // Add image column to Gerechten table if it doesn't exist
    $result = $pdo->query("SHOW COLUMNS FROM Gerechten LIKE 'afbeelding'");
    if ($result->rowCount() === 0) {
        $pdo->exec("ALTER TABLE Gerechten ADD COLUMN afbeelding VARCHAR(255) DEFAULT 'placeholder.jpg' AFTER beschrijving");
    };
    
    // Check if admin user exists, if not create one
    $stmt = $pdo->prepare("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        // Create default admin user (email: admin@fritandel.com, password: admin123)
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute(['Admin', 'admin@fritandel.com', $admin_password]);
    }
    
} catch (PDOException $e) {
    //foutmelding
    error_log($e->getMessage());
    die("Sorry, er is een probleem met de database.");
}
?>