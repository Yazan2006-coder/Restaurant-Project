<?php
require_once "database.php";
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';
$product = null;

if ($product_id <= 0) {
    $error = 'Invalid product ID';
} else {
    // Fetch the product to show confirmation
    try {
        $sql = "SELECT id, naam, beschrijving, afbeelding, categorie, prijs FROM Gerechten WHERE id = ?";
        $statement = $pdo->prepare($sql);
        $statement->execute([$product_id]);
        $product = $statement->fetch();
        
        if (!$product) {
            $error = 'Product not found';
        }
    } catch (PDOException $e) {
        $error = 'Er is iets misgegaan bij het ophalen van het product.';
    }
}

// Handle actual deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    if ($product_id > 0) {
        try {
            $sql = "DELETE FROM Gerechten WHERE id = ?";
            $statement = $pdo->prepare($sql);
            $statement->execute([$product_id]);
            header("Location: admin.php?deleted=1");
            exit();
        } catch (PDOException $e) {
            $error = 'Er is iets misgegaan bij het verwijderen van het product.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Product — Fritandel</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Space+Mono:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Style.css">
    <style>
        body {
            background: var(--parchment);
            color: var(--black);
            font-family: 'Space Mono', monospace;
        }

        .admin-header {
            background: var(--yellow);
            padding: 20px;
            border-bottom: 3px solid var(--black);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-header h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2rem;
            margin: 0;
            color: var(--black);
        }

        .admin-user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .admin-user-info p {
            margin: 0;
            color: var(--black);
            font-size: 0.95rem;
        }

        .logout-btn {
            padding: 10px 20px;
            background: var(--yellow);
            color: var(--black);
            border: none;
            border-radius: 8px;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
        }

        .logout-btn:hover {
            background: var(--white);
            transform: scale(1.05);
        }

        .delete-container {
            max-width: 500px;
            margin: 60px auto;
            padding: 40px;
            background: var(--yellow);
            border: 2px solid var(--black);
            border-radius: 12px;
        }

        .delete-container h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.8rem;
            color: #f44336;
            margin-top: 0;
            text-align: center;
        }

        .product-info {
            background: var(--parchment);
            padding: 20px;
            border: 1px solid var(--black);
            border-radius: 8px;
            margin: 20px 0;
        }

        .product-info h3 {
            margin: 0 0 10px 0;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.3rem;
        }

        .product-info p {
            margin: 5px 0;
            color: var(--grey);
        }

        .error-message {
            background: #f44336;
            color: var(--white);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .warning-text {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
            border: 1px solid #ffc107;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }

        .btn-cancel {
            background: var(--black);
            color: var(--yellow);
        }

        .btn-cancel:hover {
            background: var(--grey);
        }

        .btn-confirm {
            background: #f44336;
            color: white;
        }

        .btn-confirm:hover {
            background: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>DELETE PRODUCT</h1>
        <div class="admin-user-info">
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="delete-container">
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <a href="admin.php" class="btn btn-cancel" style="display: block; text-align: center;">Back to Admin</a>
        <?php elseif ($product): ?>
            <h2>⚠️ Delete Product?</h2>
            
            <div class="product-info">
                <h3><?php echo htmlspecialchars($product['naam']); ?></h3>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($product['categorie'] ?? 'N/A'); ?></p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($product['beschrijving'] ?? 'N/A'); ?></p>
                <p><strong>Price:</strong> €<?php echo number_format($product['prijs'], 2); ?></p>
            </div>

            <div class="warning-text">
                ⚠️ This action cannot be undone. The product will be permanently deleted.
            </div>

            <form method="POST" action="delete-product.php?id=<?php echo $product_id; ?>">
                <div class="button-group">
                    <a href="admin.php" class="btn btn-cancel">Cancel</a>
                    <button type="submit" name="confirm_delete" value="1" class="btn btn-confirm">Delete Product</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
