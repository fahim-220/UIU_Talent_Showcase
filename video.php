<?php require_once 'includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Video Entries | UIU Talent Show</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/pages/video.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="category-page video-page">
  <?php include "includes/header.php"; ?>

  <main class="category-main">
    <div class="page-heading"><p class="hero-badge"><i class="fas fa-video"></i> Creative showcase</p><h1>Video Entries</h1><p>Travel diaries, performances, short films and more from the UIU community.</p></div>
    <section class="content-section active-tab">
      <div class="cards-grid">
        <article class="media-card"><div class="card-thumbnail"><video controls poster="https://picsum.photos/seed/v1/400/225"><source src="#" type="video/mp4" /></video><span class="cat-badge video-badge"><i class="fas fa-video"></i> Video</span></div><div class="card-body"><h3 class="card-title">My Journey to Cox's Bazar</h3><div class="card-meta"><img src="https://i.pravatar.cc/30?img=1" class="avatar" alt="Ayesha Rahman" /><span class="author">Ayesha Rahman</span><span class="dot">•</span><span class="date">Sep 1, 2026</span></div><p class="card-desc">A beautiful travel vlog capturing the serenity of the world's longest sea beach during monsoon season.</p><div class="card-footer"><div class="reactions"><button aria-label="React" class="react-btn like-btn" onclick="likePost(this)"><i class="far fa-heart"></i> <span>24</span></button><button aria-label="React" class="react-btn comment-btn" onclick="openComments(this)"><i class="far fa-comment"></i> <span>8</span></button></div><div class="points-badge"><i class="fas fa-star"></i> <span>85</span> pts</div></div><div class="comments-panel hidden"><div class="comment-list"></div><div class="comment-input-row"><input class="comment-input" placeholder="Add a comment..." /><button aria-label="Send" class="send-btn" onclick="addComment(this)"><i class="fas fa-paper-plane"></i></button></div></div></div></article>
        <article class="media-card"><div class="card-thumbnail"><video controls poster="https://picsum.photos/seed/v2/400/225"><source src="#" type="video/mp4" /></video><span class="cat-badge video-badge"><i class="fas fa-video"></i> Video</span></div><div class="card-body"><h3 class="card-title">Street Magic in Old Dhaka</h3><div class="card-meta"><img src="https://i.pravatar.cc/30?img=5" class="avatar" alt="Tanvir Hossain" /><span class="author">Tanvir Hossain</span><span class="dot">•</span><span class="date">Sep 2, 2026</span></div><p class="card-desc">Watch close-up card magic tricks surprising locals in the narrow lanes of Puran Dhaka.</p><div class="card-footer"><div class="reactions"><button aria-label="React" class="react-btn like-btn" onclick="likePost(this)"><i class="far fa-heart"></i> <span>41</span></button><button aria-label="React" class="react-btn comment-btn" onclick="openComments(this)"><i class="far fa-comment"></i> <span>13</span></button></div><div class="points-badge"><i class="fas fa-star"></i> <span>110</span> pts</div></div><div class="comments-panel hidden"><div class="comment-list"></div><div class="comment-input-row"><input class="comment-input" placeholder="Add a comment..." /><button aria-label="Send" class="send-btn" onclick="addComment(this)"><i class="fas fa-paper-plane"></i></button></div></div></div></article>
        <article class="media-card"><div class="card-thumbnail"><video controls poster="https://picsum.photos/seed/v3/400/225"><source src="#" type="video/mp4" /></video><span class="cat-badge video-badge"><i class="fas fa-video"></i> Video</span></div><div class="card-body"><h3 class="card-title">Sundarbans: Land of Tigers</h3><div class="card-meta"><img src="https://i.pravatar.cc/30?img=9" class="avatar" alt="Mehedi Khan" /><span class="author">Mehedi Khan</span><span class="dot">•</span><span class="date">Sep 3, 2026</span></div><p class="card-desc">A cinematic documentary short capturing the raw beauty of the Sundarbans mangrove forest.</p><div class="card-footer"><div class="reactions"><button aria-label="React" class="react-btn like-btn" onclick="likePost(this)"><i class="far fa-heart"></i> <span>56</span></button><button aria-label="React" class="react-btn comment-btn" onclick="openComments(this)"><i class="far fa-comment"></i> <span>19</span></button></div><div class="points-badge"><i class="fas fa-star"></i> <span>140</span> pts</div></div><div class="comments-panel hidden"><div class="comment-list"></div><div class="comment-input-row"><input class="comment-input" placeholder="Add a comment..." /><button aria-label="Send" class="send-btn" onclick="addComment(this)"><i class="fas fa-paper-plane"></i></button></div></div></div></article>
      </div>
    </section>
  </main>
  <?php include "includes/footer.php"; ?>
  <script src="assets/js/main.js"></script>
</body>
</html>