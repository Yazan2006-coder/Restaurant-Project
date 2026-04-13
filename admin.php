<?php
require_once "database.php";
session_start();

// Controleer of de gebruiker is ingelogd en admin-rechten heeft
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$success = '';
$error = '';

// Controleer of het product succesvol is verwijderd vanuit delete-product.php
if (isset($_GET['deleted']) && $_GET['deleted'] === '1') {
    $success = 'Product is succesvol verwijderd!';
}

// Haal alle producten op
try {
    $sql = "SELECT * FROM Gerechten ORDER BY naam ASC";
    $statement = $pdo->prepare($sql);
    $statement->execute();
    $products = $statement->fetchAll();
} catch (PDOException $e) {
    $error = 'Fout bij ophalen producten: ' . $e->getMessage();
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Fritandel</title>
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

        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .section-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2rem;
            color: var(--black);
            margin-bottom: 30px;
            border-bottom: 2px solid var(--black);
            padding-bottom: 15px;
        }

        .action-buttons {
            margin-bottom: 40px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .add-product-btn {
            padding: 12px 25px;
            background: var(--yellow);
            color: var(--black);
            border: none;
            border-radius: 8px;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .add-product-btn:hover {
            background: var(--white);
            transform: scale(1.05);
        }

        .success-message {
            background: #4caf50;
            color: var(--white);
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: bold;
        }

        .error-message {
            background: #f44336;
            color: var(--white);
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: bold;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .product-card {
            background: var(--yellow);
            border: 2px solid var(--black);
            border-radius: 12px;
            padding: 25px;
            transition: 0.3s;
        }

        .product-card:hover {
            border-color: var(--black);
            transform: translateY(-5px);
            box-shadow: 4px 6px 0 rgba(0,0,0,0.1);
        }

        .product-card h3 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.5rem;
            color: var(--black);
            margin: 0 0 10px 0;
        }

        .product-card p {
            color: var(--black);
            margin: 5px 0;
            font-size: 0.95rem;
        }

        .product-card .price {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.3rem;
            color: var(--black);
            margin: 15px 0;
            font-weight: bold;
        }

        .product-card .category {
            background: var(--black);
            color: var(--yellow);
            display: inline-block;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .product-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .edit-btn, .delete-btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 6px;
            font-family: 'Space Mono', monospace;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            text-align: center;
            font-size: 0.9rem;
        }

        .edit-btn {
            background: var(--yellow);
            color: var(--black);
        }

        .edit-btn:hover {
            background: var(--white);
        }

        .delete-btn {
            background: #f44336;
            color: var(--white);
        }

        .delete-btn:hover {
            background: #d32f2f;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--grey);
        }

        .empty-state p {
            font-size: 1.1rem;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>ADMIN DASHBOARD</h1>
        <div class="admin-user-info">
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="admin-container">
        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <h2 class="section-title">Product Management</h2>

        <div class="action-buttons">
            <a href="add-product.php" class="add-product-btn">+ Add New Product</a>
        </div>

        <?php if (count($products) > 0): ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div style="height: 150px; background: #f0f0f0; border-radius: 8px; margin-bottom: 15px; overflow: hidden; border: 1px solid var(--black);">
                            <?php 
                              $imagePath = 'images/products/' . htmlspecialchars($product['image'] ?? 'placeholder.jpg');
                              if (file_exists($imagePath) && !empty($product['image'])) {
                                echo '<img src="' . $imagePath . '" alt="' . htmlspecialchars($product['naam']) . '" style="width: 100%; height: 100%; object-fit: cover;">';
                              } else {
                                echo '<div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 3rem;">🍟</div>';
                              }
                            ?>
                        </div>
                        <h3><?php echo htmlspecialchars($product['naam']); ?></h3>
                        <span class="category"><?php echo htmlspecialchars($product['categorie'] ?? 'Dish'); ?></span>
                        <p><?php echo htmlspecialchars($product['beschrijving'] ?? 'No description'); ?></p>
                        <div class="price">€<?php echo number_format($product['prijs'], 2); ?></div>
                        <div class="product-actions">
                            <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="edit-btn">Edit</a>
                            <a href="delete-product.php?id=<?php echo $product['id']; ?>" class="delete-btn">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>No products found. Start by adding one!</p>
                <a href="add-product.php" class="add-product-btn">+ Add First Product</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
