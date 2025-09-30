<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= esc($title ?? 'LPHS School Management System') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="<?= base_url('css/app.css') ?>" rel="stylesheet" />
</head>
<body>
  <header class="site-header py-2">
    <div class="container-fluid d-flex justify-content-between align-items-center px-4" style="height:64px">
      <?php
        $brandHref = base_url();
        if (!empty($loggedIn) && $loggedIn) {
          $brandHref = base_url('dashboard');
        }
      ?>
      <a class="site-brand flex-shrink-0" href="<?= $brandHref ?>" aria-label="LPHS SMS Home" style="margin-right: 2rem;">
        <img src="<?= base_url('LPHS2.png') ?>" alt="LPHS" width="64" height="64" />
        <span style="font-family: 'Times New Roman', serif; font-weight: bold; font-size: 1.8rem; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #ea580c 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; white-space: nowrap; position: relative;">Lourdes Provincial High School<span style="position: absolute; bottom: 0px; left: 0; right: 0; height: 1px; background: white;"></span><span style="position: absolute; bottom: -2px; left: 0; right: 0; height: 1px; background: white;"></span></span>
      </a>
      <button class="nav-toggle d-md-none" type="button" aria-label="Toggle navigation" onclick="document.querySelector('.site-nav').classList.toggle('open')">☰</button>
      <nav class="site-nav ms-auto">
        <?php
          $loggedIn = false;
          $user = null;
          try {
            $authService = auth();
            $loggedIn = $authService->loggedIn();
            if ($loggedIn) {
              $user = $authService->user();
            }
          } catch (\Throwable $e) {
            $loggedIn = false; // DB not configured yet, fall back to public nav
          }

          // Get current path for active navigation highlighting
          $currentPath = rtrim(parse_url(current_url(), PHP_URL_PATH) ?: '', '/');
          $currentSegment = trim(str_replace(rtrim(parse_url(base_url(), PHP_URL_PATH) ?: '', '/'), '', $currentPath), '/');
          if (empty($currentSegment)) {
            $currentSegment = 'home';
          }
        ?>
        <?php if ($loggedIn): ?>
          <span class="text-white">Welcome, <?= esc($user->email) ?></span>

          <?php if ($user && $user->inGroup('admin')): ?>
            <a href="<?= base_url('admin/dashboard') ?>" class="text-white text-decoration-none me-3">Dashboard</a>
          <?php elseif ($user && $user->inGroup('teacher')): ?>
            <a href="<?= base_url('teacher/dashboard') ?>" class="text-white text-decoration-none me-3">Dashboard</a>
          <?php elseif ($user && $user->inGroup('student')): ?>
            <a href="<?= base_url('student/dashboard') ?>" class="text-white text-decoration-none me-3">Dashboard</a>
          <?php elseif ($user && $user->inGroup('parent')): ?>
            <a href="<?= base_url('parent/dashboard') ?>" class="text-white text-decoration-none me-3">Dashboard</a>
          <?php endif; ?>

          <a href="<?= base_url('announcements') ?>" class="text-white text-decoration-none me-3">Announcements</a>
          <a href="<?= base_url('faq') ?>" class="text-white text-decoration-none me-3">FAQ</a>
          <a href="<?= base_url('logout') ?>" class="text-white text-decoration-none">Logout</a>
        <?php else: ?>
          <a href="<?= base_url() ?>" class="text-white text-decoration-none me-3<?= $currentSegment === 'home' ? ' active' : '' ?>">Home</a>
          <a href="<?= base_url() ?>#enrollment-process" class="text-white text-decoration-none me-3<?= $currentSegment === 'enrollment' ? ' active' : '' ?>">Enrollment</a>
          <a href="<?= base_url() ?>#analytics-section" class="text-white text-decoration-none me-3<?= $currentSegment === 'analytics' ? ' active' : '' ?>">Analytics</a>
          <a href="<?= base_url() ?>#about-section" class="text-white text-decoration-none me-3<?= $currentSegment === 'about' ? ' active' : '' ?>">About</a>
          <a href="<?= base_url('login') ?>" class="text-white text-decoration-none me-3<?= $currentSegment === 'login' ? ' active' : '' ?>">Login</a>
          <a href="<?= base_url('register') ?>" class="text-white text-decoration-none btn btn-accent<?= $currentSegment === 'register' ? ' active' : '' ?>">Enroll</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main>
    <?= $this->renderSection('content') ?>
  </main>

  <footer id="footer" class="site-footer py-3 mt-5">
    <div class="container-fluid px-5">
      <div class="footer-horizontal">
        <div class="footer-brand-compact">
          <div class="brand-info">
            <img src="<?= base_url('LPHS2.png') ?>" alt="LPHS" style="width: 80px; height: 80px; margin-right: 15px;" />
            <span class="brand-name" style="margin-left: 20px;">Lourdes Provincial<br>High School</span>
          </div>
          <div class="contact-compact">
            <span><i class="bi bi-geo-alt"></i> Barangay Lourdes, Panglao Town, Bohol, Philippines</span>
            <span><i class="bi bi-envelope"></i> info@lphs.edu.ph</span>
            <span><i class="bi bi-telephone"></i> +63 38 502 9000</span>
            <span><i class="bi bi-clock"></i> Mon-Fri 7AM-5PM</span>
          </div>
        </div>

        <div class="footer-links-horizontal">
          <div class="link-section">
            <strong><i class="bi bi-link"></i> Quick Links:</strong>
            <a href="<?= base_url() ?>"><i class="bi bi-house"></i> Home</a>
            <a href="<?= base_url('enrollment') ?>"><i class="bi bi-file-plus"></i> Enrollment</a>
            <a href="<?= base_url('about') ?>"><i class="bi bi-info-circle"></i> About</a>
            <a href="<?= base_url('announcements') ?>"><i class="bi bi-megaphone"></i> News</a>

          </div>

          <div class="link-section">
            <strong><i class="bi bi-mortarboard"></i> Programs:</strong>
            <a href="#"><i class="bi bi-calculator"></i> STEM</a>
            <a href="#"><i class="bi bi-briefcase"></i> ABM</a>
            <a href="#"><i class="bi bi-people"></i> HUMSS</a>
            <a href="#"><i class="bi bi-tools"></i> TVL</a>
            <a href="#"><i class="bi bi-palette"></i> Arts</a>
            <a href="#"><i class="bi bi-cpu"></i> ICT</a>
          </div>

          <div class="link-section">
            <strong><i class="bi bi-gear"></i> Services:</strong>
            <a href="#"><i class="bi bi-person-plus"></i> Online Enrollment</a>
            <a href="#"><i class="bi bi-file-text"></i> Grade Inquiry</a>
            <a href="#"><i class="bi bi-credit-card"></i> Payment Portal</a>
            <a href="#"><i class="bi bi-book"></i> Library</a>
            <a href="#"><i class="bi bi-heart-pulse"></i> Health</a>
            <a href="#"><i class="bi bi-shield-check"></i> Guidance</a>
          </div>

          <div class="link-section">
            <strong><i class="bi bi-collection"></i> Resources:</strong>
            <a href="#"><i class="bi bi-download"></i> Forms</a>
            <a href="#"><i class="bi bi-calendar"></i> Calendar</a>
            <a href="#"><i class="bi bi-journal"></i> Handbook</a>
            <a href="#"><i class="bi bi-map"></i> Campus Map</a>
            <a href="#"><i class="bi bi-shield-lock"></i> Privacy Policy</a>
          </div>
        </div>

        <div class="footer-info-compact">

          <div class="footer-badges-compact">
            <span class="badge bg-success"><i class="bi bi-shield-check"></i> Secure</span>
            <span class="badge bg-info"><i class="bi bi-cloud"></i> Cloud-Based</span>
            <span class="badge bg-warning"><i class="bi bi-lightning"></i> Fast</span>
            <span class="badge bg-primary"><i class="bi bi-phone"></i> Mobile Ready</span>
          </div>
        </div>
      </div>

      <div class="footer-bottom-compact">
        <div class="copyright-compact">
          <i class="bi bi-c-circle"></i> <?= date('Y') ?> Lourdes Provincial High School. All rights reserved.
        </div>
        <div class="system-compact">
          <i class="bi bi-gear"></i> Enrollment Management System | v2.1.0 | CodeIgniter 4
        </div>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Navigation Slide Down Effect -->
  <style>
    html { scroll-behavior: smooth; }
    .site-nav a:not(.btn):not([href*="dashboard"]):not([href*="logout"]):not([href*="announcements"]):not([href*="faq"]) {
      position: relative;
      transition: transform 0.3s ease;
    }
    .site-nav a:not(.btn):not([href*="dashboard"]):not([href*="logout"]):not([href*="announcements"]):not([href*="faq"]):hover {
      transform: translateY(3px);
    }
  </style>

  <!-- LPHS Floating Chatbot Widget -->
  <style>
    .lphs-chat-button { position: fixed; right: 20px; bottom: 20px; width: 50px; height: 50px; border-radius: 50%; background-color: #1e3a8a; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.15); color: white; display: flex; align-items: center; justify-content: center; font-size: 20px; transition: all 0.3s ease; }
    .lphs-chat-button:hover { transform: scale(1.1); box-shadow: 0 6px 20px rgba(0,0,0,0.2); }ackground-color: #1e3a8a; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.15); color: white; display: flex; align-items: center; justify-content: center; font-size: 24px; transition: all 0.3s ease; }
    .lphs-chat-button:hover { transform: scale(1.1); box-shadow: 0 6px 20px rgba(0,0,0,0.2); } background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: #fff; border: 0; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(251,191,36,.5); cursor: pointer; z-index: 1055; transition: all 0.3s ease; }
    .lphs-chat-button:hover { transform: scale(1.1); box-shadow: 0 12px 32px rgba(251,191,36,.6); }
    .lphs-chat-button i { font-size: 42px !important; }
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
    .lphs-answer { background:#ffffff; border:1px solid rgba(0,0,0,.06); border-radius:10px; padding:8px 10px; }
    .lphs-answer-title { font-weight:600; color:#0b5ed7; display:flex; align-items:center; gap:6px; margin-bottom:4px; font-size:12px; }
    .lphs-answer-body { font-size:13px; color:#212529; }
    .lphs-chat-input { display: flex; gap: 8px; padding: 10px; border-top: 1px solid rgba(0,0,0,.06); background: #fff; }
    .lphs-chat-input input { flex: 1; border-radius: 8px; border: 1px solid rgba(0,0,0,.12); padding: 8px 10px; font-size: 13px; }
    .lphs-answer { background:#ffffff; border:1px solid rgba(0,0,0,.06); border-radius:10px; padding:8px 10px; }
    .lphs-answer-title { font-weight:600; color:#0b5ed7; display:flex; align-items:center; gap:6px; margin-bottom:4px; font-size:12px; }
    .lphs-answer-body { font-size:13px; color:#212529; }
    .lphs-chat-input button { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: #fff; border: 0; border-radius: 8px; padding: 8px 12px; font-size: 13px; }
    @media (max-width: 420px){ .lphs-chat-panel{ right: 12px; left: 12px; width: auto; } .lphs-chat-button{ right: 12px; bottom: 12px; } }
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
          // simple contains score
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

        // First try local prototype answers
        const local = localAnswer(q);
        setTimeout(() => {
          t.remove();
          if (local){
            renderAnswer(local);
          } else {
            bubble('<span class="text-muted">I don\'t have that in my FAQ yet. Please try another phrasing.</span>', 'bot');
          }
        }, 500);

        // Note: backend call disabled for prototype
        // fetch(ASK_URL, { ... })
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

