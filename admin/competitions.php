<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create' || $action === 'edit') {
        $comp_id = isset($_POST['competition_id']) ? (int)$_POST['competition_id'] : 0;
        $title = trim($_POST['title'] ?? '');
        $category = $_POST['category'] ?? '';
        $description = trim($_POST['description'] ?? '');
        $start_date = $_POST['start_date'] ? $_POST['start_date'] : null;
        $deadline = $_POST['deadline'] ?? '';
        $prize = trim($_POST['prize'] ?? '');
        $status = $_POST['status'] === 'closed' ? 'closed' : 'open';
        
        $allowed_cats = ['video', 'audio', 'text'];
        
        if (mb_strlen($title) < 1 || mb_strlen($title) > 150) {
            set_flash('error', 'Title is required (max 150 chars).');
        } elseif (!in_array($category, $allowed_cats)) {
            set_flash('error', 'Invalid category.');
        } elseif (mb_strlen($description) < 1) {
            set_flash('error', 'Description is required.');
        } elseif (!$deadline) {
            set_flash('error', 'Deadline is required.');
        } elseif ($start_date && $deadline < $start_date) {
            set_flash('error', 'Deadline cannot be before the start date.');
        } elseif (mb_strlen($prize) > 100) {
            set_flash('error', 'Prize cannot exceed 100 chars.');
        } else {
            if ($action === 'create') {
                $stmt = $pdo->prepare("INSERT INTO competitions (title, category, description, start_date, deadline, prize, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $category, $description, $start_date, $deadline, $prize, $status]);
                set_flash('success', 'Competition created successfully.');
            } else {
                $stmt = $pdo->prepare("UPDATE competitions SET title=?, category=?, description=?, start_date=?, deadline=?, prize=?, status=? WHERE id=?");
                $stmt->execute([$title, $category, $description, $start_date, $deadline, $prize, $status, $comp_id]);
                set_flash('success', 'Competition updated successfully.');
            }
        }
        redirect('/admin/competitions.php');
    }
    
    if ($action === 'toggle_status') {
        $comp_id = isset($_POST['competition_id']) ? (int)$_POST['competition_id'] : 0;
        $new_status = $_POST['status'] === 'closed' ? 'closed' : 'open';
        if ($comp_id) {
            $stmt = $pdo->prepare("UPDATE competitions SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $comp_id]);
            set_flash('success', 'Status toggled.');
        }
        redirect('/admin/competitions.php');
    }
    
    if ($action === 'delete') {
        $comp_id = isset($_POST['competition_id']) ? (int)$_POST['competition_id'] : 0;
        if ($comp_id) {
            $stmt = $pdo->prepare("DELETE FROM competitions WHERE id = ?");
            $stmt->execute([$comp_id]);
            set_flash('success', 'Competition deleted successfully.');
        }
        redirect('/admin/competitions.php');
    }
}

