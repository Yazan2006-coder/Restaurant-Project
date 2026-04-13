<?php

$host = "db";
$db = "mydatabase";
$user = "user";
$password = "password";
$charset = "utf8mb4";

// PDO-opties voor foutafhandeling en gegevensopname
$opties = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// DSN (Data Source Name) voor databaseverbinding
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    // Maak databaseverbinding
    $pdo = new PDO ($dsn, $user, $password, $opties);
    
    // Maak gebruikerstabel als deze niet bestaat
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );");
    
    // Voeg afbeeldingskolom toe aan Gerechten-tabel als deze niet bestaat
    $result = $pdo->query("SHOW COLUMNS FROM Gerechten LIKE 'image'");
    if ($result->rowCount() === 0) {
        $pdo->exec("ALTER TABLE Gerechten ADD COLUMN image VARCHAR(255) DEFAULT 'placeholder.jpg' AFTER beschrijving");
    };
    
    // Controleer of admin-gebruiker bestaat, als er geen bestaat wordt er een gemaakt
    $stmt = $pdo->prepare("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        // admin-gebruiker inlog gegevens (email: admin@fritandel.com, wachtwoord: admin123)
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute(['Admin', 'admin@fritandel.com', $admin_password]);
    }
    
} catch (PDOException $e) {
    
    echo $e->getMessage();

    die("Sorry, databaseprobleem");
}
?>