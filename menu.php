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
