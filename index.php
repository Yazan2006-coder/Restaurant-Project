<?php
session_start();
require_once "database.php";

// Haal de zoekopdracht op uit het formulier
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Bouw de SQL-query met zoekopdracht
$sql = "SELECT * FROM Gerechten";
if (!empty($searchQuery)) {
    $sql .= " WHERE naam LIKE ? OR categorie LIKE ? OR beschrijving LIKE ?";
}

// Bereid het statement voor en voer het uit
$statement = $pdo->prepare($sql);

if (!empty($searchQuery)) {
    $searchTerm = "%" . $searchQuery . "%";
    $statement->execute([$searchTerm, $searchTerm, $searchTerm]);
} else {
    $statement->execute();
}

// Haal alle gegevens op
$gerechten = $statement->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Dishes - Fritandel</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Space+Mono:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Style.css">
    <style>
        .dishes-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .search-box {
            margin-bottom: 40px;
            display: flex;
            gap: 10px;
        }
        
        .search-box input {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid var(--black);
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Space Mono', monospace;
        }
        
        .search-box button {
            padding: 12px 30px;
            background: var(--black);
            color: var(--yellow);
            border: 2px solid var(--black);
            border-radius: 8px;
            cursor: pointer;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1rem;
            font-weight: bold;
            transition: all 0.2s;
        }
        
        .search-box button:hover {
            background: var(--yellow);
            color: var(--black);
        }
        
        .clear-search {
            padding: 12px 20px;
            background: var(--warm-tan);
            color: var(--black);
            border: 2px solid var(--black);
            border-radius: 8px;
            cursor: pointer;
            font-family: 'Bebas Neue', sans-serif;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .clear-search:hover {
            background: var(--black);
            color: var(--yellow);
        }
        
        .dishes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
            margin-top: 40px;
        }
        
        .dish-card {
            background: var(--yellow);
            border: 2.5px solid var(--black);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 4px 6px 0 rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .dish-card:hover {
            transform: translateY(-4px);
            box-shadow: 6px 10px 0 rgba(0,0,0,0.2);
        }
        
        .dish-card h3 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.5rem;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
            color: var(--black);
        }
        
        .dish-category {
            display: inline-block;
            background: var(--black);
            color: var(--yellow);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
            letter-spacing: 0.1em;
            margin-bottom: 12px;
            text-transform: uppercase;
        }
        
        .dish-description {
            font-size: 0.95rem;
            line-height: 1.6;
            color: var(--black);
            margin-bottom: 16px;
            font-style: italic;
            opacity: 0.85;
        }
        
        .dish-price {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2rem;
            color: var(--black);
            margin-bottom: 12px;
        }
        
        .dish-image {
            font-size: 4rem;
            text-align: center;
            margin-bottom: 16px;
        }
        
        .no-results {
            text-align: center;
            padding: 40px;
            font-size: 1.2rem;
            color: var(--black);
        }
        
        .search-results-info {
            font-size: 1rem;
            color: var(--deep-brown);
            margin-bottom: 20px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <header>
        <a href="fritandel.php" class="logo">
            <div class="logo-text">
                <div class="name">Fritandel</div>
                <div class="tagline">The Perfect Fry</div>
            </div>
        </a>
        <nav>
            <a href="fritandel.php">Menu</a>
            <a href="index.php">Browse All</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <a href="admin.php" class="nav-link">Admin Panel</a>
                <?php endif; ?>
                <span style="color: var(--yellow); margin: 0 10px;">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="logout.php" class="nav-link">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-link">Login</a>
                <a href="register.php" class="nav-link">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="dishes-container">
        <h1>Our Dishes</h1>
        
        <!-- Search Form -->
        <form method="GET" class="search-box">
            <input 
                type="text" 
                name="search" 
                placeholder="Search by name, category, or description..." 
                value="<?php echo htmlspecialchars($searchQuery); ?>"
            >
            <button type="submit">Search</button>
            <?php if (!empty($searchQuery)): ?>
                <a href="index.php" class="clear-search">Clear</a>
            <?php endif; ?>
        </form>

        <!-- Search Results Info -->
        <?php if (!empty($searchQuery)): ?>
            <p class="search-results-info">
                Found <?php echo count($gerechten); ?> result(s) for "<strong><?php echo htmlspecialchars($searchQuery); ?></strong>"
            </p>
        <?php endif; ?>

        <!-- Dishes Display -->
        <?php if ($gerechten && count($gerechten) > 0): ?>
            <div class="dishes-grid">
                <?php foreach ($gerechten as $dish): ?>
                    <div class="dish-card">
                        <div class="dish-image">
                            <?php 
                              $imagePath = 'images/products/' . htmlspecialchars($dish['image'] ?? 'placeholder.jpg');
                              if (file_exists($imagePath) && !empty($dish['image'])) {
                                echo '<img src="' . $imagePath . '" alt="' . htmlspecialchars($dish['naam']) . '" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">';
                              } else {
                                echo '🍟';
                              }
                            ?>
                        </div>
                        <h3><?php echo htmlspecialchars($dish['naam']); ?></h3>
                        <span class="dish-category"><?php echo htmlspecialchars($dish['categorie']); ?></span>
                        <p class="dish-description"><?php echo htmlspecialchars($dish['beschrijving']); ?></p>
                        <div class="dish-price">€<?php echo number_format($dish['prijs'], 2, ',', '.'); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <?php if (!empty($searchQuery)): ?>
                    <p>No dishes found matching "<strong><?php echo htmlspecialchars($searchQuery); ?></strong>"</p>
                    <p><a href="index.php" class="clear-search" style="display: inline-block; margin-top: 20px;">Clear Search</a></p>
                <?php else: ?>
                    <p>No dishes available</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <span class="footer-logo">Fritandel ✦</span>
        <span class="footer-copy">© 2026 Fritandel Restaurant · Nijmegen, NL · One fry. Always.</span>
    </footer>
</body>
</html>