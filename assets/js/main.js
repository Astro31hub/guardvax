// GuardVAX — Main JavaScript
document.addEventListener('DOMContentLoaded', () => {

  // Page title in topbar
  const h1 = document.querySelector('.page-title');
  const pt = document.getElementById('pageTitle');
  if (h1 && pt) {
    const clone = h1.cloneNode(true);
    clone.querySelectorAll('i').forEach(i => i.remove());
    pt.textContent = clone.textContent.trim();
  }

  // Sidebar overlay (mobile)
  const overlay = document.createElement('div');
  overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1049;display:none';
  document.body.appendChild(overlay);

  const sidebar = document.getElementById('gvxSidebar');
  overlay.addEventListener('click', () => {
    sidebar?.classList.remove('show');
    overlay.style.display = 'none';
  });

  const toggleBtn = document.querySelector('.sidebar-toggle');
  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', () => {
      const open = sidebar.classList.toggle('show');
      overlay.style.display = open ? 'block' : 'none';
    });
  }

  // Auto-dismiss alerts after 5s
  document.querySelectorAll('.alert.fade.show').forEach(el => {
    setTimeout(() => bootstrap.Alert.getOrCreateInstance(el)?.close(), 5000);
  });

  // Active nav link
  const currentPath = window.location.pathname;
  document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
    const href = link.getAttribute('href') || '';
    if (href && currentPath.includes(href.split('?')[0])) {
      link.classList.add('active');
    }
  });

  // Number counter animation
  function animateCounter(el) {
    const target = parseInt(el.textContent.replace(/,/g, ''), 10);
    if (isNaN(target) || target > 9999) return;
    let current = 0;
    const step = Math.max(1, Math.ceil(target / 40));
    const timer = setInterval(() => {
      current = Math.min(current + step, target);
      el.textContent = current.toLocaleString();
      if (current >= target) clearInterval(timer);
    }, 20);
  }

  if ('IntersectionObserver' in window) {
    const obs = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) { animateCounter(entry.target); obs.unobserve(entry.target); }
      });
    }, { threshold: 0.5 });
    document.querySelectorAll('.stat-value').forEach(el => obs.observe(el));
  }

  // Tooltip init
  document.querySelectorAll('[title]').forEach(el => {
    new bootstrap.Tooltip(el, { trigger: 'hover' });
  });
});
