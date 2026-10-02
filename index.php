<?php 
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/public_data.php'; 

$preview_comp = null;
if (isset($pdo)) {
    $stmt = $pdo->prepare("
        SELECT * FROM competitions 
        WHERE status = 'open' AND deadline >= CURDATE() 
        ORDER BY deadline ASC 
        LIMIT 1
    ");
    $stmt->execute();
    $preview_comp = $stmt->fetch();
}
$site_stats = get_site_stats($pdo);
$leaderboard = get_leaderboard($pdo, 10);

$images = [
    'video' => 'https://images.unsplash.com/photo-1504609813442-a8924e83f76e?w=640&h=360&fit=crop&auto=format',
    'audio' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=640&h=360&fit=crop&auto=format',
    'text' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?w=640&h=360&fit=crop&auto=format',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Discover, develop, and display your unique gifts on UIU Talent Hunter." />
  <title>UIU Talent Hunter</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="home-page">

  <?php include "includes/header.php"; ?>

  <!-- ========== HERO ========== -->
  <section id="home" class="hero">
    <div class="hero-bg"></div>
    <div class="hero-content">
      <p class="hero-badge"><i class="fas fa-sparkles"></i> A platform for the truly talented</p>
      <h1 class="hero-title">Welcome to a platform<br />built for the <span class="gradient-text">truly talented.</span></h1>
      <p class="hero-subtitle">UIU Talent Hunter helps individuals discover, develop, and display their unique gifts. Showcase your work, compete with peers, learn from leaders, and turn your passion into opportunity.</p>
      <div class="hero-stats">
        <div class="stat"><span class="stat-num"><?php echo format_count($site_stats['users']); ?>+</span><span class="stat-label">Talents discovered</span></div>
        <div class="stat-divider"></div>
        <div class="stat"><span class="stat-num"><?php echo format_count($site_stats['posts']); ?>+</span><span class="stat-label">Showcases shared</span></div>
        <div class="stat-divider"></div>
        <div class="stat"><span class="stat-num"><?php echo format_count($site_stats['likes']); ?>+</span><span class="stat-label">Community votes</span></div>
      </div>
      <div class="hero-btns">
        <a href="#competitions" class="btn-primary">Explore Opportunities</a>
        <?php if (is_logged_in()): ?>
          <button class="btn-secondary" onclick="openModal('upload-modal')">Submit Your Entry</button>
        <?php else: ?>
          <a href="<?php echo BASE_URL; ?>/login.php" class="btn-secondary" style="text-decoration:none; display: inline-block;">Submit Your Entry</a>
        <?php endif; ?>
      </div>
    </div>
    <div class="hero-cards">
      <div class="floating-card card1"><i class="fas fa-video"></i><span>Video</span></div>
      <div class="floating-card card2"><i class="fas fa-music"></i><span>Audio</span></div>
      <div class="floating-card card3"><i class="fas fa-pen-nib"></i><span>Blog</span></div>
    </div>
  </section>

  <main class="home-discovery">
    <section class="welcome-section home-section">
      <div class="section-kicker"><i class="fas fa-compass"></i> One ecosystem. Every possibility.</div>
      <h2>Showcase. League. Forum.</h2>
      <p class="section-lead">Whether you want to <strong>Showcase</strong> your finest work, compete in a <strong>League</strong> of peers, or connect in our <strong>Forum</strong>, UIU Talent Hunter gives your talent room to grow.</p>
      <div class="feature-row">
        <a class="feature-chip" href="video.php"><i class="fas fa-display"></i><span>Showcase</span></a>
        <a class="feature-chip" href="competitions.php"><i class="fas fa-trophy"></i><span>Compete</span></a>
        <a class="feature-chip" href="#leaderboard"><i class="fas fa-ranking-star"></i><span>Earn recognition</span></a>
        <a class="feature-chip" href="how-it-works.php"><i class="fas fa-graduation-cap"></i><span>Learn</span></a>
        <a class="feature-chip" href="achievers.php"><i class="fas fa-store"></i><span>Shop talent</span></a>
      </div>
    </section>

    <section id="how-it-works" class="how-section home-section">
      <div class="section-heading-row"><div><div class="section-kicker"><i class="fas fa-route"></i> Your talent journey</div><h2>How it Works</h2></div><a class="text-link" href="how-it-works.php">See all steps <i class="fas fa-arrow-right"></i></a></div>
      <div class="steps-grid">
        <article class="step-card"><span>01</span><i class="fas fa-user-plus"></i><h3>Register</h3><p>Create your free account and join our global community.</p></article>
        <article class="step-card"><span>02</span><i class="fas fa-right-to-bracket"></i><h3>Log In</h3><p>Open your personalized dashboard and talent toolkit.</p></article>
        <article class="step-card"><span>03</span><i class="fas fa-lightbulb"></i><h3>Discover Your Talents</h3><p>Use assessments to uncover your unique skills and passions.</p></article>
        <article class="step-card"><span>04</span><i class="fas fa-upload"></i><h3>Showcase</h3><p>Upload work, portfolios, or performances for the world to see.</p></article>
        <article class="step-card"><span>05</span><i class="fas fa-medal"></i><h3>Compete</h3><p>Enter challenges, test your skills, and climb leaderboards.</p></article>
        <article class="step-card"><span>06</span><i class="fas fa-people-group"></i><h3>League</h3><p>Join specialized circles and grow alongside your peers.</p></article>
        <article class="step-card"><span>07</span><i class="fas fa-comments"></i><h3>Forum</h3><p>Ask questions, share knowledge, and build real connections.</p></article>
        <article class="step-card"><span>08</span><i class="fas fa-coins"></i><h3>Earn</h3><p>Monetize your talent through prizes, sponsors, and the marketplace.</p></article>
        <article class="step-card"><span>09</span><i class="fas fa-book-open"></i><h3>Learn</h3><p>Access courses, tutorials, and insights from industry leaders.</p></article>
        <article class="step-card"><span>10</span><i class="fas fa-bag-shopping"></i><h3>Shops</h3><p>Discover unique services and products from gifted creators.</p></article>
      </div>
    </section>

    <section id="competitions" class="competition-preview home-section">
      <div class="section-heading-row"><div><div class="section-kicker"><i class="fas fa-fire"></i> Enter the arena</div><h2>Competitions</h2><p class="section-lead">Prove your talent to the world.</p></div><a class="text-link" href="competitions.php">View all <i class="fas fa-arrow-right"></i></a></div>
      <div class="competition-grid">
        <?php if ($preview_comp): ?>
          <article class="competition-card">
            <img src="<?php echo e($images[$preview_comp['category']]); ?>" alt="Competition image" />
            <div class="competition-body">
              <span class="competition-type"><?php echo ucfirst(e($preview_comp['category'])); ?> Competition</span>
              <h3><?php echo e($preview_comp['title']); ?></h3>
              <p><?php echo substr(e($preview_comp['description']), 0, 100); ?>...</p>
              <div class="competition-meta">
                <span><b><?php echo $preview_comp['start_date'] ? date('M j', strtotime($preview_comp['start_date'])) : '-'; ?></b> Start</span>
                <span><b><?php echo date('M j', strtotime($preview_comp['deadline'])); ?></b> End</span>
                <span><b><?php echo $preview_comp['prize'] ? e($preview_comp['prize']) : '-'; ?></b> Prize</span>
              </div>
              <a class="btn-primary" href="<?php echo BASE_URL; ?>/competitions.php">Register Now</a>
            </div>
          </article>
        <?php else: ?>
          <p style="color: var(--text3);">No active competitions at the moment. Check back soon!</p>
        <?php endif; ?>
      </div>
    </section>

    <section class="community-section home-section">
      <div class="community-panel achievers-panel"><div class="section-kicker"><i class="fas fa-star"></i> Celebrating excellence</div><h2>Talented Achievers</h2><p class="section-lead">Our leaderboard celebrates excellence across every discipline.</p><a class="text-link" href="achievers.php">Meet the achievers <i class="fas fa-arrow-right"></i></a>
        <?php if (empty($leaderboard)): ?>
          <p style="color: var(--text3); margin-top: 20px;">No achievers yet. Be the first!</p>
        <?php else: ?>
          <div class="person-row">
            <?php foreach (array_slice($leaderboard, 0, 2) as $achiever): ?>
              <?php if (!empty($achiever['avatar'])): ?>
                <img src="<?php echo e($achiever['avatar']); ?>" alt="<?php echo e($achiever['name']); ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;" />
              <?php else: ?>
                <div class="default-avatar" style="width: 50px; height: 50px; border-radius: 50%; font-size: 18px; border: 2px solid var(--border); display: flex; align-items: center; justify-content: center; background: var(--surface2);"><?php echo initials($achiever['name']); ?></div>
              <?php endif; ?>
              <div>
                <strong><?php echo e($achiever['name']); ?></strong>
                <span><?php echo $achiever['department'] ? e($achiever['department']) : 'UIU Student'; ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="community-panel supporters-panel"><div class="section-kicker"><i class="fas fa-hand-holding-heart"></i> Backing brilliance</div><h2>Top Supporters</h2><p class="section-lead">The generous people powering talent forward.</p><div class="supporter-list"><div><span>#1</span><strong>Vikram Anand</strong><small>Gold Supporter</small></div><div><span>#2</span><strong>Lena Hoffman</strong><small>Diamond Patron</small></div><div><span>#3</span><strong>Zara Osei</strong><small>Platinum Backer</small></div></div></div>
    </section>

    <section class="organizations-section home-section"><div class="section-heading-row"><div><div class="section-kicker"><i class="fas fa-building-columns"></i> Growing together</div><h2>Organizations</h2><p class="section-lead">Partner institutions shaping the next generation of talent.</p></div><a class="text-link" href="#mission">Partner with us <i class="fas fa-arrow-right"></i></a></div><div class="organization-grid"><article><img src="https://images.unsplash.com/photo-1547153760-18fc86324498?w=220&h=220&fit=crop&auto=format" alt="Dance academy" /><strong>Natya Kala</strong><span>Dance Academy</span></article><article><img src="https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=220&h=220&fit=crop&auto=format" alt="Photography studio" /><strong>Lens &amp; Light</strong><span>Photography</span></article><article><img src="https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=220&h=220&fit=crop&auto=format" alt="Vocal coaching" /><strong>VoiceBox Co.</strong><span>Vocal Coaching</span></article></div></section>

    <section class="testimonials-section home-section"><div class="section-kicker"><i class="fas fa-quote-left"></i> Voices from the community</div><h2>Testimonials</h2><div class="testimonial-grid"><blockquote><p>“UIU Talent Hunter gave me the platform I always dreamed of. Within weeks I had followers, feedback, and my first paying client.”</p><footer><img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=80&h=80&fit=crop&auto=format" alt="Aisha Malik" /><span><strong>Aisha Malik</strong><small>Digital Illustrator</small></span></footer></blockquote><blockquote><p>“The competitions here are unlike anything else. Real judges, real prizes, and a community that genuinely cheers you on.”</p><footer><img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&h=80&fit=crop&auto=format" alt="Siddharth Rao" /><span><strong>Siddharth Rao</strong><small>Bharatanatyam Dancer</small></span></footer></blockquote><blockquote><p>“Six months on UIU Talent Hunter and I have 4,200 followers and a full client calendar. The exposure is phenomenal.”</p><footer><img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=80&h=80&fit=crop&auto=format" alt="Claire Dubois" /><span><strong>Claire Dubois</strong><small>Photographer</small></span></footer></blockquote></div></section>

    <section id="mission" class="mission-section home-section"><div class="section-kicker"><i class="fas fa-heart"></i> Why we exist</div><h2>Our Mission</h2><div class="mission-grid"><article><i class="fas fa-bolt"></i><h3>Empower Every Talent</h3><p>Every individual carries a spark of brilliance. We give that spark a stage, a community, and a future.</p></article><article><i class="fas fa-handshake"></i><h3>Build Authentic Connections</h3><p>We bridge creators with audiences, sponsors with achievers, and learners with mentors.</p></article><article><i class="fas fa-award"></i><h3>Recognise &amp; Reward Excellence</h3><p>Through competitions, league tables, and spotlights, we celebrate passion and progress.</p></article></div></section>
  </main>

  <!--  LEADERBOARD  -->
  <section id="leaderboard" class="leaderboard-section">
    <div class="section-header">
      <h2><i class="fas fa-trophy"></i> Leaderboard</h2>
      <p>Top talent ranked by audience votes + admin points</p>
    </div>

    <?php
    $typeMap = ['video' => ['name' => 'Video', 'class' => 'video-pill'], 'audio' => ['name' => 'Audio', 'class' => 'audio-pill'], 'blog' => ['name' => 'Blog', 'class' => 'blog-pill']];
    $top3 = array_slice($leaderboard, 0, 3);
    $others = array_slice($leaderboard, 3);
    $first = isset($top3[0]) ? $top3[0] : null;
    $second = isset($top3[1]) ? $top3[1] : null;
    $third = isset($top3[2]) ? $top3[2] : null;
    
    function render_podium($item, $pos, $typeMap) {
        if (!$item) return '';
        $classes = [1 => 'first', 2 => 'second', 3 => 'third'];
        $rankStr = [1 => '1st', 2 => '2nd', 3 => '3rd'];
        $cls = $classes[$pos];
        $rStr = $rankStr[$pos];
        $out = '<div class="podium-item ' . $cls . '">';
        if ($pos == 1) $out .= '<div class="crown">👑</div>';
        
        if (!empty($item['avatar'])) {
            $out .= '<img src="' . e($item['avatar']) . '" class="podium-avatar" alt="' . $rStr . '" style="object-fit: cover;" />';
        } else {
            $out .= '<div class="podium-avatar" style="border: 2px solid var(--border); display: flex; align-items: center; justify-content: center; background: var(--surface2); font-size: 24px; font-weight: 600; color: var(--text);">' . initials($item['name']) . '</div>';
        }
        
        $out .= '<div class="podium-name">' . e($item['name']) . '</div>';
        $typeStr = isset($typeMap[$item['last_type']]) ? $typeMap[$item['last_type']]['name'] : '-';
        $out .= '<div class="podium-tag">' . $typeStr . '</div>';
        $out .= '<div class="podium-block"><span class="podium-rank">' . $rStr . '</span><span class="podium-pts">' . $item['total_points'] . ' pts</span></div>';
        $out .= '</div>';
        return $out;
    }
    ?>
    <?php if (empty($leaderboard)): ?>
      <div style="text-align: center; padding: 40px; color: var(--text3);">No leaderboard data yet. Keep participating!</div>
    <?php else: ?>
      <div class="podium">
        <?php echo render_podium($second, 2, $typeMap); ?>
        <?php echo render_podium($first, 1, $typeMap); ?>
        <?php echo render_podium($third, 3, $typeMap); ?>
      </div>
  
      <div class="leaderboard-table-wrap">
        <table class="leaderboard-table" id="leaderboard-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Participant</th>
              <th>Category</th>
              <th>Likes</th>
              <th>Comments</th>
              <th>Points</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($leaderboard as $idx => $user): 
                $rankCls = '';
                $badgeCls = '';
                if ($user['rank'] == 1) { $rankCls = 'rank-1'; $badgeCls = 'gold'; }
                elseif ($user['rank'] == 2) { $rankCls = 'rank-2'; $badgeCls = 'silver'; }
                elseif ($user['rank'] == 3) { $rankCls = 'rank-3'; $badgeCls = 'bronze'; }
            ?>
            <tr class="<?php echo $rankCls; ?>">
              <td><span class="rank-badge <?php echo $badgeCls; ?>"><?php echo $user['rank']; ?></span></td>
              <td>
                <?php if (!empty($user['avatar'])): ?>
                  <img src="<?php echo e($user['avatar']); ?>" class="t-avatar" alt="avatar" style="object-fit: cover;" />
                <?php else: ?>
                  <div class="t-avatar" style="border: 2px solid var(--border); display: inline-flex; align-items: center; justify-content: center; background: var(--surface2); font-size: 10px; font-weight: 600; color: var(--text); vertical-align: middle; border-radius: 50%;"><?php echo initials($user['name']); ?></div>
                <?php endif; ?>
                <?php echo e($user['name']); ?>
              </td>
              <td>
                <?php if (isset($typeMap[$user['last_type']])): ?>
                  <span class="cat-pill <?php echo $typeMap[$user['last_type']]['class']; ?>"><?php echo $typeMap[$user['last_type']]['name']; ?></span>
                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
              <td><?php echo $user['likes_received']; ?></td>
              <td><?php echo $user['comments_received']; ?></td>
              <td><strong class="total-pts"><?php echo $user['total_points']; ?></strong></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>
  <?php include "includes/footer.php"; ?>

  

  <script src="assets/js/main.js"></script>
</body>
</html>
