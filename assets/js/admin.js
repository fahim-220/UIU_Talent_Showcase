document.addEventListener('DOMContentLoaded', () => {
  // Confirm delete on generic delete-form
  document.body.addEventListener('submit', function(e) {
    if (e.target.classList.contains('delete-form')) {
      if (!confirm('Are you sure you want to delete this? This action cannot be undone.')) {
        e.preventDefault();
      }
    }
  });

});

  // Post award points AJAX form
  const awardForm = document.getElementById('award-form');
  if (awardForm) {
    awardForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      const btn = document.getElementById('award-submit-btn');
      const originalHtml = btn.innerHTML;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> processing...';
      btn.disabled = true;

      const formData = new FormData(awardForm);
      formData.append('csrf_token', window.csrfToken);

      try {
        const res = await fetch('../api/admin_points.php', {
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
          closeModal('award-modal');
          
          // Update badge
          const postId = document.getElementById('award-post-id').value;
          const badge = document.getElementById('badge-' + postId);
          if (badge && result.post_total !== undefined) {
            badge.textContent = '★ ' + result.post_total + ' pts';
          }
        } else {
          showToast('❌ ' + result.message, 'error');
        }
      } catch (err) {
        console.error(err);
        showToast('❌ Network error', 'error');
      } finally {
        if (document.body.contains(btn)) {
          btn.innerHTML = originalHtml;
          btn.disabled = false;
        }
      }
    });
  }

  // Event delegation for opening the award modal
  document.addEventListener('click', function(e) {
    const awardBtn = e.target.closest('.btn-award');
    if (awardBtn) {
      e.preventDefault();
      
      const postId = awardBtn.getAttribute('data-post-id');
      const title = awardBtn.getAttribute('data-post-title');
      const author = awardBtn.getAttribute('data-author-name');
      const type = awardBtn.getAttribute('data-post-type');
      const compId = awardBtn.getAttribute('data-competition-id');
      
      const titleInput = document.getElementById('award-post-title');
      const authorInput = document.getElementById('award-post-author');
      const noteInput = document.getElementById('award-note');
      const idInput = document.getElementById('award-post-id');
      const compSelect = document.getElementById('award-competition-id');
      const form = document.getElementById('award-form');
      
      if (titleInput) titleInput.value = title;
      if (authorInput) authorInput.value = author;
      
      if (compSelect) {
        compSelect.innerHTML = '<option value="">None</option>';
        if (window.allComps) {
          window.allComps.forEach(c => {
            if (c.category === type) {
              const opt = document.createElement('option');
              opt.value = c.id;
              opt.textContent = c.title;
              if (c.id == compId) opt.selected = true;
              compSelect.appendChild(opt);
            }
          });
        }
      }
      
      if (form) form.reset();
      
      if (idInput) idInput.value = postId;
      if (noteInput) noteInput.value = 'Points for: ' + title;
      
      if (typeof openModal === 'function') {
        openModal('award-modal');
      }
    }
  });

