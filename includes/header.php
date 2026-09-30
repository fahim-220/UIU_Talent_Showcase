<nav class="navbar">
    <div class="nav-brand">
      <span class="brand-icon">🎭</span>
      <span class="brand-text">UIU <span class="highlight">Talent Hunter</span></span>
    </div>
    <ul class="nav-links">
      <li><a href="<?php echo BASE_URL; ?>/index.php" class="nav-link">Home</a></li>
      <li class="nav-dropdown">
        <button class="nav-link dropdown-toggle" type="button" onclick="toggleTalentMenu(event)">Discover your talents <i class="fas fa-chevron-down"></i></button>
        <div class="dropdown-menu">
          <a href="<?php echo BASE_URL; ?>/video.php"><i class="fas fa-video"></i> Video</a>
          <a href="<?php echo BASE_URL; ?>/audio.php"><i class="fas fa-music"></i> Audio</a>
          <a href="<?php echo BASE_URL; ?>/blog.php"><i class="fas fa-align-left"></i> Text</a>
        </div>
      </li>
      <li><a href="<?php echo BASE_URL; ?>/how-it-works.php" class="nav-link">How It Works</a></li>
      <li><a href="<?php echo BASE_URL; ?>/achievers.php" class="nav-link">Talented Achievers</a></li>
      <li><a href="<?php echo BASE_URL; ?>/competitions.php" class="nav-link">Competitions</a></li>
    </ul>
    <div class="nav-actions">
      <?php if (is_logged_in()): ?>
        <button aria-label="Upload" class="btn-upload" onclick="openModal('upload-modal')"><i class="fas fa-plus"></i> Post</button>
        <?php if (is_admin()): ?>
          <a href="<?php echo BASE_URL; ?>/admin/index.php" class="btn-secondary" style="padding: 8px 16px; font-size: 0.9rem;">Admin</a>
        <?php endif; ?>
        <a href="<?php echo BASE_URL; ?>/dashboard.php" class="btn-secondary" style="padding: 8px 16px; font-size: 0.9rem;">Dashboard</a>
        <a href="<?php echo BASE_URL; ?>/logout.php" class="btn-secondary" style="padding: 8px 16px; font-size: 0.9rem;"><i class="fas fa-sign-out-alt"></i></a>
      <?php else: ?>
        <a href="<?php echo BASE_URL; ?>/login.php" class="btn-upload" style="text-decoration: none;"><i class="fas fa-plus"></i> Post</a>
        <a href="<?php echo BASE_URL; ?>/login.php" class="btn-secondary" style="padding: 8px 16px; font-size: 0.9rem;">Login</a>
        <a href="<?php echo BASE_URL; ?>/register.php" class="btn-primary" style="padding: 8px 16px; font-size: 0.9rem;">Register</a>
      <?php endif; ?>
    </div>
    <button class="hamburger" onclick="toggleMenu()" aria-label="Toggle menu"><i class="fas fa-bars"></i></button>
  </nav>