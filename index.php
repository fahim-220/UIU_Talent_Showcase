<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
        <div class="stat"><span class="stat-num">128</span><span class="stat-label">Talents discovered</span></div>
        <div class="stat-divider"></div>
        <div class="stat"><span class="stat-num">347</span><span class="stat-label">Showcases shared</span></div>
        <div class="stat-divider"></div>
        <div class="stat"><span class="stat-num">1.2K</span><span class="stat-label">Community votes</span></div>
      </div>
      <div class="hero-btns">
        <a href="#competitions" class="btn-primary">Explore Opportunities</a>
        <button class="btn-secondary" onclick="openModal('upload-modal')">Submit Your Entry</button>
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
        <article class="competition-card"><img src="https://images.unsplash.com/photo-1504609813442-a8924e83f76e?w=640&h=360&fit=crop&auto=format" alt="Dancers performing on stage" /><div class="competition-body"><span class="competition-type">Dance Competition</span><h3>International Dancing Championship 2026</h3><p>Bring your unique dance style to the global stage. Open to every age group and experience level.</p><div class="competition-meta"><span><b>Aug 15</b> Start</span><span><b>Sep 30</b> End</span><span><b>$5,000</b> Prize</span></div><a class="btn-primary" href="competitions.php">Register Now</a></div></article>
        <article class="competition-card"><img src="https://images.unsplash.com/photo-1485846234645-a62644f84728?w=640&h=360&fit=crop&auto=format" alt="Actor performing on a stage" /><div class="competition-body"><span class="competition-type">Acting Competition</span><h3>UIU Stage &amp; Screen Acting Awards</h3><p>Perform a monologue, scene, or original piece in front of industry judges.</p><div class="competition-meta"><span><b>Aug 10</b> Start</span><span><b>Sep 30</b> End</span><span><b>$2,500</b> Prize</span></div><a class="btn-primary" href="competitions.php">Register Now</a></div></article>
        <article class="competition-card"><img src="https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=640&h=360&fit=crop&auto=format" alt="Musician performing with a microphone" /><div class="competition-body"><span class="competition-type">Music Competition</span><h3>UIU Music Fest — Emerging Artists Showcase</h3><p>From acoustic sets to full bands, if it moves you, it will move us.</p><div class="competition-meta"><span><b>Aug 5</b> Start</span><span><b>Oct 1</b> End</span><span><b>$4,000</b> Prize</span></div><a class="btn-primary" href="competitions.php">Register Now</a></div></article>
      </div>
    </section>

    <section class="community-section home-section">
      <div class="community-panel achievers-panel"><div class="section-kicker"><i class="fas fa-star"></i> Celebrating excellence</div><h2>Talented Achievers</h2><p class="section-lead">Our leaderboard celebrates excellence across every discipline.</p><a class="text-link" href="achievers.php">Meet the achievers <i class="fas fa-arrow-right"></i></a><div class="person-row"><img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=220&h=220&fit=crop&auto=format" alt="Arjun Sharma" /><div><strong>Arjun Sharma</strong><span>Photography</span></div><img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=220&h=220&fit=crop&auto=format" alt="Priya Nair" /><div><strong>Priya Nair</strong><span>Classical Dance</span></div></div></div>
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

    <div class="podium">
      <div class="podium-item second">
        <img src="https://i.pravatar.cc/60?img=12" class="podium-avatar" alt="2nd" />
        <div class="podium-name">Priya Sharma</div>
        <div class="podium-tag">Audio</div>
        <div class="podium-block">
          <span class="podium-rank">2nd</span>
          <span class="podium-pts">155 pts</span>
        </div>
      </div>
      <div class="podium-item first">
        <div class="crown">👑</div>
        <img src="https://i.pravatar.cc/70?img=45" class="podium-avatar" alt="1st" />
        <div class="podium-name">Omar Faruq</div>
        <div class="podium-tag">Blog</div>
        <div class="podium-block">
          <span class="podium-rank">1st</span>
          <span class="podium-pts">275 pts</span>
        </div>
      </div>
      <div class="podium-item third">
        <img src="https://i.pravatar.cc/60?img=40" class="podium-avatar" alt="3rd" />
        <div class="podium-name">Nusrat Jahan</div>
        <div class="podium-tag">Blog</div>
        <div class="podium-block">
          <span class="podium-rank">3rd</span>
          <span class="podium-pts">220 pts</span>
        </div>
      </div>
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
            <th>Admin Pts</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <tr class="rank-1">
            <td><span class="rank-badge gold">1</span></td>
            <td><img src="https://i.pravatar.cc/28?img=40" class="t-avatar" alt="avatar" /> Omar Faruq</td>
            <td><span class="cat-pill blog-pill">Blog</span></td>
            <td>115</td>
            <td>47</td>
            <td><span class="admin-pts" id="ap-omar">113</span></td>
            <td><strong class="total-pts">275</strong></td>
          </tr>
          <tr class="rank-2">
            <td><span class="rank-badge silver">2</span></td>
            <td><img src="https://i.pravatar.cc/28?img=12" class="t-avatar" alt="avatar" /> Priya Sharma</td>
            <td><span class="cat-pill audio-pill">Audio</span></td>
            <td>62</td>
            <td>21</td>
            <td><span class="admin-pts" id="ap-priya">72</span></td>
            <td><strong class="total-pts">155</strong></td>
          </tr>
          <tr class="rank-3">
            <td><span class="rank-badge bronze">3</span></td>
            <td><img src="https://i.pravatar.cc/28?img=33" class="t-avatar" alt="avatar" /> Nusrat Jahan</td>
            <td><span class="cat-pill blog-pill">Blog</span></td>
            <td>88</td>
            <td>34</td>
            <td><span class="admin-pts" id="ap-nusrat">98</span></td>
            <td><strong class="total-pts">220</strong></td>
          </tr>
          <tr>
            <td><span class="rank-badge">4</span></td>
            <td><img src="https://i.pravatar.cc/28?img=25" class="t-avatar" alt="avatar" /> Sabrina Islam</td>
            <td><span class="cat-pill audio-pill">Audio</span></td>
            <td>79</td>
            <td>30</td>
            <td><span class="admin-pts" id="ap-sabrina">91</span></td>
            <td><strong class="total-pts">200</strong></td>
          </tr>
          <tr>
            <td><span class="rank-badge">5</span></td>
            <td><img src="https://i.pravatar.cc/28?img=9" class="t-avatar" alt="avatar" /> Mehedi Khan</td>
            <td><span class="cat-pill video-pill">Video</span></td>
            <td>56</td>
            <td>19</td>
            <td><span class="admin-pts" id="ap-mehedi">65</span></td>
            <td><strong class="total-pts">140</strong></td>
          </tr>
          <tr>
            <td><span class="rank-badge">6</span></td>
            <td><img src="https://i.pravatar.cc/28?img=45" class="t-avatar" alt="avatar" /> Zara Ahmed</td>
            <td><span class="cat-pill blog-pill">Blog</span></td>
            <td>73</td>
            <td>28</td>
            <td><span class="admin-pts" id="ap-zara">84</span></td>
            <td><strong class="total-pts">185</strong></td>
          </tr>
          <tr>
            <td><span class="rank-badge">7</span></td>
            <td><img src="https://i.pravatar.cc/28?img=5" class="t-avatar" alt="avatar" /> Tanvir Hossain</td>
            <td><span class="cat-pill video-pill">Video</span></td>
            <td>41</td>
            <td>13</td>
            <td><span class="admin-pts" id="ap-tanvir">56</span></td>
            <td><strong class="total-pts">110</strong></td>
          </tr>
          <tr>
            <td><span class="rank-badge">8</span></td>
            <td><img src="https://i.pravatar.cc/28?img=20" class="t-avatar" alt="avatar" /> Farhan Uddin</td>
            <td><span class="cat-pill audio-pill">Audio</span></td>
            <td>38</td>
            <td>11</td>
            <td><span class="admin-pts" id="ap-farhan">46</span></td>
            <td><strong class="total-pts">95</strong></td>
          </tr>
          <tr>
            <td><span class="rank-badge">9</span></td>
            <td><img src="https://i.pravatar.cc/28?img=1" class="t-avatar" alt="avatar" /> Ayesha Rahman</td>
            <td><span class="cat-pill video-pill">Video</span></td>
            <td>24</td>
            <td>8</td>
            <td><span class="admin-pts" id="ap-ayesha">53</span></td>
            <td><strong class="total-pts">85</strong></td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>

  <?php include "includes/footer.php"; ?>

  

  <script src="assets/js/main.js"></script>
</body>
</html>
