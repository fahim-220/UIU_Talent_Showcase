<?php
require_once 'includes/auth.php';

$stmt = $pdo->prepare("
    SELECT p.*, u.name as author_name, u.avatar,
           (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
           (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count,
           COALESCE(pp.post_points, 0) as total_points
    FROM posts p
    JOIN users u ON p.user_id = u.id
    LEFT JOIN (SELECT post_id, SUM(points) as post_points FROM points WHERE post_id IS NOT NULL GROUP BY post_id) pp ON pp.post_id = p.id
    WHERE p.type = ?
    ORDER BY p.created_at DESC
");
$stmt->execute(['text']);
$posts = $stmt->fetchAll();

$liked_posts = [];
$comments_by_post = [];

if (count($posts) > 0) {
    $post_ids = array_column($posts, 'id');
    $in_clause = str_repeat('?,', count($post_ids) - 1) . '?';

    // 1. Get likes for current user
    if (is_logged_in()) {
        $l_stmt = $pdo->prepare("SELECT post_id FROM likes WHERE user_id = ? AND post_id IN ($in_clause)");
        $params = array_merge([$_SESSION['user_id']], $post_ids);
        $l_stmt->execute($params);
        $liked_posts = $l_stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // 2. Get comments
    $c_stmt = $pdo->prepare("
        SELECT c.*, u.name as author_name, u.avatar
        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.post_id IN ($in_clause)
        ORDER BY c.created_at ASC
    ");
    $c_stmt->execute($post_ids);
    $all_comments = $c_stmt->fetchAll();
    
    foreach ($all_comments as $c) {
        $comments_by_post[$c['post_id']][] = $c;
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Read inspiring blogs and articles written by the UIU community." />
  <title>Blog Entries | UIU Talent Show</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css" />
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/pages/blog.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="category-page blog-page">
  <?php include "includes/header.php"; ?>

  <main class="category-main">
    <div class="page-heading">
      <p class="hero-badge"><i class="fas fa-pen-nib"></i> Creative showcase</p>
      <h1>Blog Entries</h1>
      <p>Stories, essays, and photo journals written by UIU students.</p>
    </div>
    <section class="content-section active-tab">
      <div class="blog-grid">
        <?php if (count($posts) === 0): ?>
          <div style="text-align: center; width: 100%; padding: 40px 0;">
            <p style="color: var(--text3); font-size: 1.1rem;">No blog entries yet.</p>
            <p style="margin-top: 10px;">Be the first! <a href="<?php echo is_logged_in() ? '#' : BASE_URL.'/login.php'; ?>" onclick="<?php echo is_logged_in() ? "openModal('upload-modal'); return false;" : ""; ?>" style="color: var(--accent); text-decoration: none;">Write a post</a></p>
          </div>
        <?php else: ?>
          <?php foreach ($posts as $post): ?>
            <?php $is_liked = in_array($post['id'], $liked_posts); ?>
            <article class="blog-card">
              <div class="blog-header">
                <?php if ($post['avatar']): ?>
                  <img src="<?php echo e($post['avatar']); ?>" class="avatar-lg" alt="<?php echo e($post['author_name']); ?>" />
                <?php else: ?>
                  <div class="avatar-lg" style="background: var(--accent); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 16px; border-radius: 50%; width: 40px; height: 40px;"><?php echo e(strtoupper(substr($post['author_name'], 0, 1))); ?></div>
                <?php endif; ?>
                <div>
                  <h4 class="blog-author"><?php echo e($post['author_name']); ?></h4>
                  <span class="blog-date"><i class="far fa-calendar"></i> <?php echo date('M j, Y', strtotime($post['created_at'])); ?></span>
                </div>
                <span class="cat-badge blog-badge"><i class="fas fa-pen-nib"></i> Blog</span>
              </div>
              <h3 class="blog-title"><?php echo e($post['title']); ?></h3>
              <p class="blog-excerpt"><?php echo nl2br(e($post['description'])); ?></p>
              <?php if ($post['cover_image']): ?>
                <div class="blog-photos">
                  <div class="blog-photo-item">
                    <img src="<?php echo BASE_URL . '/' . e($post['cover_image']); ?>" alt="Cover" />
                  </div>
                </div>
              <?php endif; ?>
              <div class="card-footer">
                <div class="reactions">
                    <button aria-label="React" class="react-btn like-btn <?php echo $is_liked ? 'liked' : ''; ?>" data-id="<?php echo $post['id']; ?>" onclick="likePost(this)">
                      <i class="<?php echo $is_liked ? 'fas fa-heart' : 'far fa-heart'; ?>"></i> <span><?php echo $post['like_count']; ?></span>
                    </button>
                    <button aria-label="React" class="react-btn comment-btn" onclick="openComments(this)"><i class="far fa-comment"></i> <span class="comment-count-text"><?php echo $post['comment_count']; ?></span></button>
                </div>
                <div class="points-badge" title="Points awarded for this post"><i class="fas fa-star"></i> <span><?php echo e($post['total_points']); ?></span> pts</div>
              </div>
              <div class="comments-panel hidden">
                <div class="comment-list">
                  <?php if (isset($comments_by_post[$post['id']])): ?>
                    <?php foreach ($comments_by_post[$post['id']] as $c): ?>
                      <div class="comment">
                        <?php if ($c['avatar']): ?>
                          <img src="<?php echo e($c['avatar']); ?>" class="c-avatar" alt="<?php echo e($c['author_name']); ?>" />
                        <?php else: ?>
                          <div class="c-avatar" style="background: var(--accent2); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 10px; width: 24px; height: 24px; border-radius: 50%;"><?php echo e(strtoupper(substr($c['author_name'], 0, 1))); ?></div>
                        <?php endif; ?>
                        <div>
                          <strong><?php echo e($c['author_name']); ?></strong>
                          <p><?php echo nl2br(e($c['body'])); ?></p>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
                <div class="comment-input-row">
                  <input class="comment-input" placeholder="Add a comment..." />
                  <button aria-label="Send" class="send-btn" data-id="<?php echo $post['id']; ?>" onclick="addComment(this)"><i class="fas fa-paper-plane"></i></button>
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