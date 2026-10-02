<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_admin();

$admin_id = current_user()['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    
    if (!$user_id) {
        set_flash('error', 'Invalid user ID.');
        redirect('/admin/users.php');
    }
    
    // Check role of target user
    $stmt = $pdo->prepare("SELECT id, role, avatar FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $target = $stmt->fetch();
    
    if (!$target) {
        set_flash('error', 'User not found.');
        redirect('/admin/users.php');
    }
    
    if ($target['role'] === 'admin') {
        set_flash('error', 'Cannot perform actions on an admin account.');
        redirect('/admin/users.php');
    }
    
    if ($target['id'] === $admin_id) {
        set_flash('error', 'Cannot perform actions on your own account.');
        redirect('/admin/users.php');
    }
    
    if ($action === 'toggle_block') {
        $new_status = $_POST['status'] === 'active' ? 'active' : 'blocked';
        $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $user_id]);
        set_flash('success', 'User status updated.');
    } elseif ($action === 'delete') {
        // Collect files to delete
        $stmt = $pdo->prepare("SELECT file_path, cover_image FROM posts WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $posts_files = $stmt->fetchAll();
        
        $files_to_delete = [];
        if ($target['avatar']) $files_to_delete[] = $target['avatar'];
        
        foreach ($posts_files as $p) {
            if ($p['file_path']) $files_to_delete[] = $p['file_path'];
            if ($p['cover_image']) $files_to_delete[] = $p['cover_image'];
        }
        
        // Delete user row (cascades)
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        
        // Delete physical files safely
        $upload_dir = realpath(__DIR__ . '/../uploads');
        if ($upload_dir) {
            foreach ($files_to_delete as $path) {
                if (strpos($path, 'uploads/') === 0) {
                    $abs = realpath(__DIR__ . '/../' . $path);
                    if ($abs && strpos($abs, $upload_dir) === 0 && file_exists($abs)) {
                        unlink($abs);
                    }
                }
            }
        }
        
        set_flash('success', 'User deleted successfully.');
    }
    
    redirect('/admin/users.php');
}

$q = trim($_GET['q'] ?? '');
if ($q) {
    $stmt = $pdo->prepare("
        SELECT u.id, u.name, u.email, u.department, u.role, u.status, u.created_at, 
        (SELECT COUNT(*) FROM posts WHERE user_id = u.id) as post_count
        FROM users u 
        WHERE u.name LIKE ? OR u.email LIKE ? 
        ORDER BY u.created_at DESC LIMIT 200
    ");
    $like = "%$q%";
    $stmt->execute([$like, $like]);
} else {
    $stmt = $pdo->query("
        SELECT u.id, u.name, u.email, u.department, u.role, u.status, u.created_at, 
        (SELECT COUNT(*) FROM posts WHERE user_id = u.id) as post_count
        FROM users u 
        ORDER BY u.created_at DESC LIMIT 200
    ");
}
$users = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Users - Admin</title>
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
      <?php
      $success = get_flash('success');
      $error = get_flash('error');
      if ($success): ?>
        <div class="toast type-success" style="position: static; margin-bottom: 20px; transform: none; opacity: 1; visibility: visible;"><?php echo e($success); ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="toast type-error" style="position: static; margin-bottom: 20px; transform: none; opacity: 1; visibility: visible;"><?php echo e($error); ?></div>
      <?php endif; ?>

      <div class="admin-header">
        <h2>Manage Users</h2>
        <form method="GET" action="users.php" style="display: flex; gap: 10px;">
          <input type="text" name="q" class="form-input" placeholder="Search name or email" value="<?php echo e($q); ?>" style="padding: 6px 12px; width: 250px;" />
          <button type="submit" class="btn-primary" style="padding: 6px 12px;">Search</button>
        </form>
      </div>

      <div class="table-container">
        <table class="dash-table">
          <thead>
            <tr>
              <th>User</th>
              <th>Dept. / Posts</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
              <tr>
                <td>
                  <strong><?php echo e($u['name']); ?></strong> 
                  <?php if ($u['role'] === 'admin'): ?>
                    <span class="hero-badge" style="padding: 2px 6px; font-size: 10px;">Admin</span>
                  <?php endif; ?><br>
                  <small style="color: var(--text3);"><?php echo e($u['email']); ?><br>Joined: <?php echo date('M j, Y', strtotime($u['created_at'])); ?></small>
                </td>
                <td>
                  <?php echo $u['department'] ? e($u['department']) : '-'; ?><br>
                  <small><?php echo $u['post_count']; ?> posts</small>
                </td>
                <td>
                  <?php if ($u['status'] === 'active'): ?>
                    <span style="color: var(--green);">Active</span>
                  <?php else: ?>
                    <span style="color: #f87171;">Blocked</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($u['role'] === 'admin'): ?>
                    <span style="color: var(--text3);">No actions</span>
                  <?php else: ?>
                    <form method="POST" action="users.php" style="display: inline-block;">
                      <input type="hidden" name="action" value="toggle_block">
                      <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                      <input type="hidden" name="status" value="<?php echo $u['status'] === 'active' ? 'blocked' : 'active'; ?>">
                      <?php echo csrf_field(); ?>
                      <button type="submit" class="btn-primary" style="padding: 4px 8px; font-size: 12px; background: transparent; color: <?php echo $u['status'] === 'active' ? '#f87171' : 'var(--green)'; ?>; border: 1px solid <?php echo $u['status'] === 'active' ? '#f87171' : 'var(--green)'; ?>;">
                        <?php echo $u['status'] === 'active' ? 'Block' : 'Unblock'; ?>
                      </button>
                    </form>
                    
                    <form method="POST" action="users.php" class="delete-form" style="display: inline-block;">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                      <?php echo csrf_field(); ?>
                      <button type="submit" class="btn-primary" style="padding: 4px 8px; font-size: 12px; background: transparent; color: #f87171; border: 1px solid #f87171;">Delete</button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($users)): ?>
              <tr><td colspan="4">No users found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>

  <?php include __DIR__ . '/../includes/footer.php'; ?>
  <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
  <script src="<?php echo BASE_URL; ?>/assets/js/admin.js"></script>
</body>
</html>
