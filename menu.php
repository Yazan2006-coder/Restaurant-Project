<?php
// Menu Section - Fetch from database
$selectedCategory = $_GET['category'] ?? 'all';

if ($selectedCategory === 'all') {
    $sql = "SELECT id, naam, beschrijving, afbeelding, categorie, prijs FROM Gerechten ORDER BY naam ASC";
    $statement = $pdo->prepare($sql);
    $statement->execute();
} else {
    $sql = "SELECT id, naam, beschrijving, afbeelding, categorie, prijs FROM Gerechten WHERE categorie = ? ORDER BY naam ASC";
    $statement = $pdo->prepare($sql);
    $statement->execute([$selectedCategory]);
}
$menuItems = $statement->fetchAll();
?>

<div class="section-label">Menu</div>
<div class="menu-tabs">
  <a href="?category=all" class="tab-btn <?php echo $selectedCategory === 'all' ? 'active' : ''; ?>">All</a>
  <a href="?category=Signature" class="tab-btn <?php echo $selectedCategory === 'Signature' ? 'active' : ''; ?>">Signature</a>
  <a href="?category=Hot%20%26%20Spicy" class="tab-btn <?php echo $selectedCategory === 'Hot & Spicy' ? 'active' : ''; ?>">Hot & Spicy</a>
  <a href="?category=Premium" class="tab-btn <?php echo $selectedCategory === 'Premium' ? 'active' : ''; ?>">Premium</a>
</div>

<div class="grid-wrap">
  <div class="grid-header">
    <span class="grid-title">The Fritandel Menu</span>
    <span class="grid-count"><?php echo count($menuItems); ?> styles · same fry, different soul</span>
  </div>
  <div class="items-grid">
    <?php foreach ($menuItems as $item): ?>
      <div class="item-card">
        <div class="item-thumb">
          <?php 
            $imagePath = 'images/products/' . htmlspecialchars($item['afbeelding'] ?? 'placeholder.jpg');
            if (file_exists($imagePath) && !empty($item['afbeelding'])) {
              echo '<img src="' . $imagePath . '" alt="' . htmlspecialchars($item['naam']) . '" style="width: 100%; height: 100%; object-fit: cover;">';
            } else {
              echo '🍟';
            }
          ?>
        </div>
        <span class="item-tag"><?php echo htmlspecialchars($item['categorie'] ?? 'Dish'); ?></span>
        <div class="item-overlay">
          <h3><?php echo htmlspecialchars($item['naam']); ?></h3>
          <span class="item-detail"><?php echo htmlspecialchars($item['beschrijving']); ?> · €<?php echo number_format($item['prijs'], 2); ?></span>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>