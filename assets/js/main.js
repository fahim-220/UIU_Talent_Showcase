/* ========================================
   UIU TALENT SHOW — SCRIPT
   ======================================== */

// ---- Like toggle ----
async function likePost(btn) {
  if (!window.isLoggedIn) {
    showToast('⚠️ Please log in to like', 'warn');
    setTimeout(() => window.location.href = window.loginUrl, 1500);
    return;
  }

  const postId = btn.getAttribute('data-id');
  if (!postId) return;

  btn.disabled = true;

  try {
    const res = await fetch('api/like.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ post_id: postId, csrf_token: window.csrfToken })
    });
    
    let result;
    try {
      result = await res.json();
    } catch (e) {
      console.error(e);
      showToast('❌ Unexpected server response', 'error');
      return;
    }
    
    if (result.success) {
      const countEl = btn.querySelector('span');
      const icon = btn.querySelector('i');
      
      if (countEl) countEl.textContent = result.count;
      if (result.liked) {
        btn.classList.add('liked');
        if (icon) icon.className = 'fas fa-heart';
        createHeartParticle(btn);
      } else {
        btn.classList.remove('liked');
        if (icon) icon.className = 'far fa-heart';
      }
    } else {
      showToast('❌ ' + result.message, 'error');
      if (res.status === 401) {
        setTimeout(() => window.location.href = window.loginUrl, 1500);
      }
    }
  } catch (err) {
    console.error(err);
    showToast('❌ Network error', 'error');
  } finally {
    btn.disabled = false;
  }
}

// ---- Heart particle effect ----
function createHeartParticle(btn) {
  const particle = document.createElement('span');
  particle.textContent = '❤️';
  particle.style.cssText = `
    position: fixed;
    pointer-events: none;
    font-size: 1.4rem;
    z-index: 9999;
    animation: heartFloat 0.8s ease forwards;
  `;

  const rect = btn.getBoundingClientRect();
  particle.style.left = rect.left + rect.width / 2 + 'px';
  particle.style.top = rect.top + 'px';
  document.body.appendChild(particle);

  setTimeout(() => particle.remove(), 800);
}

// Add animation to document
const style = document.createElement('style');
style.textContent = `
  @keyframes heartFloat {
    0% { transform: translateY(0) scale(1); opacity: 1; }
    100% { transform: translateY(-60px) scale(1.5); opacity: 0; }
  }
`;
document.head.appendChild(style);

// ---- Comments toggle ----
function openComments(btn) {
  const card = btn.closest('.media-card, .blog-card');
  const panel = card.querySelector('.comments-panel');

  if (panel.classList.contains('hidden')) {
    panel.classList.remove('hidden');
    btn.classList.add('active');
    btn.querySelector('i').className = 'fas fa-comment';
  } else {
    panel.classList.add('hidden');
    btn.querySelector('i').className = 'far fa-comment';
  }
}

