<?php
require_once "database.php";
session_start();

// Controleert of de gebruiker admin is
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam = isset($_POST['naam']) ? trim($_POST['naam']) : '';
    $beschrijving = isset($_POST['beschrijving']) ? trim($_POST['beschrijving']) : '';
    $categorie = isset($_POST['categorie']) ? trim($_POST['categorie']) : '';
    $prijs = isset($_POST['prijs']) ? floatval($_POST['prijs']) : 0;
    $image = '';

    // Verwerk de afbeeldingsupload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'images/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = basename($_FILES['image']['name']);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array(strtolower($fileExt), $allowedExt)) {
            $newFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $naam) . '.' . $fileExt;
            $uploadPath = $uploadDir . $newFileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $image = $newFileName;
            } else {
                $error = 'Afbeelding kan niet worden geüpload.';
            }
        } else {
            $error = 'Ongeldig afbeeldingsformaat. Ondersteund: jpg, jpeg, png, gif, webp';
        }
    }

    if (empty($naam) || empty($beschrijving) || empty($categorie) || $prijs <= 0) {
        $error = 'Vul alle productgegevens in met geldige gegevens.';;
    } elseif (empty($error)) {
        try {
            $sql = "INSERT INTO Gerechten (naam, beschrijving, image, categorie, prijs) VALUES (?, ?, ?, ?, ?)";
            $statement = $pdo->prepare($sql);
            $statement->execute([$naam, $beschrijving, $image ?: 'placeholder.jpg', $categorie, $prijs]);
            
            $success = 'Product is succesvol toegevoegd!';
            // Redirect na 2 seconden
            header("Refresh: 2; url=admin.php");
        } catch (PDOException $e) {
            $error = 'Error adding product: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product — Fritandel Admin</title>
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
        }

        .admin-container {
            max-width: 600px;
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

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--black);
            font-size: 1rem;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid var(--black);
            border-radius: 8px;
            font-family: 'Space Mono', monospace;
            font-size: 1rem;
            background: var(--white);
            color: var(--black);
            box-sizing: border-box;
            transition: 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--yellow);
            box-shadow: 0 0 10px rgba(255, 222, 0, 0.3);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
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

        .form-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .save-btn, .cancel-btn {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.1rem;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            text-align: center;
        }

        .save-btn {
            background: var(--yellow);
            color: var(--black);
        }

        .save-btn:hover {
            background: var(--white);
        }

        .cancel-btn {
            background: var(--grey);
            color: var(--black);
        }

        .cancel-btn:hover {
            background: var(--white);
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>ADD NEW PRODUCT</h1>
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

        <form method="POST" action="add-product.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="naam">Product Name *</label>
                <input type="text" id="naam" name="naam" required autofocus>
            </div>

            <div class="form-group">
                <label for="beschrijving">Description *</label>
                <textarea id="beschrijving" name="beschrijving" required></textarea>
            </div>

            <div class="form-group">
                <label for="image">Product Image</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                <small style="color: var(--grey); display: block; margin-top: 5px;">Allowed: JPG, PNG, GIF, WebP (Optional)</small>
            </div>

            <div class="form-group">
                <label for="categorie">Category *</label>
                <select id="categorie" name="categorie" required>
                    <option value="">-- Select a Category --</option>
                    <option value="Signature">Signature</option>
                    <option value="Hot & Spicy">Hot & Spicy</option>
                    <option value="Premium">Premium</option>
                    <option value="Vegan">Vegan</option>
                    <option value="Classic">Classic</option>
                    <option value="New">New</option>
                </select>
            </div>

            <div class="form-group">
                <label for="prijs">Price (€) *</label>
                <input type="number" id="prijs" name="prijs" step="0.01" min="0.01" required>
            </div>

            <div class="form-buttons">
                <button type="submit" class="save-btn">Save Product</button>
                <a href="admin.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
