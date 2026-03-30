<?php
require_once "database.php";
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
    
    if (empty($name) || empty($email) || empty($password) || empty($password_confirm)) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $password_confirm) {
        $error = 'Passwords do not match.';
    } else {
        try {
            // Check if email already exists
            $sql = "SELECT id FROM users WHERE email = ?";
            $statement = $pdo->prepare($sql);
            $statement->execute([$email]);
            
            if ($statement->rowCount() > 0) {
                $error = 'This email is already registered.';
            } else {
                // Create new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')";
                $statement = $pdo->prepare($sql);
                $statement->execute([$name, $email, $hashed_password]);

                $success = 'Account created successfully! Please log in.';
                // Redirect after 2 seconds
                header("Refresh: 2; url=login.php");
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}