// ---- Add comment ----
async function addComment(sendBtn) {
  if (!window.isLoggedIn) {
    showToast('⚠️ Please log in to comment', 'warn');
    setTimeout(() => window.location.href = window.loginUrl, 1500);
    return;
  }

  const row = sendBtn.closest('.comment-input-row');
  if (!row) return;
  const input = row.querySelector('.comment-input');
  if (!input) return;
  const text = input.value.trim();
  if (!text) return;

  const postId = sendBtn.getAttribute('data-id');
  if (!postId) return;

  sendBtn.disabled = true;
  const originalHtml = sendBtn.innerHTML;
  sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

  try {
    const res = await fetch('api/comment.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ post_id: postId, body: text, csrf_token: window.csrfToken })
    });
    
    let result;
    try {
      result = await res.json();
    } catch (e) {
      console.error(e);
      showToast('❌ Unexpected server response', 'error');
      return;
    }

    if (result.success) {
      const panel = sendBtn.closest('.comments-panel');
      const list = panel ? panel.querySelector('.comment-list') : null;
      
      const cardContainer = sendBtn.closest('.media-card, .blog-card');
      const cardFooter = cardContainer ? cardContainer.querySelector('.card-footer') : null;
      const commentCountSpan = cardFooter ? cardFooter.querySelector('.comment-btn .comment-count-text') : null;

      const c = result.comment;
      
      if (list) {
        const commentEl = document.createElement('div');
        commentEl.className = 'comment';
        
        let avatarHtml = '';
        if (c.avatar) {
          avatarHtml = `<img src="${c.avatar}" class="c-avatar" alt="avatar" />`;
        } else {
          avatarHtml = `<div class="c-avatar" style="background: var(--accent2); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 10px; width: 24px; height: 24px; border-radius: 50%;">${c.initials}</div>`;
        }

        const divBody = document.createElement('div');
        const strong = document.createElement('strong');
        strong.textContent = c.author_name;
        const p = document.createElement('p');
        
        const lines = c.body.split('\n');
        lines.forEach((line, i) => {
          p.appendChild(document.createTextNode(line));
          if (i < lines.length - 1) {
            p.appendChild(document.createElement('br'));
          }
        });
        
        divBody.appendChild(strong);
        divBody.appendChild(p);

        commentEl.innerHTML = avatarHtml;
        commentEl.appendChild(divBody);
        list.appendChild(commentEl);
      }
      
      input.value = '';
      
      if (commentCountSpan) {
        commentCountSpan.textContent = result.count;
      }
    } else {
      showToast('❌ ' + result.message, 'error');
      if (res.status === 401) {
        setTimeout(() => window.location.href = window.loginUrl, 1500);
      }
    }
  } catch (err) {
    console.error(err);
    showToast('❌ Network error', 'error');
  } finally {
    sendBtn.disabled = false;
    sendBtn.innerHTML = originalHtml;
  }
}

// ---- Toast ----
let toastTimer;
function showToast(msg, type = 'success') {
  const toast = document.getElementById('toast');
  if (!toast) return;
  toast.textContent = msg;
  toast.className = 'toast';
  toast.classList.add('type-' + type);
  toast.classList.remove('hidden');

  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => {
    toast.classList.add('hidden');
  }, 3000);
}

// ---- Navbar mobile toggle ----
function toggleMenu() {
  document.body.classList.toggle('nav-open');
}

function toggleTalentMenu(event) {
  event.stopPropagation();
  const dropdown = event.currentTarget.closest('.nav-dropdown');
  document.querySelectorAll('.nav-dropdown.open').forEach(item => {
    if (item !== dropdown) item.classList.remove('open');
  });
  dropdown.classList.toggle('open');
}

document.addEventListener('click', () => {
  document.querySelectorAll('.nav-dropdown.open').forEach(dropdown => dropdown.classList.remove('open'));
});

// Close nav on link click
document.querySelectorAll('.nav-link').forEach(link => {
  link.addEventListener('click', () => {
    document.body.classList.remove('nav-open');
    // Update active nav link
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    link.classList.add('active');
  });
});

// ---- Smooth active nav on scroll ----
const sections = ['home', 'video', 'audio', 'blog', 'leaderboard'];

window.addEventListener('scroll', () => {
  const scrollY = window.scrollY + 100;
  sections.forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;
    if (scrollY >= el.offsetTop && scrollY < el.offsetTop + el.offsetHeight) {
      document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
      const active = document.querySelector(`.nav-link[href="#${id}"]`);
      if (active) active.classList.add('active');
    }
  });
});

