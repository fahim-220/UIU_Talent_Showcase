document.addEventListener('DOMContentLoaded', () => {
  // Tab switching logic
  const tabs = document.querySelectorAll('.dash-tab');
  const sections = document.querySelectorAll('.dash-section');
  
  function switchTab(hash) {
    if (!hash) hash = '#overview';
    
    let targetSection = document.querySelector(hash);
    let targetTab = document.querySelector(`.dash-tab[href="${hash}"]`);
    
    if (!targetSection) {
      hash = '#overview';
      targetSection = document.querySelector(hash);
      targetTab = document.querySelector(`.dash-tab[href="${hash}"]`);
    }
    
    if (targetSection && targetTab) {
      sections.forEach(sec => sec.classList.remove('active'));
      tabs.forEach(tab => tab.classList.remove('active'));
      
      targetSection.classList.add('active');
      targetTab.classList.add('active');
    }
  }

  // Handle initial load
  switchTab(window.location.hash);

  // Handle hash changes
  window.addEventListener('hashchange', () => {
    switchTab(window.location.hash);
  });

  // Avatar preview
  const avatarInput = document.getElementById('avatar-input');
  if (avatarInput) {
    avatarInput.addEventListener('change', function() {
      const file = this.files[0];
      if (file) {
        const previewEl = document.getElementById('avatar-preview-img');
        const defaultEl = document.getElementById('avatar-preview-default');
        
        const reader = new FileReader();
        reader.onload = function(e) {
          if (previewEl) {
            previewEl.src = e.target.result;
            previewEl.style.display = 'block';
            if (defaultEl) defaultEl.style.display = 'none';
          } else if (defaultEl) {
            // If user had no avatar previously, the img tag might not exist, create it
            const img = document.createElement('img');
            img.src = e.target.result;
            img.id = 'avatar-preview-img';
            defaultEl.parentNode.insertBefore(img, defaultEl);
            defaultEl.style.display = 'none';
          }
        }
        reader.readAsDataURL(file);
      }
    });
  }

  // Delete Post event delegation
  const postsTable = document.getElementById('my-posts-table');
  if (postsTable) {
    postsTable.addEventListener('click', async (e) => {
      const deleteBtn = e.target.closest('.delete-post-btn');
      if (!deleteBtn) return;
      
      const postId = deleteBtn.getAttribute('data-id');
      if (!postId) return;
      
      if (!confirm('Are you sure you want to delete this post? This cannot be undone.')) {
        return;
      }
      
      const originalHtml = deleteBtn.innerHTML;
      deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
      deleteBtn.disabled = true;
      
      const formData = new FormData();
      formData.append('post_id', postId);
      formData.append('csrf_token', window.csrfToken);
      
      try {
        const res = await fetch('api/delete_post.php', {
          method: 'POST',
          body: formData
        });
        
        let result;
        try {
          result = await res.json();
        } catch (err) {
          console.error(err);
          showToast('❌ Unexpected server response', 'error');
          return;
        }
        
        if (result.success) {
          showToast('🎉 ' + result.message);
          // Remove row
          const row = deleteBtn.closest('tr');
          if (row) row.remove();
          
          // Update count
          const countEl = document.getElementById('stat-total-posts');
          if (countEl && result.posts !== undefined) {
            countEl.textContent = result.posts;
          }
          
          // If no rows left, show empty state
          const tbody = postsTable.querySelector('tbody');
          if (tbody && tbody.children.length === 0) {
            postsTable.outerHTML = `<div class="empty-state">
              <p>You haven't posted anything yet.</p>
              <button class="btn-primary" style="margin-top: 15px;" onclick="openModal('upload-modal')">Upload a Post</button>
            </div>`;
          }
        } else {
          showToast('❌ ' + result.message, 'error');
        }
      } catch (err) {
        console.error(err);
        showToast('❌ Network error', 'error');
      } finally {
        if (document.body.contains(deleteBtn)) {
          deleteBtn.innerHTML = originalHtml;
          deleteBtn.disabled = false;
        }
      }
    });
  }
});

