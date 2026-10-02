<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_admin();

// Live leaderboard
$stmt = $pdo->query("
    SELECT u.id, u.name, SUM(p.points) as total 
    FROM points p 
    JOIN users u ON p.user_id = u.id 
    GROUP BY u.id 
    HAVING total > 0 
    ORDER BY total DESC 
    LIMIT 10
");
$lb_raw = $stmt->fetchAll();

// Recent points history
$history = $pdo->query("
    SELECT p.points, p.note, p.created_at, u.name as user_name, c.title as comp_title, po.title as post_title 
    FROM points p 
    JOIN users u ON p.user_id = u.id 
    LEFT JOIN competitions c ON p.competition_id = c.id 
    LEFT JOIN posts po ON p.post_id = po.id
    ORDER BY p.created_at DESC 
    LIMIT 50
")->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Leaderboard & History - Admin</title>
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
        <h2>Leaderboard & History</h2>
      </div>

      <div class="lb-history-stack">
        
        <div class="dash-panel">
          <h3>Live Leaderboard (Top 10)</h3>
          <p style="color: var(--text3); font-size: 14px; margin-top: 5px;">Points are awarded from Manage Posts</p>
          <div class="table-container" style="margin-top: 15px; border: none;">
            <table class="dash-table" id="admin-leaderboard">
              <thead>
                <tr>
                  <th class="rank-col">Rank</th>
                  <th class="user-col">User</th>
                  <th class="points-col">Points</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $rank = 1;
                $prev = null;
                $pos = 1;
                foreach ($lb_raw as $lb): 
                  if ($prev !== null && $lb['total'] < $prev) {
                      $rank = $pos;
                  }
                ?>
                  <tr>
                    <td class="rank-col"><strong>#<?php echo $rank; ?></strong></td>
                    <td class="user-col"><?php echo e($lb['name']); ?></td>
                    <td class="points-col" style="color: var(--accent); font-weight: 600;"><?php echo $lb['total']; ?></td>
                  </tr>
                <?php 
                  $prev = $lb['total'];
                  $pos++;
                endforeach; ?>
                <?php if (empty($lb_raw)): ?>
                  <tr><td colspan="3">Leaderboard is empty.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        
        <div class="dash-panel">
          <h3>Recent History</h3>
          <div class="table-container" style="margin-top: 15px; border: none;">
            <table class="dash-table" id="points-history-table">
              <thead>
                <tr>
                  <th class="col-date">Date</th>
                  <th class="col-user">User</th>
                  <th class="col-post">Post</th>
                  <th class="col-comp">Competition</th>
                  <th class="col-points">Points</th>
                  <th class="col-note">Note</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($history as $h): ?>
                  <tr>
                    <td class="col-date"><?php echo date('M j, Y H:i', strtotime($h['created_at'])); ?></td>
                    <td class="col-user"><strong><?php echo e($h['user_name']); ?></strong></td>
                    <td class="col-post"><?php echo $h['post_title'] ? e($h['post_title']) : '-'; ?></td>
                    <td class="col-comp"><?php echo $h['comp_title'] ? e($h['comp_title']) : '-'; ?></td>
                    <td class="col-points" style="font-weight: 600; color: <?php echo $h['points'] > 0 ? 'var(--accent)' : '#f87171'; ?>;">
                      <?php echo $h['points'] > 0 ? '+' . $h['points'] : $h['points']; ?>
                    </td>
                    <td class="col-note"><?php echo e($h['note']); ?></td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($history)): ?>
                  <tr><td colspan="6">No points history found.</td></tr>
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
  <script>window.csrfToken = "<?php echo e(csrf_token()); ?>";</script>
  <script src="<?php echo BASE_URL; ?>/assets/js/admin.js"></script>
</body>
</html>
