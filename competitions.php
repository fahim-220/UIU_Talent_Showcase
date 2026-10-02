<?php
require_once 'includes/auth.php';

$images = [
    'video' => 'https://images.unsplash.com/photo-1504609813442-a8924e83f76e?w=640&h=240&fit=crop&auto=format',
    'audio' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=640&h=240&fit=crop&auto=format',
    'text' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?w=640&h=240&fit=crop&auto=format',
];

$stmt = $pdo->prepare("
    SELECT c.*, COUNT(ce.id) as entry_count 
    FROM competitions c
    LEFT JOIN competition_entries ce ON c.id = ce.competition_id
    GROUP BY c.id
    ORDER BY 
        CASE WHEN c.status = 'closed' OR c.deadline < CURDATE() THEN 1 ELSE 0 END ASC,
        c.deadline ASC
");
$stmt->execute();
$competitions = $stmt->fetchAll();

$user_competitions = [];
$user_posts = [];
if (is_logged_in()) {
    $c_stmt = $pdo->prepare("SELECT competition_id FROM competition_entries WHERE user_id = ?");
    $c_stmt->execute([$_SESSION['user_id']]);
    $user_competitions = $c_stmt->fetchAll(PDO::FETCH_COLUMN);

    $p_stmt = $pdo->prepare("SELECT id, title, type FROM posts WHERE user_id = ?");
    $p_stmt->execute([$_SESSION['user_id']]);
    $user_posts = $p_stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Join competitions and showcase your talent to the UIU community." />
  <title>Competitions | UIU Talent Hunter</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css" />
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/pages/competitions.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="category-page competitions-page">
  <?php include "includes/header.php"; ?>
  <main class="competition-page-main">
    <header class="page-heading">
      <p class="hero-badge"><i class="fas fa-trophy"></i> Find your arena</p>
      <h1>Competitions</h1>
      <p>Enter the arena. Prove your talent to the world.</p>
    </header>
    <div class="competition-filter">
      <button class="filter-btn active" data-filter="all"><i class="fas fa-border-all"></i> All</button>
      <button class="filter-btn" data-filter="video"><i class="fas fa-video"></i> Video</button>
      <button class="filter-btn" data-filter="audio"><i class="fas fa-music"></i> Audio</button>
      <button class="filter-btn" data-filter="text"><i class="fas fa-align-left"></i> Text</button>
    </div>
    
    <section class="competition-list" id="competition-list">
      <?php if (count($competitions) === 0): ?>
        <p style="text-align: center; color: var(--text3); width: 100%; padding: 40px 0;">No competitions available at the moment.</p>
      <?php else: ?>
        <?php foreach ($competitions as $comp): 
            $is_closed = ($comp['status'] === 'closed' || $comp['deadline'] < date('Y-m-d'));
            $is_joined = in_array($comp['id'], $user_competitions);
        ?>
          <article class="full-competition-card comp-item" data-category="<?php echo e($comp['category']); ?>">
            <img src="<?php echo e($images[$comp['category']]); ?>" alt="Competition Image" />
            <div class="full-competition-body">
              <span class="competition-type"><?php echo ucfirst(e($comp['category'])); ?> Competition</span>
              <h2><?php echo e($comp['title']); ?></h2>
              <p><?php echo nl2br(e($comp['description'])); ?></p>
              <div class="details-grid">
                <div><b><?php echo $comp['start_date'] ? date('M j, Y', strtotime($comp['start_date'])) : '-'; ?></b><span>Start Date</span></div>
                <div><b><?php echo date('M j, Y', strtotime($comp['deadline'])); ?></b><span>Deadline</span></div>
                <div><b><?php echo $comp['prize'] ? e($comp['prize']) : '-'; ?></b><span>Prize</span></div>
                <div><b class="entry-count-text"><?php echo $comp['entry_count']; ?></b><span>Entries</span></div>
              </div>
              <?php if ($is_closed): ?>
                <button class="btn-primary" disabled style="background: var(--surface2); color: var(--text3); border: 1px solid var(--border);">Closed</button>
              <?php elseif ($is_joined): ?>
                <button class="btn-primary" disabled style="background: var(--surface2); color: var(--accent); border: 1px solid var(--accent);"><i class="fas fa-check"></i> Registered</button>
              <?php else: ?>
                <?php if (is_logged_in()): ?>
                  <button class="btn-primary" onclick="openJoinModal(<?php echo $comp['id']; ?>, '<?php echo e($comp['category']); ?>', '<?php echo e($comp['title']); ?>')">Register Now</button>
                <?php else: ?>
                  <a class="btn-primary" href="<?php echo BASE_URL; ?>/login.php" style="text-decoration: none;">Register Now</a>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
      <p id="empty-filter-msg" class="hidden" style="text-align: center; color: var(--text3); width: 100%; padding: 40px 0;">No competitions in this category.</p>
    </section>
  </main>
  
  <?php if (is_logged_in()): ?>
  <!-- ========== JOIN MODAL ========== -->
  <div class="modal-overlay hidden" id="join-modal">
    <div class="modal">
      <div class="modal-header">
        <h3><i class="fas fa-trophy"></i> Join Competition</h3>
        <button aria-label="Close" class="modal-close" onclick="closeModal('join-modal')">&times;</button>
      </div>
      <div class="modal-body">
        <form id="join-form">
          <input type="hidden" name="competition_id" id="join-comp-id" value="" />
          <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
          <p style="margin-bottom: 15px;"><strong>Competition:</strong> <span id="join-comp-title"></span></p>
          
          <div class="form-group">
            <label>Attach one of your posts (Optional)</label>
            <select name="post_id" id="join-post-select" class="form-input" style="padding: 10px;">
              <option value="">No post for now</option>
              <?php foreach ($user_posts as $p): ?>
                <option value="<?php echo $p['id']; ?>" data-type="<?php echo e($p['type']); ?>" style="display: none;">
                  <?php echo e($p['title']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <button type="button" class="btn-submit" id="join-submit-btn" onclick="submitJoin()">
            <i class="fas fa-check"></i> Confirm Registration
          </button>
        </form>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php include "includes/footer.php"; ?>
  <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>