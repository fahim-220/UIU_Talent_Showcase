<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_admin();

// Stats queries
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_posts = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$total_comps = $pdo->query("SELECT COUNT(*) FROM competitions")->fetchColumn();
$total_points = $pdo->query("SELECT COALESCE(SUM(points), 0) FROM points")->fetchColumn();

// Latest users
$latest_users = $pdo->query("SELECT name, email, created_at FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Latest posts
$latest_posts = $pdo->query("SELECT p.title, p.type, p.created_at, u.name as author FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT 5")->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Overview - UIU Talent Showcase</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css" />
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dashboard.css" />
</head>
<body>
  <?php include __DIR__ . '/../includes/header.php'; ?>

  <main class="admin-layout">
    <aside>
      <?php include __DIR__ . '/../includes/admin_nav.php'; ?>
    </aside>

    <div class="admin-content dash-section active" style="display: block;">
      <div class="admin-header">
        <h2>Dashboard Overview</h2>
      </div>

      <div class="stat-grid">
        <div class="stat-card">
          <i class="fas fa-users"></i>
          <h3><?php echo $total_users; ?></h3>
          <p>Total Users</p>
        </div>
        <div class="stat-card">
          <i class="fas fa-photo-video"></i>
          <h3><?php echo $total_posts; ?></h3>
          <p>Total Posts</p>
        </div>
        <div class="stat-card">
          <i class="fas fa-trophy"></i>
          <h3><?php echo $total_comps; ?></h3>
          <p>Competitions</p>
        </div>
        <div class="stat-card">
          <i class="fas fa-star"></i>
          <h3><?php echo $total_points; ?></h3>
          <p>Total Points Awarded</p>
        </div>
      </div>

      <div class="profile-grid" style="margin-top: 30px;">
        <div class="dash-panel">
          <h3>Recent Users</h3>
          <div class="table-container" style="margin-top: 15px; border: none;">
            <table class="dash-table">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Joined</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($latest_users as $u): ?>
                  <tr>
                    <td>
                      <strong><?php echo e($u['name']); ?></strong><br>
                      <small style="color: var(--text3);"><?php echo e($u['email']); ?></small>
                    </td>
                    <td><?php echo date('M j, Y', strtotime($u['created_at'])); ?></td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($latest_users)): ?>
                  <tr><td colspan="2">No users found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="dash-panel">
          <h3>Recent Posts</h3>
          <div class="table-container" style="margin-top: 15px; border: none;">
            <table class="dash-table">
              <thead>
                <tr>
                  <th>Post</th>
                  <th>Author</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($latest_posts as $p): ?>
                  <tr>
                    <td>
                      <strong><?php echo e($p['title']); ?></strong><br>
                      <small style="color: var(--text3);"><i class="fas fa-<?php echo $p['type'] === 'video' ? 'video' : ($p['type'] === 'audio' ? 'music' : 'align-left'); ?>"></i> <?php echo ucfirst(e($p['type'])); ?></small>
                    </td>
                    <td><?php echo e($p['author']); ?><br><small style="color: var(--text3);"><?php echo date('M j, Y', strtotime($p['created_at'])); ?></small></td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($latest_posts)): ?>
                  <tr><td colspan="2">No posts found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </main>

  <?php include __DIR__ . '/../includes/footer.php'; ?>
  <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
  <script src="<?php echo BASE_URL; ?>/assets/js/admin.js"></script>
</body>
</html>
