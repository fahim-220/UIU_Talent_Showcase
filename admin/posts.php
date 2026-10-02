<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    
    if (!$post_id) {
        set_flash('error', 'Invalid post ID.');
        redirect('/admin/posts.php');
    }
    
    if ($action === 'delete') {
        $stmt = $pdo->prepare("SELECT file_path, cover_image FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);
        $post = $stmt->fetch();
        
        if ($post) {
            $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
            $stmt->execute([$post_id]);
            
            $upload_dir = realpath(__DIR__ . '/../uploads');
            if ($upload_dir) {
                foreach (['file_path', 'cover_image'] as $col) {
                    if ($post[$col] && strpos($post[$col], 'uploads/') === 0) {
                        $abs = realpath(__DIR__ . '/../' . $post[$col]);
                        if ($abs && strpos($abs, $upload_dir) === 0 && file_exists($abs)) {
                            unlink($abs);
                        }
                    }
                }
            }
            set_flash('success', 'Post deleted successfully.');
        } else {
            set_flash('error', 'Post not found.');
        }
    }
    
    redirect('/admin/posts.php');
}

$type_filter = $_GET['type'] ?? 'all';
$allowed_types = ['video', 'audio', 'text'];

if (in_array($type_filter, $allowed_types)) {
    $stmt = $pdo->prepare("
        SELECT p.id, p.title, p.type, p.created_at, u.name as author,
        (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes,
        (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments,
        pt.total_points
        FROM posts p JOIN users u ON p.user_id = u.id
        LEFT JOIN (SELECT post_id, SUM(points) as total_points FROM points WHERE post_id IS NOT NULL GROUP BY post_id) pt ON pt.post_id = p.id
        WHERE p.type = ?
        ORDER BY p.created_at DESC LIMIT 200
    ");
    $stmt->execute([$type_filter]);
} else {
    $stmt = $pdo->query("
        SELECT p.id, p.title, p.type, p.created_at, u.name as author,
        (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes,
        (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments,
        pt.total_points
        FROM posts p JOIN users u ON p.user_id = u.id
        LEFT JOIN (SELECT post_id, SUM(points) as total_points FROM points WHERE post_id IS NOT NULL GROUP BY post_id) pt ON pt.post_id = p.id
        ORDER BY p.created_at DESC LIMIT 200
    ");
}
$posts = $stmt->fetchAll();

// Get competition entries for these posts
$post_ids = array_column($posts, 'id');
$entries = [];
if ($post_ids) {
    $in = str_repeat('?,', count($post_ids) - 1) . '?';
    $stmt = $pdo->prepare("SELECT post_id, competition_id FROM competition_entries WHERE post_id IN ($in)");
    $stmt->execute($post_ids);
    foreach ($stmt->fetchAll() as $e) {
        $entries[$e['post_id']] = $e['competition_id'];
    }
}

// Fetch all competitions to populate dropdown dynamically
$comps = $pdo->query("SELECT id, title, category FROM competitions")->fetchAll();
$comps_json = htmlspecialchars(json_encode($comps), ENT_QUOTES, 'UTF-8');

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Posts - Admin</title>
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
        <h2>Manage Posts</h2>
        <form method="GET" action="posts.php" style="display: flex; gap: 10px;">
          <select name="type" class="form-input" style="padding: 6px 12px; width: auto;" onchange="this.form.submit()">
            <option value="all" <?php echo $type_filter === 'all' ? 'selected' : ''; ?>>All Types</option>
            <option value="video" <?php echo $type_filter === 'video' ? 'selected' : ''; ?>>Video</option>
            <option value="audio" <?php echo $type_filter === 'audio' ? 'selected' : ''; ?>>Audio</option>
            <option value="text" <?php echo $type_filter === 'text' ? 'selected' : ''; ?>>Text</option>
          </select>
        </form>
      </div>

      <div class="table-container">
        <table class="dash-table">
          <thead>
            <tr>
              <th>Title / Type</th>
              <th>Author / Date</th>
              <th>Engagement</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($posts as $p): 
              $pts = $p['total_points'] ? (int)$p['total_points'] : 0;
              $entered_comp = $entries[$p['id']] ?? '';
            ?>
              <tr>
                <td>
                  <strong><?php echo e($p['title']); ?></strong><br>
                  <small style="color: var(--text3);"><i class="fas fa-<?php echo $p['type'] === 'video' ? 'video' : ($p['type'] === 'audio' ? 'music' : 'align-left'); ?>"></i> <?php echo ucfirst(e($p['type'])); ?></small><br>
                  <span class="points-badge" id="badge-<?php echo $p['id']; ?>">★ <?php echo $pts; ?> pts</span>
                </td>
                <td>
                  <?php echo e($p['author']); ?><br>
                  <small style="color: var(--text3);"><?php echo date('M j, Y', strtotime($p['created_at'])); ?></small>
                </td>
                <td><small><?php echo $p['likes']; ?> Likes • <?php echo $p['comments']; ?> Comments</small></td>
                <td>
                  <a href="<?php echo BASE_URL . '/' . e($p['type']) . '.php#post-' . $p['id']; ?>" class="btn-primary" style="padding: 4px 8px; font-size: 12px; text-decoration: none; display: inline-block;">View</a>
                  
                  <button class="btn-primary btn-award" aria-label="Award points" style="padding: 4px 8px; font-size: 12px; background: transparent; color: var(--accent); border: 1px solid var(--accent); display: inline-block;"
                          data-post-id="<?php echo $p['id']; ?>"
                          data-post-title="<?php echo e($p['title']); ?>"
                          data-author-name="<?php echo e($p['author']); ?>"
                          data-post-type="<?php echo e($p['type']); ?>"
                          data-competition-id="<?php echo $entered_comp; ?>">
                    <i class="fas fa-star"></i>
                  </button>

                  <form method="POST" action="posts.php" class="delete-form" style="display: inline-block;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="post_id" value="<?php echo $p['id']; ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn-primary" style="padding: 4px 8px; font-size: 12px; background: transparent; color: #f87171; border: 1px solid #f87171;">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($posts)): ?>
              <tr><td colspan="4">No posts found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>

  <!-- Award Points Modal -->
  <div id="award-modal" class="modal-overlay hidden">
    <div class="modal-content" style="max-width: 500px;">
      <div class="modal-header">
        <h3>Award Points</h3>
        <button class="close-btn" onclick="closeModal('award-modal')"><i class="fas fa-times"></i></button>
      </div>
      <form id="award-form">
        <input type="hidden" name="post_id" id="award-post-id" value="">
        
        <div class="form-group">
          <label>Post</label>
          <input type="text" id="award-post-title" class="form-input" readonly style="background: var(--surface2); color: var(--text3);" />
        </div>
        
        <div class="form-group">
          <label>Author</label>
          <input type="text" id="award-post-author" class="form-input" readonly style="background: var(--surface2); color: var(--text3);" />
        </div>
        
        <div class="form-group">
          <label>Competition (Optional)</label>
          <select name="competition_id" id="award-competition-id" class="form-input">
            <option value="">None</option>
          </select>
        </div>
        
        <div class="form-group">
          <label>Points <span style="color: #f87171;">*</span></label>
          <input type="number" name="points" class="form-input" required min="-1000" max="1000" placeholder="e.g. 50 or -10" />
          <small style="color: var(--text3); display: block; margin-top: 5px;">Use a negative number to deduct points</small>
        </div>
        
        <div class="form-group">
          <label>Note <span style="color: #f87171;">*</span></label>
          <input type="text" name="note" id="award-note" class="form-input" required maxlength="255" />
        </div>
        
        <div style="display: flex; gap: 10px; margin-top: 20px;">
          <button type="submit" id="award-submit-btn" class="btn-primary" style="flex: 1;">Submit</button>
          <button type="button" class="btn-primary" onclick="closeModal('award-modal')" style="flex: 1; background: transparent; border: 1px solid var(--border); color: var(--text);">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <?php include __DIR__ . '/../includes/footer.php'; ?>
  <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
  <script>window.csrfToken = "<?php echo e(csrf_token()); ?>";</script>
  <script src="<?php echo BASE_URL; ?>/assets/js/admin.js"></script>
  <script>
    window.allComps = <?php echo $comps_json; ?>;
  </script>
</body>
</html>
