<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_login();

$user = current_user();
if ($user && $user['status'] === 'blocked') {
    session_destroy();
    redirect('/login.php');
}

// Handle forms
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    if (isset($_POST['action']) && $_POST['action'] === 'edit_profile') {
        $name = trim($_POST['name'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        
        if (mb_strlen($name) < 1 || mb_strlen($name) > 100) {
            set_flash('error', 'Name must be between 1 and 100 characters.');
            // Refill form data via flash if desired, but we can keep it simple as requested:
            // "Re-fill name, department and bio after an error (never passwords)"
            $_SESSION['form_data'] = ['name' => $name, 'department' => $department, 'bio' => $bio];
        } elseif (mb_strlen($department) > 100) {
            set_flash('error', 'Department cannot exceed 100 characters.');
            $_SESSION['form_data'] = ['name' => $name, 'department' => $department, 'bio' => $bio];
        } elseif (mb_strlen($bio) > 500) {
            set_flash('error', 'Bio cannot exceed 500 characters.');
            $_SESSION['form_data'] = ['name' => $name, 'department' => $department, 'bio' => $bio];
        } else {
            $avatar_path = $user['avatar'];
            
            // Handle avatar upload
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['avatar'];
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    set_flash('error', 'Avatar upload failed.');
                    redirect('/dashboard.php#profile');
                }
                
                if ($file['size'] > 2 * 1024 * 1024) {
                    set_flash('error', 'Avatar exceeds 2MB limit.');
                    redirect('/dashboard.php#profile');
                }
                
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($file['tmp_name']);
                $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];
                
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
                
                if (!in_array($mime, $allowed_mimes) || !in_array($ext, $allowed_exts)) {
                    set_flash('error', 'Invalid avatar format. Only JPG, PNG, WEBP are allowed.');
                    redirect('/dashboard.php#profile');
                }
                
                $new_filename = bin2hex(random_bytes(16)) . '.' . $ext;
                $dest_path = __DIR__ . '/uploads/images/' . $new_filename;
                
                if (move_uploaded_file($file['tmp_name'], $dest_path)) {
                    $new_avatar_path = 'uploads/images/' . $new_filename;
                    
                    // Delete old avatar
                    if ($avatar_path && strpos($avatar_path, 'uploads/images/') === 0) {
                        $old_abs = __DIR__ . '/' . $avatar_path;
                        if (file_exists($old_abs)) {
                            unlink($old_abs);
                        }
                    }
                    $avatar_path = $new_avatar_path;
                } else {
                    set_flash('error', 'Failed to save avatar.');
                    redirect('/dashboard.php#profile');
                }
            }
            
            $stmt = $pdo->prepare("UPDATE users SET name = ?, department = ?, bio = ?, avatar = ? WHERE id = ?");
            $stmt->execute([$name, $department, $bio, $avatar_path, $user['id']]);
            set_flash('success', 'Profile updated successfully.');
            unset($_SESSION['form_data']);
            redirect('/dashboard.php#profile');
        }
    }
    
    if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        
        if (!password_verify($current, $user['password'])) {
            set_flash('error', 'Current password is incorrect.');
        } elseif (mb_strlen($new) < 8) {
            set_flash('error', 'New password must be at least 8 characters long.');
        } elseif ($new !== $confirm) {
            set_flash('error', 'New passwords do not match.');
        } else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hash, $user['id']]);
            session_regenerate_id(true);
            set_flash('success', 'Password changed successfully.');
            redirect('/dashboard.php#profile');
        }
    }
}

// Pre-fill form data for error states
$form_data = $_SESSION['form_data'] ?? [
    'name' => $user['name'],
    'department' => $user['department'],
    'bio' => $user['bio']
];
unset($_SESSION['form_data']);

$user_id = $user['id'];

// Aggregate queries for Section 1
$stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_posts = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(l.id) FROM likes l JOIN posts p ON l.post_id = p.id WHERE p.user_id = ?");
$stmt->execute([$user_id]);
$total_likes = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(c.id) FROM comments c JOIN posts p ON c.post_id = p.id WHERE p.user_id = ?");
$stmt->execute([$user_id]);
$total_comments = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COALESCE(SUM(points), 0) FROM points WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_points = (int)$stmt->fetchColumn();

$rank_text = "Unranked";
if ($total_points > 0) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM (
            SELECT user_id, SUM(points) as total 
            FROM points 
            GROUP BY user_id 
            HAVING total > ?
        ) as sub
    ");
    $stmt->execute([$total_points]);
    $higher_users = (int)$stmt->fetchColumn();
    $rank_text = "#" . ($higher_users + 1);
}

