<?php
// Menu Section - Fetch from database
$sql = "SELECT * FROM Gerechten ORDER BY naam ASC";
$statement = $pdo->prepare($sql);
$statement->execute();
$menuItems = $statement->fetchAll();
?>

<div class="section-label">Menu</div>
<div class="menu-tabs">
  <button class="tab-btn active" type="button">All</button>
  <button class="tab-btn" type="button">Signature</button>
  <button class="tab-btn" type="button">Hot & Spicy</button>
  <button class="tab-btn" type="button">Premium</button>
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