// ---- File drag & drop ----
const fileDrop = document.querySelector('.file-drop');
if (fileDrop) {
  fileDrop.addEventListener('dragover', e => {
    e.preventDefault();
    fileDrop.style.borderColor = 'var(--accent)';
    fileDrop.style.background = 'rgba(124,107,255,0.08)';
  });

  fileDrop.addEventListener('dragleave', () => {
    fileDrop.style.borderColor = '';
    fileDrop.style.background = '';
  });

  fileDrop.addEventListener('drop', e => {
    e.preventDefault();
    fileDrop.style.borderColor = '';
    fileDrop.style.background = '';
    const files = e.dataTransfer.files;
    if (files.length > 0) {
      fileDrop.innerHTML = `<i class="fas fa-check-circle" style="color:var(--green)"></i>
        <p>${files.length} file(s) selected</p>
        <span>${Array.from(files).map(f => f.name).join(', ')}</span>`;
    }
  });

  document.getElementById('file-input').addEventListener('change', function () {
    if (this.files.length > 0) {
      fileDrop.innerHTML = `<i class="fas fa-check-circle" style="color:var(--green)"></i>
        <p>${this.files.length} file(s) selected</p>
        <span>${Array.from(this.files).map(f => f.name).join(', ')}</span>`;
    }
  });
}

// ---- Toast type colors ----
const toastStyle = document.createElement('style');
toastStyle.textContent = `
  .toast.type-error { border-left: 3px solid #f87171; }
  .toast.type-warn { border-left: 3px solid var(--gold); }
  .toast.type-success { border-left: 3px solid var(--green); }
`;
document.head.appendChild(toastStyle);

function escapeHtml(str) {
  const div = document.createElement('div');
  div.appendChild(document.createTextNode(str));
  return div.innerHTML;
}

// ---- Modal ----
function openModal(id) {
  const modal = document.getElementById(id);
  if (!modal) {
    console.warn("Modal not found: " + id);
    return;
  }
  modal.classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}

function closeModal(id) {
  const modal = document.getElementById(id);
  if (!modal) return;
  modal.classList.add('hidden');
  document.body.style.overflow = '';
}

// Close on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', function (e) {
    if (e.target === this) closeModal(this.id);
  });
});

// Close on Escape
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-overlay:not(.hidden)').forEach(m => closeModal(m.id));
  }
});

// ---- Upload category selection ----
function selectCat(btn, cat) {
  document.querySelectorAll('.cat-option').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  
  document.getElementById('upload-type').value = cat;
  const fileGroup = document.getElementById('upload-file-group');
  const coverGroup = document.getElementById('upload-cover-group');
  const hint = document.getElementById('upload-file-hint');
  const fileInput = document.getElementById('file-input');
  
  if (cat === 'video') {
    fileGroup.classList.remove('hidden');
    coverGroup.classList.add('hidden');
    hint.textContent = 'Video: MP4, WEBM (Max 100MB)';
    fileInput.accept = '.mp4,.webm';
  } else if (cat === 'audio') {
    fileGroup.classList.remove('hidden');
    coverGroup.classList.add('hidden');
    hint.textContent = 'Audio: MP3, WAV, OGG, M4A (Max 20MB)';
    fileInput.accept = '.mp3,.wav,.ogg,.m4a';
  } else if (cat === 'text') {
    fileGroup.classList.add('hidden');
    coverGroup.classList.remove('hidden');
  }
}

// ---- Submit entry ----
async function submitEntry() {
  const form = document.getElementById('upload-form');
  if (!form) return;
  
  const title = document.getElementById('upload-title').value.trim();
  const desc = document.getElementById('upload-description').value.trim();
  const type = document.getElementById('upload-type').value;
  const fileInput = document.getElementById('file-input');
  
  if (!title) {
    showToast('⚠️ Title is required.', 'warn');
    return;
  }
  if (type === 'text' && !desc) {
    showToast('⚠️ Description is required for text posts.', 'warn');
    return;
  }
  if ((type === 'video' || type === 'audio') && fileInput.files.length === 0) {
    showToast('⚠️ File is required.', 'warn');
    return;
  }
  
  const btn = document.getElementById('upload-submit-btn');
  const originalText = btn.innerHTML;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
  btn.disabled = true;
  
  const formData = new FormData(form);
  
  try {
    const res = await fetch('api/upload.php', {
      method: 'POST',
      body: formData
    });
    
    let result;
    try {
      result = await res.json();
    } catch (e) {
      result = { success: false, message: 'Server returned an invalid response.' };
    }
    
    if (result.success) {
      showToast('🎉 ' + result.message);
      form.reset();
      if (result.redirect) {
        window.location.href = result.redirect;
      } else {
        closeModal('upload-modal');
      }
    } else {
      showToast('❌ ' + result.message, 'error');
    }
  } catch (err) {
    showToast('❌ Network error occurred.', 'error');
  } finally {
    btn.innerHTML = originalText;
    btn.disabled = false;
  }
}

