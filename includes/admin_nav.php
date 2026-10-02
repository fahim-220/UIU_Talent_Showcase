<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="admin-sidebar">
  <ul class="admin-sidebar-list">
    <li>
      <a href="<?php echo BASE_URL; ?>/admin/index.php" class="admin-nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
        <i class="fas fa-chart-line" style="width: 25px;"></i> Overview
      </a>
    </li>
    <li>
      <a href="<?php echo BASE_URL; ?>/admin/users.php" class="admin-nav-link <?php echo $current_page === 'users.php' ? 'active' : ''; ?>">
        <i class="fas fa-users" style="width: 25px;"></i> Users
      </a>
    </li>
    <li>
      <a href="<?php echo BASE_URL; ?>/admin/posts.php" class="admin-nav-link <?php echo $current_page === 'posts.php' ? 'active' : ''; ?>">
        <i class="fas fa-photo-video" style="width: 25px;"></i> Posts
      </a>
    </li>
    <li>
      <a href="<?php echo BASE_URL; ?>/admin/competitions.php" class="admin-nav-link <?php echo $current_page === 'competitions.php' ? 'active' : ''; ?>">
        <i class="fas fa-trophy" style="width: 25px;"></i> Competitions
      </a>
    </li>
    <li>
      <a href="<?php echo BASE_URL; ?>/admin/points.php" class="admin-nav-link <?php echo $current_page === 'points.php' ? 'active' : ''; ?>">
        <i class="fas fa-star" style="width: 25px;"></i> Leaderboard
      </a>
    </li>
    <li class="admin-nav-divider">
      <a href="<?php echo BASE_URL; ?>/index.php" class="admin-nav-link back-link">
        <i class="fas fa-arrow-left" style="width: 25px;"></i> Back to Site
      </a>
    </li>
  </ul>
</nav>
