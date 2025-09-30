<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= esc($title ?? 'Dashboard - LPHS SMS') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="<?= base_url('css/app.css') ?>" rel="stylesheet" />
  <link href="<?= base_url('css/dashboard.css') ?>" rel="stylesheet" />
</head>
<body class="dashboard-app">
  <!-- Sidebar -->
  <div class="app-sidebar-wrapper">
    <aside class="app-sidebar">
      <?php
        $portalLabel = 'Student Portal';
        $role = 'student';
        $dashboardUrl = base_url('student/dashboard');
        $accountPanelHref = base_url('student/profile');
        $notificationsHref = base_url('student/notifications');
        $navMain = [
          ['href' => base_url('student/dashboard'), 'icon' => 'bi-house', 'label' => 'Dashboard'],
          ['href' => base_url('student/grades'), 'icon' => 'bi-bar-chart-line', 'label' => 'My Grades'],
          ['href' => base_url('student/materials'), 'icon' => 'bi-folder', 'label' => 'Materials'],
          ['href' => base_url('student/announcements'), 'icon' => 'bi-megaphone', 'label' => 'Announcements'],

        ];
        try {
          $auth = auth();
          if ($auth->loggedIn()) {
            $user = $auth->user();
            if ($user->inGroup('admin')) {
              $role = 'admin';
              $portalLabel = 'Admin Portal';
              $dashboardUrl = base_url('admin/dashboard');
              $accountPanelHref = base_url('admin/users');
              $notificationsHref = base_url('admin/notifications');
              $navMain = [
                ['href' => base_url('admin/dashboard'), 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
                ['href' => base_url('admin/enrollments'), 'icon' => 'bi-person-check', 'label' => 'Enrollments'],
                ['href' => base_url('admin/students'), 'icon' => 'bi-people-fill', 'label' => 'Students'],
                ['href' => base_url('admin/teachers'), 'icon' => 'bi-person-video3', 'label' => 'Teachers'],
                ['href' => base_url('admin/sections'), 'icon' => 'bi-grid-3x3-gap', 'label' => 'Sections & Subjects'],
                ['href' => base_url('admin/analytics'), 'icon' => 'bi-graph-up', 'label' => 'Analytics'],
                ['href' => base_url('admin/announcements'), 'icon' => 'bi-megaphone', 'label' => 'Announcements'],
                ['href' => base_url('admin/notifications'), 'icon' => 'bi-bell', 'label' => 'Notifications'],
                ['href' => base_url('admin/password-resets'), 'icon' => 'bi-key', 'label' => 'Password Resets', 'badge' => 'password-reset-count'],
                ['href' => base_url('admin/users'), 'icon' => 'bi-people', 'label' => 'Users & Roles'],
              ];
            } elseif ($user->inGroup('teacher')) {
              $role = 'teacher';
              $portalLabel = 'Teacher Portal';
              $dashboardUrl = base_url('teacher/dashboard');
              $accountPanelHref = base_url('teacher/dashboard');
              $notificationsHref = base_url('teacher/announcements');
              $navMain = [
                ['href' => base_url('teacher/dashboard'), 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
                ['href' => base_url('teacher/students'), 'icon' => 'bi-people-fill', 'label' => 'My Students'],
                ['href' => base_url('teacher/grades'), 'icon' => 'bi-bar-chart-line', 'label' => 'Enter Grades'],
                ['href' => base_url('teacher/schedule'), 'icon' => 'bi-calendar-event', 'label' => 'My Schedule'],
                ['href' => base_url('teacher/announcements'), 'icon' => 'bi-megaphone', 'label' => 'Announcements'],
              ];
            } elseif ($user->inGroup('parent')) {
              $role = 'parent';
              $portalLabel = 'Parent Portal';
              $dashboardUrl = base_url('parent/dashboard');
              $accountPanelHref = base_url('parent/dashboard');
              $notificationsHref = base_url('parent/announcements');
              $navMain = [
                ['href' => base_url('parent/dashboard'), 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
                ['href' => base_url('parent/children'), 'icon' => 'bi-people', 'label' => 'My Children'],
                ['href' => base_url('parent/announcements'), 'icon' => 'bi-megaphone', 'label' => 'Announcements'],
              ];
            }
          } else {
            // In dev/test mode, default to student nav so pages are browsable
            if (defined('ENVIRONMENT') && ENVIRONMENT !== 'production') {
              $role = 'student';
            }
          }
        } catch (\Throwable $e) {
          // fall back to student defaults
        }
      ?>
      <!-- Sidebar Header -->
      <div class="app-sidebar-header">
        <a href="<?= $dashboardUrl ?>" class="d-flex align-items-center text-decoration-none">

          <div class="app-brand-text">
            <div class="app-brand-title">LPHS SMS</div>
            <div class="app-brand-subtitle"><?= esc($portalLabel) ?></div>
          </div>
        </a>
        <button class="app-sidebar-toggle d-lg-none" type="button" aria-label="Toggle sidebar">
          <i class="bi bi-list"></i>
        </button>
      </div>

      <!-- Sidebar Navigation -->
      <nav>
        <div class="app-sidebar-section-title">Main Navigation</div>
        <?php
          $currentPath = rtrim(parse_url(current_url(), PHP_URL_PATH) ?: '', '/');
        ?>
        <ul class="app-sidebar-menu">
          <?php foreach ($navMain as $item):
            $itemPath = rtrim(parse_url($item['href'], PHP_URL_PATH) ?: '', '/');
            $isActive = $itemPath !== '' && ($currentPath === $itemPath || str_starts_with($currentPath . '/', $itemPath . '/'));
          ?>
            <li>
              <a href="<?= $item['href'] ?>" class="app-sidebar-link<?= $isActive ? ' active' : '' ?>" data-label="<?= esc($item['label']) ?>"<?= $isActive ? ' aria-current="page"' : '' ?>>
                <i class="bi <?= esc($item['icon']) ?>"></i><span><?= esc($item['label']) ?></span>
                <?php if (isset($item['badge'])): ?>
                  <span class="badge bg-danger ms-auto" id="<?= $item['badge'] ?>" style="display: none;"></span>
                <?php endif; ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>

        <div class="app-sidebar-section-title">Quick Access</div>
        <ul class="app-sidebar-menu">
          <li><a href="<?= base_url('faq') ?>" class="app-sidebar-link" data-label="FAQ Chatbot"><i class="bi bi-question-circle"></i><span>FAQ Chatbot</span></a></li>
        </ul>

        <div class="app-sidebar-divider"></div>
        <div class="app-sidebar-section-title">Account</div>
        <ul class="app-sidebar-menu">
          <li><a href="<?= base_url('logout') ?>" class="app-sidebar-link" data-label="Logout"><i class="bi bi-box-arrow-left"></i><span>Logout</span></a></li>
        </ul>
      </nav>
    </aside>
  </div>

  <!-- Sidebar Backdrop (Mobile Only) -->
  <div class="app-sidebar-backdrop d-lg-none"></div>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Top Bar -->
    <div class="top-bar">
      <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
          <button class="app-sidebar-toggle d-lg-none me-3" type="button" aria-label="Toggle sidebar">
            <i class="bi bi-list"></i>
          </button>
          <button class="app-desktop-collapse d-none d-lg-inline-flex btn btn-outline-secondary btn-sm me-3" type="button" aria-label="Collapse sidebar">
            <i class="bi bi-layout-sidebar-inset"></i>
          </button>
          <?php $pageTitle = (string) ($title ?? 'Dashboard'); $parts = explode(' - ', $pageTitle, 2); ?>
          <h4 class="mb-0 top-bar-title">
            <span class="title-main"><?= esc($parts[0]) ?></span><?php if (!empty($parts[1])): ?> <span class="title-sub">- <?= esc($parts[1]) ?></span><?php endif; ?>
          </h4>
        </div>
        <div class="top-bar-actions">
          <a href="<?= $notificationsHref ?>" class="btn btn-outline-secondary btn-sm me-2">
            <i class="bi bi-bell"></i>
          </a>
          <a href="<?= $accountPanelHref ?>" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-person"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Page Content -->
    <main class="page-content">
      <div class="container-fluid py-4">
        <?= $this->renderSection('content') ?>
      </div>
    </main>

    <!-- Footer -->
    <footer class="modern-footer">
    <div class="footer-content">
      <div class="footer-brand">
        <img src="<?= base_url('LPHS2.png') ?>" alt="LPHS" style="width: 100px; height: 100px; margin-bottom: 15px;" />
        <h3>LPHS SMS</h3>
        <p>Empowering students with modern education management through innovative technology solutions.</p>
        <div class="social-links">
          <a href="#"><i class="bi bi-facebook"></i></a>
          <a href="#"><i class="bi bi-twitter"></i></a>
          <a href="#"><i class="bi bi-linkedin"></i></a>
          <a href="#"><i class="bi bi-instagram"></i></a>
        </div>
      </div>

      <div class="footer-links">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="<?= $dashboardUrl ?>">Dashboard</a></li>
          <li><a href="<?= base_url('faq') ?>">FAQ</a></li>
          <li><a href="#">Support</a></li>
          <li><a href="#">Help Center</a></li>
        </ul>
      </div>

      <div class="footer-resources">
        <h4>Resources</h4>
        <ul>
          <li><a href="#">Documentation</a></li>
          <li><a href="#">Training</a></li>
          <li><a href="#">System Status</a></li>
          <li><a href="#">API Guide</a></li>
        </ul>
      </div>

      <div class="footer-contact">
        <h4>Contact Info</h4>
        <div class="contact-item">
          <i class="bi bi-geo-alt"></i>
          <span>123 Education St, City</span>
        </div>
        <div class="contact-item">
          <i class="bi bi-telephone"></i>
          <span>(000) 123-4567</span>
        </div>
        <div class="contact-item">
          <i class="bi bi-envelope"></i>
          <span>info@lphs.edu</span>
        </div>
        <div class="contact-item">
          <i class="bi bi-clock"></i>
          <span>24/7 System Access</span>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="footer-bottom-left">
        <p>&copy; 2024 LPHS SMS. All rights reserved.</p>
        <span class="version">Version 1.0.0</span>
      </div>
      <div class="footer-bottom-right">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
        <a href="#">Security</a>
      </div>
    </div>
    </footer>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Load password reset count for admin users
    <?php if (auth()->user() && auth()->user()->inGroup('admin')): ?>
    function loadPasswordResetCount() {
      fetch('<?= base_url('admin/password-resets/count') ?>')
        .then(response => response.json())
        .then(data => {
          const badge = document.getElementById('password-reset-count');
          if (badge && data.count > 0) {
            badge.textContent = data.count;
            badge.style.display = 'inline-block';
          } else if (badge) {
            badge.style.display = 'none';
          }
        })
        .catch(error => console.error('Error loading password reset count:', error));
    }

    // Load count on page load
    document.addEventListener('DOMContentLoaded', loadPasswordResetCount);

    // Refresh count every 30 seconds
    setInterval(loadPasswordResetCount, 30000);
    <?php endif; ?>
  </script>
  <script>
    // Sidebar functionality (scoped to dashboard)
    document.addEventListener('DOMContentLoaded', function() {
      const sidebarWrapper = document.querySelector('.app-sidebar-wrapper');
      const mobileToggleButtons = document.querySelectorAll('.app-sidebar-toggle');
      const desktopCollapseBtn = document.querySelector('.app-desktop-collapse');
      const sidebarBackdrop = document.querySelector('.app-sidebar-backdrop');

      // Mobile sidebar toggle
      mobileToggleButtons.forEach(btn => {
        btn.addEventListener('click', function() {
          sidebarWrapper.classList.toggle('show');
          sidebarBackdrop.classList.toggle('show');
        });
      });

      // Close sidebar when clicking outside on mobile
      document.addEventListener('click', function(event) {
        if (window.innerWidth < 992) {
          if (!sidebarWrapper.contains(event.target) && !event.target.closest('.app-sidebar-toggle')) {
            sidebarWrapper.classList.remove('show');
            sidebarBackdrop.classList.remove('show');
          }
        }
      });

      // Handle window resize
      window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
          sidebarWrapper.classList.remove('show');
          sidebarBackdrop.classList.remove('show');
        }
      });

      // Desktop collapse toggle
      if (desktopCollapseBtn) {
        desktopCollapseBtn.addEventListener('click', function() {
          sidebarWrapper.classList.toggle('collapsed');
        });
      }

      // Set active sidebar link based on current page
      const currentPath = window.location.pathname;
      document.querySelectorAll('.app-sidebar-link').forEach(link => {
        try {
          const linkPath = new URL(link.href, window.location.origin).pathname;
          if (linkPath === currentPath) {
            link.classList.add('active');
          }
        } catch (e) {
          // ignore URL parse errors
        }
      });
    });
  </script>

  <!-- LPHS Floating Chatbot Widget (Dashboard) -->
  <style>
    .lphs-chat-button { position: fixed; right: 20px; bottom: 20px; width: 50px; height: 50px; border-radius: 50%; background-color: #fbbf24; color: #000; border: 0; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(251,191,36,0.3); cursor: pointer; z-index: 1055; transition: all 0.3s ease; }
    .lphs-chat-button:hover { transform: scale(1.1); box-shadow: 0 6px 20px rgba(251,191,36,0.4); }
    .lphs-chat-button i { font-size: 20px !important; }
    .lphs-chat-panel { position: fixed; right: 20px; bottom: 90px; width: 340px; max-height: 70vh; display: none; background: #fff; border-radius: 12px; box-shadow: 0 16px 48px rgba(0,0,0,.18); overflow: hidden; z-index: 1055; border: 1px solid rgba(0,0,0,.08); font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; }
    .lphs-chat-panel.open { display: flex; flex-direction: column; animation: lphs-slide-up .18s ease-out; }
    @keyframes lphs-slide-up { from { transform: translateY(8px); opacity: .0; } to { transform: translateY(0); opacity: 1; } }
    .lphs-chat-header { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 10px 12px; border-bottom: 1px solid rgba(0,0,0,.06); background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 70%, #fbbf24 100%); color: #fff; }
    .lphs-chat-header .title { display: flex; align-items: center; gap: 8px; font-weight: 600; font-size: 14px; }
    .lphs-chat-header .title .logo { width: 28px; height: 28px; border-radius: 8px; background: rgba(255,255,255,.15); display: grid; place-items: center; font-size: 16px; }
    .lphs-chat-messages { background: #f8f9fa; padding: 12px; overflow-y: auto; flex: 1; }
    .lphs-msg { display: flex; margin-bottom: 10px; }
    .lphs-msg.user { justify-content: flex-end; }
    .lphs-bubble { max-width: 82%; padding: 8px 10px; border-radius: 12px; font-size: 13px; line-height: 1.35; box-shadow: 0 1px 0 rgba(0,0,0,.04); white-space: pre-wrap; word-wrap: break-word; }
    .lphs-msg.user .lphs-bubble { background: #0d6efd; color: #fff; border-bottom-right-radius: 4px; }
    .lphs-msg.bot .lphs-bubble { background: #fff; color: #212529; border-bottom-left-radius: 4px; border: 1px solid rgba(0,0,0,.06); }
    .lphs-typing { display: inline-flex; align-items: center; gap: 3px; }
    .lphs-typing .dot { width: 6px; height: 6px; background: #6c757d; border-radius: 999px; opacity: .6; animation: lphs-bounce 1.2s infinite; }
    .lphs-typing .dot:nth-child(2) { animation-delay: .15s; }
    .lphs-typing .dot:nth-child(3) { animation-delay: .3s; }
    @keyframes lphs-bounce { 0%, 80%, 100% { transform: translateY(0); opacity: .4; } 40% { transform: translateY(-4px); opacity: 1; } }
    .lphs-quick { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px; }
    .lphs-quick button { border: 1px solid rgba(13,110,253,.35); background: rgba(13,110,253,.06); color: #0b5ed7; padding: 4px 8px; border-radius: 999px; font-size: 12px; }
    .lphs-chat-input { display: flex; gap: 8px; padding: 10px; border-top: 1px solid rgba(0,0,0,.06); background: #fff; }
    .lphs-chat-input input { flex: 1; border-radius: 8px; border: 1px solid rgba(0,0,0,.12); padding: 8px 10px; font-size: 13px; }
    .lphs-chat-input button { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: #fff; border: 0; border-radius: 8px; padding: 8px 12px; font-size: 13px; }
    @media (max-width: 420px){ .lphs-chat-panel{ right: 12px; left: 12px; width: auto; } .lphs-chat-button{ right: 12px; bottom: 12px; } }
    .lphs-answer { background:#ffffff; border:1px solid rgba(0,0,0,.06); border-radius:10px; padding:8px 10px; }
    .lphs-answer-title { font-weight:600; color:#0b5ed7; display:flex; align-items:center; gap:6px; margin-bottom:4px; font-size:12px; }
    .lphs-answer-body { font-size:13px; color:#212529; }
  </style>

  <button id="lphsChatBtn" class="lphs-chat-button" aria-label="Open LPHS AI Chatbot">
    <i class="bi bi-robot"></i>
  </button>

  <section id="lphsChatPanel" class="lphs-chat-panel" aria-live="polite" aria-label="LPHS FAQ Chatbot">
    <div class="lphs-chat-header">
      <div class="title">
        <span class="logo"><i class="bi bi-robot" style="font-size: 20px;"></i></span>
        <span>LPHS AI Assistant</span>
      </div>
      <div class="d-flex align-items-center gap-1">
        <button id="lphsChatClear" class="btn btn-sm btn-light" title="New chat"><i class="bi bi-stars"></i></button>
        <button id="lphsChatMin" class="btn btn-sm btn-light" title="Minimize"><i class="bi bi-dash-lg"></i></button>
      </div>
    </div>
    <div id="lphsChatMsgs" class="lphs-chat-messages"></div>
    <div class="lphs-chat-input">
      <input id="lphsChatInput" type="text" placeholder="Ask about enrollment, documents, grades…" />
      <button id="lphsChatSend" aria-label="Send"><i class="bi bi-send"></i></button>
    </div>
  </section>

  <script>
    (function(){
      const btn = document.getElementById('lphsChatBtn');
      const panel = document.getElementById('lphsChatPanel');
      const msgs = document.getElementById('lphsChatMsgs');
      const input = document.getElementById('lphsChatInput');
      const send = document.getElementById('lphsChatSend');
      const min = document.getElementById('lphsChatMin');
      const clearBtn = document.getElementById('lphsChatClear');

      // Prototype: local KB first; we'll wire to backend later
      const LOCAL_FAQ = [
        { q: 'how do i enroll', a: 'To enroll, go to Enrollment > Fill out the online form > Upload requirements > Submit. You will receive a confirmation email.' , cat: 'Enrollment' },
        { q: 'what documents do i need', a: 'Required documents: Birth Certificate, Form 138/Report Card, Good Moral Certificate, 2x2 ID Photo, and Proof of Residency.' , cat: 'Requirements' },
        { q: 'when is the enrollment period', a: 'Enrollment period runs from May 15 to June 30. Late enrollment may be accommodated subject to availability.' , cat: 'Schedule' },
        { q: 'how do i check my grades', a: 'Log in to your Student Portal > Grades section. Select the term to view detailed grades.' , cat: 'Grades' },
        { q: 'school hours', a: 'Classes run from 7:30 AM to 4:30 PM, Monday to Friday. Some programs may vary by section.', cat: 'General' },
        { q: 'payment', a: 'Payments can be made at the cashier or via the online payment portal. Keep your transaction receipt.', cat: 'Finance' },
        { q: 'hi', a: 'Hi! Ask me about enrollment, requirements, schedules, or grades. You can also tap a quick question below.', cat: 'Greeting' },
        { q: 'hello', a: 'Hello! I can help with FAQs like enrollment steps, documents, enrollment period, or checking grades.', cat: 'Greeting' }
      ];

      const quick = [ 'How do I enroll?', 'What documents do I need?', 'When is the enrollment period?', 'How do I check my grades?' ];

      function escapeHtml(s){
        return s.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c]));
      }

      function bubble(html, who){
        const wrap = document.createElement('div');
        wrap.className = 'lphs-msg ' + (who === 'user' ? 'user' : 'bot');
        const b = document.createElement('div');
        b.className = 'lphs-bubble';
        b.innerHTML = html;
        wrap.appendChild(b);
        msgs.appendChild(wrap);
        msgs.scrollTop = msgs.scrollHeight;
        return wrap;
      }

      function showTyping(){
        const el = bubble('<span class="lphs-typing"><span class="dot"></span><span class="dot"></span><span class="dot"></span></span>','bot');
        el.dataset.typing = '1';
        return el;
      }

      function welcome(){
        msgs.innerHTML = '';
        bubble('<strong>Hello! I\'m the LPHS FAQ bot.</strong><br/><span class="text-muted">Try one of these quick questions:</span>','bot');
        const qwrap = document.createElement('div'); qwrap.className = 'lphs-msg bot';
        const b = document.createElement('div'); b.className = 'lphs-bubble';
        const row = document.createElement('div'); row.className = 'lphs-quick';
        quick.forEach(q => { const qb = document.createElement('button'); qb.type = 'button'; qb.textContent = q; qb.addEventListener('click', () => { input.value = q; doAsk(); }); row.appendChild(qb); });
        b.appendChild(row); qwrap.appendChild(b); msgs.appendChild(qwrap);
      }

      function localAnswer(query){
        const qn = query.toLowerCase().replace(/\s+/g,' ').trim();
        let best = null; let score = 0;
        for (const item of LOCAL_FAQ){
          const t = item.q;
          if (qn === t) { best = item; score = 100; break; }
          const s = (qn.includes(t) ? t.length : 0);
          if (s > score){ best = item; score = s; }
        }
        return best;
      }

      function renderAnswer(a){
        const html = '<div class="lphs-answer">'
          +'<div class="lphs-answer-title"><i class="bi bi-chat-square-quote"></i> '+escapeHtml(a.cat||'FAQ')+'</div>'
          +'<div class="lphs-answer-body">'+escapeHtml(a.a)+'</div>'
          +'</div>';
        bubble(html, 'bot');
      }

      function doAsk(){
        const q = input.value.trim();
        if (!q) { input.focus(); return; }
        bubble(escapeHtml(q), 'user');
        input.value = '';
        const t = showTyping();
        const local = localAnswer(q);
        setTimeout(() => {
          t.remove();
          if (local){
            renderAnswer(local);
          } else {
            bubble('<span class="text-muted">I don\'t have that in my FAQ yet. Please try another phrasing.</span>', 'bot');
          }
        }, 500);
        // backend call disabled for prototype
      }

      btn.addEventListener('click', () => {
        panel.classList.toggle('open');
        if (panel.classList.contains('open')){
          if (!msgs.dataset.init){ msgs.dataset.init = '1'; welcome(); }
          setTimeout(() => input.focus(), 150);
        }
      });
      min.addEventListener('click', () => panel.classList.remove('open'));
      if (clearBtn) clearBtn.addEventListener('click', () => { msgs.dataset.init = ''; welcome(); input.focus(); });
      send.addEventListener('click', doAsk);
      input.addEventListener('keydown', e => { if (e.key === 'Enter') doAsk(); });

      // Trim history: keep last 50 nodes
      const observer = new MutationObserver(() => {
        const nodes = msgs.querySelectorAll('.lphs-msg');
        if (nodes.length > 50) {
          for (let i = 0; i < nodes.length - 50; i++) nodes[i].remove();
        }
      });
      observer.observe(msgs, { childList: true });
    })();
  </script>

</body>
</html>


