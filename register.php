<?php
require_once "database.php";
session_start();

// Omleiden indien al ingelogd
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
        $error = 'Vul alle velden in.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Voer een geldig e-mailadres in.';
    } elseif (strlen($password) < 6) {
        $error = 'Wachtwoord moet minstens 6 tekens lang zijn.';
    } elseif ($password !== $password_confirm) {
        $error = 'Wachtwoorden komen niet overeen.';
    } else {
        try {
            // Controleer of e-mail al bestaat
            $sql = "SELECT id FROM users WHERE email = ?";
            $statement = $pdo->prepare($sql);
            $statement->execute([$email]);
            
            if ($statement->rowCount() > 0) {
                $error = 'Dit e-mailadres is al geregistreerd.';
            } else {
                // Maak nieuwe gebruiker aan
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')";
                $statement = $pdo->prepare($sql);
                $statement->execute([$name, $email, $hashed_password]);

                $success = 'Account succesvol aangemaakt! Meld u aan.';
                // Redirect na 2 seconden
                header("Refresh: 2; url=login.php");
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Fritandel</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Space+Mono:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Style.css">
    <style>
        body {
            background: var(--parchment);
            color: var(--black);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .register-container {
            background: var(--white);
            color: var(--black);
            border-radius: 12px;
            padding: 40px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2.5rem;
            margin: 0;
            color: var(--black);
        }

        .register-header p {
            font-family: 'Space Mono', monospace;
            color: var(--grey);
            margin: 10px 0 0 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-family: 'Space Mono', monospace;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--black);
            border-radius: 8px;
            font-family: 'Space Mono', monospace;
            font-size: 1rem;
            box-sizing: border-box;
            transition: 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--yellow);
            background: #fffef0;
        }

        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-family: 'Space Mono', monospace;
            font-size: 0.9rem;
            text-align: center;
        }

        .success-message {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-family: 'Space Mono', monospace;
            font-size: 0.9rem;
            text-align: center;
        }

        .register-btn {
            width: 100%;
            padding: 14px;
            background: var(--black);
            color: var(--yellow);
            border: none;
            border-radius: 8px;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .register-btn:hover {
            background: var(--yellow);
            color: var(--black);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            font-family: 'Space Mono', monospace;
            font-size: 0.9rem;
        }

        .login-link a {
            color: var(--black);
            text-decoration: none;
            font-weight: bold;
            border-bottom: 2px solid var(--yellow);
            transition: 0.3s;
        }

        .login-link a:hover {
            color: var(--yellow);
        }

        .back-home {
            text-align: center;
            margin-top: 15px;
        }

        .back-home a {
            color: var(--grey);
            text-decoration: none;
            font-family: 'Space Mono', monospace;
            font-size: 0.85rem;
            transition: 0.3s;
        }

        .back-home a:hover {
            color: var(--black);
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>FRITANDEL</h1>
            <p>Create Your Account</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required autofocus>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirm Password</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>

            <button type="submit" class="register-btn">Create Account</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Log in here</a>
        </div>

        <div class="back-home">
            <a href="index.php">← Back to Home</a>
        </div>
    </div>
</body>
</html>