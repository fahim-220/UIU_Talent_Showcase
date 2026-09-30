<nav class="navbar">
    <div class="nav-brand">
      <span class="brand-icon">🎭</span>
      <span class="brand-text">UIU <span class="highlight">Talent Hunter</span></span>
    </div>
    <ul class="nav-links">
      <li><a href="index.php" class="nav-link">Home</a></li>
      <li class="nav-dropdown">
        <button class="nav-link dropdown-toggle" type="button" onclick="toggleTalentMenu(event)">Discover your talents <i class="fas fa-chevron-down"></i></button>
        <div class="dropdown-menu">
          <a href="video.php"><i class="fas fa-video"></i> Video</a>
          <a href="audio.php"><i class="fas fa-music"></i> Audio</a>
          <a href="blog.php"><i class="fas fa-align-left"></i> Text</a>
        </div>
      </li>
      <li><a href="how-it-works.php" class="nav-link">How It Works</a></li>
      <li><a href="achievers.php" class="nav-link">Talented Achievers</a></li>
      <li><a href="competitions.php" class="nav-link">Competitions</a></li>
    </ul>
    <div class="nav-actions">
      <button aria-label="Upload" class="btn-upload" onclick="openModal('upload-modal')"><i class="fas fa-plus"></i> Post</button>
    </div>
    <button class="hamburger" onclick="toggleMenu()" aria-label="Toggle menu"><i class="fas fa-bars"></i></button>
  </nav>