// Section 2 - My Posts
$stmt = $pdo->prepare("SELECT id, title, type, created_at, 
    (SELECT COUNT(*) FROM likes WHERE post_id = posts.id) as like_count,
    (SELECT COUNT(*) FROM comments WHERE post_id = posts.id) as comment_count
    FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$my_posts = $stmt->fetchAll();

// Section 3 - My Competitions
$stmt = $pdo->prepare("
    SELECT c.title, c.category, c.deadline, c.status, p.title as post_title 
    FROM competition_entries ce 
    JOIN competitions c ON ce.competition_id = c.id 
    LEFT JOIN posts p ON ce.post_id = p.id 
    WHERE ce.user_id = ?
    ORDER BY c.deadline DESC
");
$stmt->execute([$user_id]);
$my_competitions = $stmt->fetchAll();

// Section 4 - My Points
$stmt = $pdo->prepare("
    SELECT p.points, p.note, p.created_at, c.title as comp_title 
    FROM points p 
    LEFT JOIN competitions c ON p.competition_id = c.id 
    WHERE p.user_id = ? 
    ORDER BY p.created_at DESC
");
$stmt->execute([$user_id]);
$my_points = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Dashboard - UIU Talent Showcase</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css" />
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dashboard.css" />
</head>
<body>
  <?php include 'includes/header.php'; ?>

  <main class="dash-layout">
    
    <?php
    $success = get_flash('success');
    $error = get_flash('error');
    if ($success): ?>
      <div class="toast type-success" style="position: static; margin-bottom: 20px; transform: none; opacity: 1; visibility: visible;"><?php echo e($success); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="toast type-error" style="position: static; margin-bottom: 20px; transform: none; opacity: 1; visibility: visible;"><?php echo e($error); ?></div>
    <?php endif; ?>

    <header class="page-heading" style="text-align: left; padding: 0;">
      <h1 style="font-size: 2rem;">My Dashboard</h1>
      <p style="color: var(--text2);">Manage your posts, competitions, and profile.</p>
    </header>

    <nav class="dash-nav">
      <a href="#overview" class="dash-tab active">Overview</a>
      <a href="#posts" class="dash-tab">My Posts</a>
      <a href="#competitions" class="dash-tab">My Competitions</a>
      <a href="#points" class="dash-tab">My Points</a>
      <a href="#profile" class="dash-tab">Profile</a>
    </nav>

    <!-- SECTION 1: OVERVIEW -->
    <section id="overview" class="dash-section active">
      <div class="stat-grid">
        <div class="stat-card">
          <i class="fas fa-file-video"></i>
          <h3><?php echo $total_posts; ?></h3>
          <p>Total Posts</p>
        </div>
        <div class="stat-card">
          <i class="fas fa-heart"></i>
          <h3><?php echo $total_likes; ?></h3>
          <p>Likes Received</p>
        </div>
        <div class="stat-card">
          <i class="fas fa-comment"></i>
          <h3><?php echo $total_comments; ?></h3>
          <p>Comments Received</p>
        </div>
        <div class="stat-card">
          <i class="fas fa-star"></i>
          <h3><?php echo $total_points; ?></h3>
          <p>Total Points</p>
        </div>
        <div class="stat-card">
          <i class="fas fa-trophy"></i>
          <h3><?php echo $rank_text; ?></h3>
          <p>Current Rank</p>
        </div>
      </div>
    </section>

    <!-- SECTION 2: MY POSTS -->
    <section id="posts" class="dash-section">
      <?php if (empty($my_posts)): ?>
        <div class="empty-state dash-panel">
          <p>You haven't posted anything yet.</p>
          <button class="btn-primary" style="margin-top: 15px;" onclick="openModal('upload-modal')">Upload a Post</button>
        </div>
      <?php else: ?>
        <div class="table-container">
          <table class="dash-table" id="my-posts-table">
            <thead>
              <tr>
                <th>Type</th>
                <th>Title</th>
                <th>Date</th>
                <th>Engagement</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($my_posts as $p): ?>
                <tr>
                  <td><span class="hero-badge" style="padding: 4px 8px; font-size: 12px;"><i class="fas fa-<?php echo $p['type'] === 'video' ? 'video' : ($p['type'] === 'audio' ? 'music' : 'align-left'); ?>"></i> <?php echo ucfirst(e($p['type'])); ?></span></td>
                  <td><strong><?php echo e($p['title']); ?></strong></td>
                  <td><?php echo date('M j, Y', strtotime($p['created_at'])); ?></td>
                  <td><small><?php echo $p['like_count']; ?> Likes • <?php echo $p['comment_count']; ?> Comments</small></td>
                  <td>
                    <a href="<?php echo BASE_URL . '/' . e($p['type']) . '.php#post-' . $p['id']; ?>" class="btn-primary" style="padding: 6px 12px; font-size: 13px; text-decoration: none;">View</a>
                    <button class="btn-primary delete-post-btn" data-id="<?php echo $p['id']; ?>" style="padding: 6px 12px; font-size: 13px; background: transparent; color: #f87171; border: 1px solid #f87171;"><i class="fas fa-trash"></i> Delete</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>

    <!-- SECTION 3: MY COMPETITIONS -->
    <section id="competitions" class="dash-section">
      <?php if (empty($my_competitions)): ?>
        <div class="empty-state dash-panel">
          <p>You haven't joined any competitions yet.</p>
          <a class="btn-primary" href="<?php echo BASE_URL; ?>/competitions.php" style="margin-top: 15px; display: inline-block; text-decoration: none;">Browse Competitions</a>
        </div>
      <?php else: ?>
        <div class="table-container">
          <table class="dash-table">
            <thead>
              <tr>
                <th>Competition Title</th>
                <th>Category</th>
                <th>Status</th>
                <th>Deadline</th>
                <th>Attached Post</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($my_competitions as $c): 
                $is_closed = ($c['status'] === 'closed' || $c['deadline'] < date('Y-m-d'));
              ?>
                <tr>
                  <td><strong><?php echo e($c['title']); ?></strong></td>
                  <td><?php echo ucfirst(e($c['category'])); ?></td>
                  <td>
                    <?php if ($is_closed): ?>
                      <span style="color: #f87171;">Closed</span>
                    <?php else: ?>
                      <span style="color: var(--green);">Open</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo date('M j, Y', strtotime($c['deadline'])); ?></td>
                  <td><?php echo $c['post_title'] ? e($c['post_title']) : '-'; ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>

    <!-- SECTION 4: MY POINTS -->
    <section id="points" class="dash-section">
      <div class="dash-panel" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0;">Total Points Balance</h3>
        <span style="font-size: 24px; font-weight: 700; color: var(--accent);"><?php echo $total_points; ?></span>
      </div>
      
      <?php if (empty($my_points)): ?>
        <div class="empty-state dash-panel">
          <p>You don't have any points yet.</p>
        </div>
      <?php else: ?>
        <div class="table-container">
          <table class="dash-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Competition</th>
                <th>Note</th>
                <th>Points</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($my_points as $pt): ?>
                <tr>
                  <td><?php echo date('M j, Y', strtotime($pt['created_at'])); ?></td>
                  <td><?php echo $pt['comp_title'] ? e($pt['comp_title']) : '-'; ?></td>
                  <td><?php echo e($pt['note']); ?></td>
                  <td style="color: var(--accent); font-weight: 600;">+<?php echo $pt['points']; ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>

    <!-- SECTION 5: PROFILE -->
    <section id="profile" class="dash-section">
      <div class="profile-grid">
        
        <!-- Edit Profile Form -->
        <div class="dash-panel">
          <h3>Edit Profile</h3>
          <form action="dashboard.php" method="POST" enctype="multipart/form-data" style="margin-top: 20px;">
            <input type="hidden" name="action" value="edit_profile">
            <?php echo csrf_field(); ?>
            
            <div class="form-group">
              <label>Email Address</label>
              <input type="email" class="form-input" value="<?php echo e($user['email']); ?>" readonly style="background: var(--surface2); color: var(--text3); cursor: not-allowed;" />
            </div>
            
            <div class="form-group">
              <label>Name <span style="color: #f87171;">*</span></label>
              <input type="text" name="name" class="form-input" value="<?php echo e($form_data['name']); ?>" required maxlength="100" />
            </div>
            
            <div class="form-group">
              <label>Department</label>
              <input type="text" name="department" class="form-input" value="<?php echo e($form_data['department']); ?>" maxlength="100" />
            </div>
            
            <div class="form-group">
              <label>Bio</label>
              <textarea name="bio" class="form-input" rows="4" maxlength="500"><?php echo e($form_data['bio']); ?></textarea>
            </div>
            
            <div class="form-group">
              <label>Avatar (Max 2MB)</label>
              <div class="avatar-preview-wrapper">
                <?php if ($user['avatar']): ?>
                  <img src="<?php echo e($user['avatar']); ?>" alt="Avatar" id="avatar-preview-img" />
                  <div class="default-avatar" id="avatar-preview-default" style="display: none;"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></div>
                <?php else: ?>
                  <div class="default-avatar" id="avatar-preview-default"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></div>
                <?php endif; ?>
                <input type="file" name="avatar" id="avatar-input" class="form-input" accept=".jpg,.jpeg,.png,.webp" style="padding: 10px;" />
              </div>
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%;">Save Profile</button>
          </form>
        </div>
        
        <!-- Change Password Form -->
        <div class="dash-panel">
          <h3>Change Password</h3>
          <form action="dashboard.php" method="POST" style="margin-top: 20px;">
            <input type="hidden" name="action" value="change_password">
            <?php echo csrf_field(); ?>
            
            <div class="form-group">
              <label>Current Password <span style="color: #f87171;">*</span></label>
              <input type="password" name="current_password" class="form-input" required />
            </div>
            
            <div class="form-group">
              <label>New Password (Min 8 chars) <span style="color: #f87171;">*</span></label>
              <input type="password" name="new_password" class="form-input" required minlength="8" />
            </div>
            
            <div class="form-group">
              <label>Confirm New Password <span style="color: #f87171;">*</span></label>
              <input type="password" name="confirm_password" class="form-input" required minlength="8" />
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Change Password</button>
          </form>
        </div>
        
      </div>
    </section>

  </main>

  <?php include 'includes/footer.php'; ?>
  <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
  <script>
    // Make CSRF token available to dashboard.js fetch calls
    window.csrfToken = "<?php echo e(csrf_token()); ?>";
  </script>
  <script src="<?php echo BASE_URL; ?>/assets/js/dashboard.js"></script>
</body>
</html>