// ---- Competitions Filter ----
document.addEventListener('DOMContentLoaded', () => {
  const filterBtns = document.querySelectorAll('.competition-filter .filter-btn');
  if (filterBtns.length === 0) return;
  
  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      // Update active state
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      
      const filter = btn.getAttribute('data-filter');
      const items = document.querySelectorAll('.comp-item');
      let count = 0;
      
      items.forEach(item => {
        if (filter === 'all' || item.getAttribute('data-category') === filter) {
          item.style.display = '';
          count++;
        } else {
          item.style.display = 'none';
        }
      });
      
      const msg = document.getElementById('empty-filter-msg');
      if (msg) {
        if (count === 0) {
          msg.classList.remove('hidden');
        } else {
          msg.classList.add('hidden');
        }
      }
    });
  });
});

// ---- Competitions Join ----
function openJoinModal(compId, category, title) {
  const modal = document.getElementById('join-modal');
  if (!modal) return;
  
  document.getElementById('join-comp-id').value = compId;
  document.getElementById('join-comp-title').textContent = title;
  
  const select = document.getElementById('join-post-select');
  if (select) {
    select.value = "";
    const options = select.querySelectorAll('option');
    options.forEach(opt => {
      if (!opt.value) {
        opt.style.display = 'block'; // No post option
      } else if (opt.getAttribute('data-type') === category) {
        opt.style.display = 'block';
      } else {
        opt.style.display = 'none';
      }
    });
  }
  
  openModal('join-modal');
}

async function submitJoin() {
  const form = document.getElementById('join-form');
  if (!form) return;
  
  const btn = document.getElementById('join-submit-btn');
  const originalHtml = btn.innerHTML;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
  btn.disabled = true;
  
  const formData = new FormData(form);
  
  try {
    const res = await fetch('api/join_competition.php', {
      method: 'POST',
      body: formData
    });
    
    let result;
    try {
      result = await res.json();
    } catch (e) {
      console.error(e);
      showToast('❌ Unexpected server response', 'error');
      return;
    }
    
    if (result.success) {
      showToast('🎉 ' + result.message);
      closeModal('join-modal');
      
      // Update UI: Find the card, update button and count
      const compId = formData.get('competition_id');
      const cards = document.querySelectorAll('.comp-item');
      cards.forEach(card => {
        const regBtn = card.querySelector('.btn-primary');
        if (regBtn && regBtn.getAttribute('onclick') && regBtn.getAttribute('onclick').includes(`openJoinModal(${compId}`)) {
          regBtn.outerHTML = `<button class="btn-primary" disabled style="background: var(--surface2); color: var(--accent); border: 1px solid var(--accent);"><i class="fas fa-check"></i> Registered</button>`;
          const countSpan = card.querySelector('.entry-count-text');
          if (countSpan && result.entries !== undefined) {
            countSpan.textContent = result.entries;
          }
        }
      });
      
    } else {
      showToast('❌ ' + result.message, 'error');
    }
  } catch (err) {
    console.error(err);
    showToast('❌ Network error', 'error');
  } finally {
    btn.innerHTML = originalHtml;
    btn.disabled = false;
  }
}

