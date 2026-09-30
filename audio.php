<?php
require_once 'includes/auth.php';

$stmt = $pdo->prepare("
    SELECT p.*, u.name as author_name, u.avatar,
           (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
           (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count,
           (SELECT COALESCE(SUM(points), 0) FROM points WHERE user_id = p.user_id) as total_points
    FROM posts p
    JOIN users u ON p.user_id = u.id
    WHERE p.type = 'audio'
    ORDER BY p.created_at DESC
");
$stmt->execute();
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Audio Entries | UIU Talent Show</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css" />
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/pages/audio.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="category-page audio-page">
  <?php include "includes/header.php"; ?>

  <main class="category-main">
    <div class="page-heading">
      <p class="hero-badge"><i class="fas fa-music"></i> Creative showcase</p>
      <h1>Audio Entries</h1>
      <p>Original music, podcasts, beatboxing, and vocal performances.</p>
    </div>
    <section class="content-section active-tab">
      <div class="cards-grid">
        <?php if (count($posts) === 0): ?>
          <div style="text-align: center; width: 100%; grid-column: 1 / -1; padding: 40px 0;">
            <p style="color: var(--text3); font-size: 1.1rem;">No audio entries yet.</p>
            <p style="margin-top: 10px;">Be the first! <a href="<?php echo is_logged_in() ? '#' : BASE_URL.'/login.php'; ?>" onclick="<?php echo is_logged_in() ? "openModal('upload-modal'); return false;" : ""; ?>" style="color: var(--accent); text-decoration: none;">Post audio</a></p>
          </div>
        <?php else: ?>
          <?php foreach ($posts as $post): ?>
            <article class="media-card">
              <div class="card-thumbnail audio-thumb">
                <i class="fas fa-headphones-alt"></i>
                <span class="cat-badge audio-badge"><i class="fas fa-music"></i> Audio</span>
              </div>
              <div class="card-body">
                <h3 class="card-title"><?php echo e($post['title']); ?></h3>
                <div class="card-meta">
                  <?php if ($post['avatar']): ?>
                    <img src="<?php echo e($post['avatar']); ?>" class="avatar" alt="<?php echo e($post['author_name']); ?>" />
                  <?php else: ?>
                    <div class="avatar" style="background: var(--accent); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px;"><?php echo e(strtoupper(substr($post['author_name'], 0, 1))); ?></div>
                  <?php endif; ?>
                  <span class="author"><?php echo e($post['author_name']); ?></span>
                  <span class="dot">&bull;</span>
                  <span class="date"><?php echo date('M j, Y', strtotime($post['created_at'])); ?></span>
                </div>
                <div class="audio-player">
                  <audio controls>
                    <source src="<?php echo BASE_URL . '/' . e($post['file_path']); ?>" type="audio/mpeg" />
                  </audio>
                </div>
                <?php if ($post['description']): ?>
                  <p class="card-desc"><?php echo nl2br(e($post['description'])); ?></p>
                <?php endif; ?>
                <div class="card-footer">
                  <div class="reactions">
                    <button aria-label="React" class="react-btn like-btn" onclick="likePost(this)"><i class="far fa-heart"></i> <span><?php echo $post['like_count']; ?></span></button>
                    <button aria-label="React" class="react-btn comment-btn" onclick="openComments(this)"><i class="far fa-comment"></i> <span><?php echo $post['comment_count']; ?></span></button>
                  </div>
                  <div class="points-badge"><i class="fas fa-star"></i> <span><?php echo $post['total_points']; ?></span> pts</div>
                </div>
                <div class="comments-panel hidden">
                  <div class="comment-list"></div>
                  <div class="comment-input-row">
                    <input class="comment-input" placeholder="Add a comment..." />
                    <button aria-label="Send" class="send-btn" onclick="addComment(this)"><i class="fas fa-paper-plane"></i></button>
                  </div>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>
  </main>
  <?php include "includes/footer.php"; ?>
  <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>