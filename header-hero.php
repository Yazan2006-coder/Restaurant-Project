<?php
// Header and Hero Section
?>

<!-- TICKER -->
<div class="ticker-wrap">
  <div class="ticker-inner">
    <span>One Fry. One Love</span>
    <span>Kitchen Opens 12pm Daily</span>
    <span>The Signature Cut — Made Fresh to Order</span>
    <span>Dine In or Take Away</span>
    <span>Crafted in Small Batches</span>
    <span>One Fry. One Love</span>
    <span>Kitchen Opens 12pm Daily</span>
    <span>The Signature Cut — Made Fresh to Order</span>
    <span>Dine In or Take Away</span>
    <span>Crafted in Small Batches</span>
  </div>
</div>

<!-- HEADER -->
<header>
  <a href="index.php" class="logo">
    <img src="logo.svg" alt="Fritandel" class="logo-icon">
    <div class="logo-text">
      <div class="name">Fritandel</div>
      <div class="tagline">The Perfect Fry</div>
    </div>
  </a>
  <nav>
    <a href="index.php">Menu</a>
    <a href="fritandel.php">Browse All</a>
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

<!-- HERO -->
<div class="hero">
  <div class="hero-text">
    <span class="hero-eyebrow">Craft Fried Potatoes</span>
    <h1 class="hero-title">One Fry. <em>Always.</em></h1>
    <p class="hero-desc">Hand-cut, small-batch fried potatoes. Every piece crafted with intention. No shortcuts. No compromises.</p>
    <div class="hero-price">
      <span class="currency">From</span>
      <span class="amount">€6.50</span>
    </div>
    <a href="#menu" class="hero-order">Start Your Order</a>
  </div>
  <div class="hero-visual">
    <img src="logo.jpg" alt="Fresh potatoes" class="hero-logo-img">
  </div>
</div>
