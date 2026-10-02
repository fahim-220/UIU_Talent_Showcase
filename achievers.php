<?php require_once 'includes/auth.php'; require_once 'includes/public_data.php'; ?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><meta name="description" content="Meet the top talented achievers on the UIU Talent Hunter leaderboard." />
  <title>Talented Achievers | UIU Talent Hunter</title><link rel="stylesheet" href="assets/css/style.css" /><link rel="stylesheet" href="assets/css/pages/achievers.css" /><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" /><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" /></head>
<body class="category-page achievers-page"><?php include "includes/header.php"; ?>
<main class="achievers-page-main"><header class="page-heading"><p class="hero-badge"><i class="fas fa-star"></i> Celebrating excellence</p><h1>Talented Achievers</h1><p>Meet the people turning passion into progress across every discipline.</p></header><section class="achiever-grid">
<?php
$achievers = get_leaderboard($pdo, 12);
if (empty($achievers)):
?>
  <p style="color: var(--text3); font-size: 1.1rem; text-align: center; width: 100%;">No achievers yet. Be the first to join the leaderboard!</p>
<?php else: ?>
  <?php foreach ($achievers as $idx => $a): ?>
    <article class="achiever-card">
      <?php if (!empty($a['avatar'])): ?>
        <img src="<?php echo e($a['avatar']); ?>" alt="<?php echo e($a['name']); ?>" style="object-fit: cover;" />
      <?php else: ?>
        <div style="width: 100%; aspect-ratio: 1/1; background: var(--surface2); display: flex; align-items: center; justify-content: center; font-size: 64px; font-weight: bold; color: var(--text3);"><?php echo initials($a['name']); ?></div>
      <?php endif; ?>
      <div>
        <span class="achiever-rank"><?php echo str_pad($a['rank'], 2, '0', STR_PAD_LEFT); ?></span>
        <h2><?php echo e($a['name']); ?></h2>
        <p><?php echo $a['department'] ? e($a['department']) : 'UIU Student'; ?></p>
        <div style="font-size: 0.85rem; color: var(--text2); margin-top: 10px;">
          <span style="color: var(--accent); font-weight: 600;"><?php echo $a['total_points']; ?> pts</span> &bull; 
          <?php echo $a['post_count']; ?> <?php echo $a['post_count'] == 1 ? 'post' : 'posts'; ?>
        </div>
        <!-- Using real data, last_type is used as skill label if not null -->
        <a href="index.php#leaderboard" class="text-link">View ranking <i class="fas fa-arrow-right"></i></a>
      </div>
    </article>
  <?php endforeach; ?>
<?php endif; ?>
</section></main><?php include "includes/footer.php"; ?><script src="assets/js/main.js"></script></body></html>