// Check if viewing entries
$view_id = isset($_GET['view_entries']) ? (int)$_GET['view_entries'] : 0;
$entries = [];
$view_comp = null;
if ($view_id) {
    $stmt = $pdo->prepare("SELECT title FROM competitions WHERE id = ?");
    $stmt->execute([$view_id]);
    $view_comp = $stmt->fetchColumn();
    
    if ($view_comp) {
        $stmt = $pdo->prepare("
            SELECT u.name, ce.created_at, p.title as post_title 
            FROM competition_entries ce 
            JOIN users u ON ce.user_id = u.id 
            LEFT JOIN posts p ON ce.post_id = p.id 
            WHERE ce.competition_id = ?
            ORDER BY ce.created_at DESC
        ");
        $stmt->execute([$view_id]);
        $entries = $stmt->fetchAll();
    }
}

// Fetch all competitions
$stmt = $pdo->query("
    SELECT c.*, 
    (SELECT COUNT(*) FROM competition_entries WHERE competition_id = c.id) as entry_count
    FROM competitions c 
    ORDER BY c.created_at DESC
");
$competitions = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Competitions - Admin</title>
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

      <?php if ($view_comp): ?>
        <div class="admin-header">
          <h2>Entries for: <?php echo e($view_comp); ?></h2>
          <a href="competitions.php" class="btn-primary" style="text-decoration: none; background: var(--surface2); color: var(--text);">Back to Competitions</a>
        </div>
        <div class="table-container">
          <table class="dash-table">
            <thead>
              <tr>
                <th>Entrant Name</th>
                <th>Joined Date</th>
                <th>Attached Post</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($entries as $e): ?>
                <tr>
                  <td><strong><?php echo e($e['name']); ?></strong></td>
                  <td><?php echo date('M j, Y', strtotime($e['created_at'])); ?></td>
                  <td><?php echo $e['post_title'] ? e($e['post_title']) : '-'; ?></td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($entries)): ?>
                <tr><td colspan="3">No entries yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="admin-header">
          <h2>Manage Competitions</h2>
          <button class="btn-primary" onclick="openCompModal()">+ New Competition</button>
        </div>

        <div class="table-container">
          <table class="dash-table">
            <thead>
              <tr>
                <th>Competition</th>
                <th>Dates / Prize</th>
                <th>Status</th>
                <th>Entries</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($competitions as $c): 
                $is_closed = ($c['status'] === 'closed' || $c['deadline'] < date('Y-m-d'));
                $comp_json = htmlspecialchars(json_encode([
                  'id' => $c['id'], 'title' => $c['title'], 'category' => $c['category'],
                  'description' => $c['description'], 'start_date' => $c['start_date'],
                  'deadline' => $c['deadline'], 'prize' => $c['prize'], 'status' => $c['status']
                ]), ENT_QUOTES, 'UTF-8');
              ?>
                <tr>
                  <td>
                    <strong><?php echo e($c['title']); ?></strong><br>
                    <small style="color: var(--text3);"><?php echo ucfirst(e($c['category'])); ?></small>
                  </td>
                  <td>
                    <small>Start: <?php echo $c['start_date'] ? date('M j', strtotime($c['start_date'])) : '-'; ?><br>End: <?php echo date('M j, Y', strtotime($c['deadline'])); ?><br>Prize: <?php echo $c['prize'] ? e($c['prize']) : '-'; ?></small>
                  </td>
                  <td>
                    <?php if ($is_closed): ?>
                      <span style="color: #f87171;">Closed</span>
                    <?php else: ?>
                      <span style="color: var(--green);">Open</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <a href="competitions.php?view_entries=<?php echo $c['id']; ?>" style="color: var(--accent); text-decoration: none; font-weight: 500;"><?php echo $c['entry_count']; ?> entries</a>
                  </td>
                  <td>
                    <button class="btn-primary" onclick="openCompModal(<?php echo $comp_json; ?>)" style="padding: 4px 8px; font-size: 12px; margin-bottom: 4px; display: block; width: 100%;">Edit</button>
                    
                    <form method="POST" action="competitions.php" style="display: block; margin-bottom: 4px;">
                      <input type="hidden" name="action" value="toggle_status">
                      <input type="hidden" name="competition_id" value="<?php echo $c['id']; ?>">
                      <input type="hidden" name="status" value="<?php echo $c['status'] === 'active' || $c['status'] === 'open' ? 'closed' : 'open'; ?>">
                      <?php echo csrf_field(); ?>
                      <button type="submit" class="btn-primary" style="padding: 4px 8px; font-size: 12px; background: transparent; color: var(--text2); border: 1px solid var(--border); width: 100%;">Toggle</button>
                    </form>
                    
                    <form method="POST" action="competitions.php" class="delete-form" style="display: block;">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="competition_id" value="<?php echo $c['id']; ?>">
                      <?php echo csrf_field(); ?>
                      <button type="submit" class="btn-primary" style="padding: 4px 8px; font-size: 12px; background: transparent; color: #f87171; border: 1px solid #f87171; width: 100%;">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($competitions)): ?>
                <tr><td colspan="5">No competitions found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <!-- Competition Modal -->
  <div id="comp-modal" class="modal-overlay hidden">
    <div class="modal-content" style="max-width: 500px;">
      <div class="modal-header">
        <h3 id="comp-modal-title">New Competition</h3>
        <button class="close-btn" onclick="closeModal('comp-modal')"><i class="fas fa-times"></i></button>
      </div>
      <form id="comp-form" action="competitions.php" method="POST">
        <input type="hidden" name="action" id="comp-action" value="create">
        <input type="hidden" name="competition_id" id="comp-id" value="0">
        <?php echo csrf_field(); ?>
        
        <div class="form-group">
          <label>Title <span style="color: #f87171;">*</span></label>
          <input type="text" name="title" id="comp-title-input" class="form-input" required maxlength="150" />
        </div>
        
        <div class="form-group">
          <label>Category <span style="color: #f87171;">*</span></label>
          <select name="category" id="comp-category-input" class="form-input" required>
            <option value="video">Video</option>
            <option value="audio">Audio</option>
            <option value="text">Text</option>
          </select>
        </div>
        
        <div class="form-group">
          <label>Description <span style="color: #f87171;">*</span></label>
          <textarea name="description" id="comp-desc-input" class="form-input" required rows="3"></textarea>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
          <div class="form-group">
            <label>Start Date</label>
            <input type="date" name="start_date" id="comp-start-input" class="form-input" />
          </div>
          <div class="form-group">
            <label>Deadline <span style="color: #f87171;">*</span></label>
            <input type="date" name="deadline" id="comp-deadline-input" class="form-input" required />
          </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
          <div class="form-group">
            <label>Prize</label>
            <input type="text" name="prize" id="comp-prize-input" class="form-input" maxlength="100" />
          </div>
          <div class="form-group">
            <label>Status <span style="color: #f87171;">*</span></label>
            <select name="status" id="comp-status-input" class="form-input" required>
              <option value="open">Open</option>
              <option value="closed">Closed</option>
            </select>
          </div>
        </div>
        
        <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Save Competition</button>
      </form>
    </div>
  </div>

  <?php include __DIR__ . '/../includes/footer.php'; ?>
  <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
  <script src="<?php echo BASE_URL; ?>/assets/js/admin.js"></script>
  <script>
    function openCompModal(comp = null) {
      if (comp) {
        document.getElementById('comp-modal-title').textContent = 'Edit Competition';
        document.getElementById('comp-action').value = 'edit';
        document.getElementById('comp-id').value = comp.id;
        document.getElementById('comp-title-input').value = comp.title;
        document.getElementById('comp-category-input').value = comp.category;
        document.getElementById('comp-desc-input').value = comp.description;
        document.getElementById('comp-start-input').value = comp.start_date || '';
        document.getElementById('comp-deadline-input').value = comp.deadline || '';
        document.getElementById('comp-prize-input').value = comp.prize || '';
        document.getElementById('comp-status-input').value = comp.status;
      } else {
        document.getElementById('comp-modal-title').textContent = 'New Competition';
        document.getElementById('comp-form').reset();
        document.getElementById('comp-action').value = 'create';
        document.getElementById('comp-id').value = '0';
      }
      openModal('comp-modal');
    }
  </script>
</body>
</html>
