<?php
require_once "database.php";
session_start();

// Omleiden indien al ingelogd
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['user_role'] ?? 'user';
    if ($role === 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $login_type = isset($_POST['login_type']) ? $_POST['login_type'] : 'user';

    if (empty($email) || empty($password)) {
        $error = 'Vul alle velden in.';;
    } else {
        try {
            if ($login_type === 'admin') {
                // Admin-aanmelding
                $sql = "SELECT * FROM users WHERE email = ? AND role = 'admin'";
            } else {
                // Gebruikersaanmelding
                $sql = "SELECT * FROM users WHERE email = ? AND role = 'user'";
            }
            
            $statement = $pdo->prepare($sql);
            $statement->execute([$email]);
            $user = $statement->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Aanmelding succesvol
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $error = 'Invalid email or password.';
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
    <title>Login — Fritandel</title>
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

        .login-container {
            background: var(--white);
            color: var(--black);
            border-radius: 12px;
            padding: 40px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2.5rem;
            margin: 0;
            color: var(--black);
        }

        .login-header p {
            font-family: 'Space Mono', monospace;
            color: var(--grey);
            margin: 10px 0 0 0;
        }

        .login-type-toggle {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            background: #f5f5f5;
            padding: 5px;
            border-radius: 8px;
        }

        .login-type-toggle input[type="radio"] {
            display: none;
        }

        .login-type-toggle label {
            flex: 1;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            border-radius: 6px;
            transition: 0.3s;
            font-family: 'Space Mono', monospace;
            font-size: 0.9rem;
            font-weight: bold;
            background: #f5f5f5;
            color: var(--grey);
        }

        .login-type-toggle input[type="radio"]:checked + label {
            background: var(--black);
            color: var(--yellow);
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

        .login-btn {
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

        .login-btn:hover {
            background: var(--yellow);
            color: var(--black);
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            font-family: 'Space Mono', monospace;
            font-size: 0.9rem;
        }

        .register-link a {
            color: var(--black);
            text-decoration: none;
            font-weight: bold;
            border-bottom: 2px solid var(--yellow);
            transition: 0.3s;
        }

        .register-link a:hover {
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
    <div class="login-container">
        <div class="login-header">
            <h1>FRITANDEL</h1>
            <p>The Perfect Fry</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="login-type-toggle">
                <input type="radio" id="user_login" name="login_type" value="user" checked>
                <label for="user_login">Customer</label>
                
                <input type="radio" id="admin_login" name="login_type" value="admin">
                <label for="admin_login">Admin</label>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="login-btn">Log In</button>
        </form>

        <div class="register-link">
            Don't have an account? <a href="register.php">Create one here</a>
        </div>

        <div class="back-home">
            <a href="index.php">← Back to Home</a>
        </div>
    </div>
</body>
</html>