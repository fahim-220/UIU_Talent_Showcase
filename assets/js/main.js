/* ========================================
   UIU TALENT SHOW — SCRIPT
   ======================================== */

// ---- Like toggle ----
function likePost(btn) {
  const countEl = btn.querySelector('span');
  let count = parseInt(countEl.textContent);

  if (btn.classList.contains('liked')) {
    btn.classList.remove('liked');
    btn.querySelector('i').className = 'far fa-heart';
    countEl.textContent = count - 1;
  } else {
    btn.classList.add('liked');
    btn.querySelector('i').className = 'fas fa-heart';
    countEl.textContent = count + 1;
    createHeartParticle(btn);
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
function addComment(sendBtn) {
  const row = sendBtn.closest('.comment-input-row');
  const input = row.querySelector('.comment-input');
  const text = input.value.trim();
  if (!text) return;

  const list = sendBtn.closest('.comments-panel').querySelector('.comment-list');
  const randId = Math.floor(Math.random() * 70) + 1;

  const commentEl = document.createElement('div');
  commentEl.className = 'comment';
  commentEl.innerHTML = `
    <img src="https://i.pravatar.cc/24?img=${randId}" class="c-avatar" alt="you" />
    <div>
      <strong>You</strong>
      <p>${escapeHtml(text)}</p>
    </div>
  `;

  list.appendChild(commentEl);
  input.value = '';

  // Update comment count
  const commentBtn = sendBtn.closest('.card-body, .blog-card').querySelector('.comment-btn span');
  if (commentBtn) commentBtn.textContent = parseInt(commentBtn.textContent) + 1;

  showToast('💬 Comment posted!');
}

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
}

// ---- Submit entry ----
function submitEntry() {
  const nameInput = document.querySelector('#upload-modal .form-input');
  const name = nameInput.value.trim();

  if (!name) {
    showToast('⚠️ Please enter your name', 'warn');
    nameInput.focus();
    return;
  }

  closeModal('upload-modal');
  showToast('🎉 Entry submitted! Awaiting review.');

  // Reset form
  document.querySelectorAll('#upload-modal .form-input').forEach(i => i.value = '');
  document.querySelectorAll('#upload-modal .cat-option').forEach((b, idx) => {
    b.classList.toggle('active', idx === 0);
  });
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
