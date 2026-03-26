<?php
session_start();
require_once "database.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fritandel — The Only Fry You Need</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Space+Mono:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="Style.css">
</head>
<body>

<?php require "header-hero.php"; ?>

<!-- MAIN -->
<div class="page">
  <div>
    <?php require "menu.php"; ?>
  </div>

  <?php require "sidebar.php"; ?>
</div>

<?php require "footer-main.php"; ?>

</body>
</html>
