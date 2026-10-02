<footer class="footer">
    <div class="footer-content">
      <div class="footer-brand">
        <span class="brand-icon">🎭</span>
        <span class="brand-text">UIU <span class="highlight">Talent Hunter</span></span>
        <p>A platform for UIU students to showcase their creativity and talent.</p>
      </div>
      <div class="footer-links">
        <h5>Quick Links</h5>
        <a href="<?php echo BASE_URL; ?>/how-it-works.php">How It Works</a>
        <a href="<?php echo BASE_URL; ?>/competitions.php">Competitions</a>
        <a href="<?php echo BASE_URL; ?>/achievers.php">Leaderboard</a>
      </div>
      <div class="footer-links">
        <h5>Follow Us</h5>
        <?php if (!empty(SOCIAL_FACEBOOK_URL)): ?>
          <a href="<?php echo e(SOCIAL_FACEBOOK_URL); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook"></i> Facebook</a>
        <?php else: ?>
          <span style="color: var(--text3); display: flex; align-items: center; gap: 8px;"><i class="fab fa-facebook"></i> Facebook</span>
        <?php endif; ?>
        
        <?php if (!empty(SOCIAL_INSTAGRAM_URL)): ?>
          <a href="<?php echo e(SOCIAL_INSTAGRAM_URL); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram"></i> Instagram</a>
        <?php else: ?>
          <span style="color: var(--text3); display: flex; align-items: center; gap: 8px;"><i class="fab fa-instagram"></i> Instagram</span>
        <?php endif; ?>
        
        <?php if (!empty(SOCIAL_YOUTUBE_URL)): ?>
          <a href="<?php echo e(SOCIAL_YOUTUBE_URL); ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fab fa-youtube"></i> YouTube</a>
        <?php else: ?>
          <span style="color: var(--text3); display: flex; align-items: center; gap: 8px;"><i class="fab fa-youtube"></i> YouTube</span>
        <?php endif; ?>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; 2026 UIU Talent Hunter. Made with ❤️ for UIU students.</p>
    </div>
</footer>

  <!-- ========== UPLOAD MODAL ========== -->
  <div class="modal-overlay hidden" id="upload-modal">
    <div class="modal">
      <div class="modal-header">
        <h3><i class="fas fa-upload"></i> Submit Your Entry</h3>
        <button aria-label="Close" class="modal-close" onclick="closeModal('upload-modal')">&times;</button>
      </div>
      <div class="modal-body">
        <form id="upload-form">
          <input type="hidden" name="type" id="upload-type" value="video" />
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>" />
          <div class="form-group">
            <label>Entry Category</label>
            <div class="category-select">
              <button type="button" class="cat-option active" onclick="selectCat(this,'video')"><i class="fas fa-video"></i> Video</button>
              <button type="button" class="cat-option" onclick="selectCat(this,'audio')"><i class="fas fa-music"></i> Audio</button>
              <button type="button" class="cat-option" onclick="selectCat(this,'text')"><i class="fas fa-pen-nib"></i> Blog (Text)</button>
            </div>
          </div>
          <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" id="upload-title" class="form-input" placeholder="Give your entry a title" required maxlength="150" />
          </div>
          <div class="form-group" id="upload-file-group">
            <label>Upload File</label>
            <div class="file-drop" onclick="document.getElementById('file-input').click()">
              <i class="fas fa-cloud-upload-alt"></i>
              <p>Click to upload or drag & drop</p>
              <span id="upload-file-hint">Video: MP4, WEBM</span>
            </div>
            <input type="file" id="file-input" name="file" class="hidden" accept=".mp4,.webm" />
          </div>
          <div class="form-group hidden" id="upload-cover-group">
            <label>Cover Image (Optional)</label>
            <input type="file" id="cover-input" name="cover_image" class="form-input" accept=".jpg,.jpeg,.png,.webp" />
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea name="description" id="upload-description" class="form-input" rows="4" placeholder="Tell us about your entry..."></textarea>
          </div>
          <button type="button" class="btn-submit" id="upload-submit-btn" onclick="submitEntry()">
            <i class="fas fa-paper-plane"></i> Submit Entry
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- ========== TOAST ========== -->
  <div class="toast hidden" id="toast"></div>
  <script>
    window.csrfToken = "<?php echo e(csrf_token()); ?>";
    window.isLoggedIn = <?php echo is_logged_in() ? 'true' : 'false'; ?>;
    window.loginUrl = "<?php echo BASE_URL; ?>/login.php";
  </script>
