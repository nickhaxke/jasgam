<?php
$config = require __DIR__ . '/../../config/app.php';
$basePath = rtrim($config['base_path'] ?? '', '/');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($title ?? 'JUSGAM - Free Games Portal'); ?></title>
  <script>window.BASE_PATH = "<?php echo htmlspecialchars($basePath, ENT_QUOTES); ?>";</script>
  <meta name="description" content="JUSGAM - Download free games for PC, Mobile, PS4/PS5 and Emulators. No registration required.">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.0/dist/tailwind.min.css" rel="stylesheet">
  <link href="<?php echo htmlspecialchars($basePath); ?>/assets/css/gaming.css" rel="stylesheet">
  <style>
    :root {
      --blue-primary: #2563eb;
      --blue-light:   #3b82f6;
      --cyan-accent:  #06b6d4;
      --dark-bg:      #080d1a;
      --dark-card:    #0d1526;
      --dark-border:  rgba(37, 99, 235, 0.25);
    }

    * { scroll-behavior: smooth; box-sizing: border-box; }

    body {
      background: linear-gradient(160deg, #080d1a 0%, #0a1628 50%, #060c18 100%);
      min-height: 100vh;
      color: #e2e8f0;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
    }

    /* ── Scrollbar ── */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: #080d1a; }
    ::-webkit-scrollbar-thumb { background: rgba(37,99,235,.4); border-radius: 3px; }
    ::-webkit-scrollbar-thumb:hover { background: rgba(37,99,235,.7); }

    /* ── Glassmorphism ── */
    .glass {
      background: rgba(13, 21, 38, 0.85);
      backdrop-filter: blur(16px);
      border-bottom: 1px solid var(--dark-border);
    }

    /* ── Game Card ── */
    .game-card {
      background: var(--dark-card);
      border: 1px solid var(--dark-border);
      border-radius: 1rem;
      overflow: hidden;
      transition: all .3s ease;
      position: relative;
    }
    .game-card:hover {
      border-color: var(--blue-light);
      transform: translateY(-6px);
      box-shadow: 0 20px 50px rgba(37,99,235,.25);
    }
    .game-card-image {
      position: relative;
      overflow: hidden;
      aspect-ratio: 16/9;
      background: linear-gradient(135deg, #1e3a8a, #0e1f6e);
    }
    .game-card-image img {
      width: 100%; height: 100%; object-fit: cover;
      transition: transform .4s ease;
    }
    .game-card:hover .game-card-image img { transform: scale(1.07); }

    /* ── Platform Badge ── */
    .platform-badge {
      display: inline-flex; align-items: center; gap: .3rem;
      padding: .2rem .6rem;
      border-radius: .4rem;
      font-size: .7rem; font-weight: 700; letter-spacing: .05em;
      text-transform: uppercase;
    }
    .badge-pc      { background: rgba(37,99,235,.25);  color: #93c5fd; border: 1px solid rgba(37,99,235,.5); }
    .badge-android { background: rgba(34,197,94,.2);   color: #86efac; border: 1px solid rgba(34,197,94,.4); }
    .badge-ps4     { background: rgba(99,102,241,.25); color: #c4b5fd; border: 1px solid rgba(99,102,241,.5); }
    .badge-ps5     { background: rgba(168,85,247,.25); color: #e9d5ff; border: 1px solid rgba(168,85,247,.5); }
    .badge-emu     { background: rgba(249,115,22,.2);  color: #fdba74; border: 1px solid rgba(249,115,22,.4); }

    /* ── Buttons ── */
    .btn-download {
      display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
      padding: .75rem 1.5rem;
      background: linear-gradient(135deg, #1d4ed8, #2563eb);
      color: #fff; font-weight: 700; border-radius: .75rem;
      border: none; cursor: pointer;
      box-shadow: 0 8px 25px rgba(37,99,235,.35);
      transition: all .3s ease;
      text-decoration: none;
    }
    .btn-download:hover {
      background: linear-gradient(135deg, #2563eb, #3b82f6);
      transform: translateY(-2px);
      box-shadow: 0 14px 35px rgba(37,99,235,.5);
      color: #fff;
    }
    .btn-outline {
      display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
      padding: .65rem 1.25rem;
      background: transparent; color: #93c5fd;
      border: 1px solid rgba(37,99,235,.5);
      border-radius: .75rem; font-weight: 600;
      cursor: pointer; transition: all .3s ease;
      text-decoration: none;
    }
    .btn-outline:hover {
      background: rgba(37,99,235,.15);
      border-color: #3b82f6; color: #bfdbfe;
    }

    /* ── Section headings ── */
    .section-title {
      font-size: 1.5rem; font-weight: 800; color: #f1f5f9;
      display: flex; align-items: center; gap: .75rem;
    }
    .section-title::after {
      content: '';
      flex: 1; height: 1px;
      background: linear-gradient(90deg, rgba(37,99,235,.5), transparent);
    }

    /* ── Category card ── */
    .cat-card {
      background: var(--dark-card);
      border: 1px solid var(--dark-border);
      border-radius: .875rem;
      padding: 1.25rem;
      text-align: center;
      transition: all .3s ease;
      cursor: pointer;
    }
    .cat-card:hover {
      border-color: var(--blue-light);
      background: rgba(37,99,235,.08);
      transform: translateY(-4px);
      box-shadow: 0 12px 30px rgba(37,99,235,.2);
    }

    /* ── Gradient text ── */
    .gradient-text {
      background: linear-gradient(135deg, #60a5fa, #06b6d4);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    /* ── Glow pulse ── */
    @keyframes glow-pulse {
      0%, 100% { box-shadow: 0 0 15px rgba(37,99,235,.3); }
      50%       { box-shadow: 0 0 35px rgba(37,99,235,.6); }
    }
    .glow { animation: glow-pulse 3s ease-in-out infinite; }

    /* ── Animations ── */
    @keyframes fadeSlideUp {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    .animate-in { animation: fadeSlideUp .5s ease-out both; }
    .delay-1 { animation-delay: .1s; }
    .delay-2 { animation-delay: .2s; }
    .delay-3 { animation-delay: .3s; }

    /* ── Sticky header ── */
    header { position: sticky; top: 0; z-index: 50; }
    header.scrolled { box-shadow: 0 4px 30px rgba(0,0,0,.5); }

    /* ── Mobile menu ── */
    #mobile-menu { display: none; }
    #mobile-menu.open { display: block; }

    /* ── Input ── */
    .search-input {
      background: rgba(255,255,255,.05);
      border: 1px solid rgba(37,99,235,.3);
      border-radius: .625rem;
      color: #e2e8f0; padding: .5rem 1rem;
      outline: none; transition: border-color .2s;
    }
    .search-input:focus { border-color: #3b82f6; }
    .search-input::placeholder { color: rgba(226,232,240,.4); }
  </style>
</head>
<body>

  <!-- ══ NAVBAR ══ -->
  <header class="glass" id="main-header">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-4">

      <!-- Logo -->
      <a href="<?php echo htmlspecialchars($basePath); ?>/" class="flex items-center gap-2 flex-shrink-0">
        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-600 to-cyan-500 flex items-center justify-center glow">
          <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M7.97 16L5 19c-.67.67-1 .67-1.41.29A1.987 1.987 0 013 17.83V17c0-.55.45-1 1-1h3.03l.94-1zm5.76-16a5.5 5.5 0 015.5 5.5c0 1.85-.95 3.6-2.5 4.64L18 13h-2v-1.5A3.5 3.5 0 0012.5 8H7.38L4.12 4.74A5.5 5.5 0 0113.73 0z"/>
          </svg>
        </div>
        <div>
          <span class="font-black text-xl gradient-text tracking-widest">JUSGAM</span>
          <div class="text-xs text-blue-400 font-semibold tracking-wider leading-none">FREE GAMES</div>
        </div>
      </a>

      <!-- Desktop Nav -->
      <ul class="hidden md:flex items-center gap-6 text-sm font-semibold">
        <li><a href="<?php echo htmlspecialchars($basePath); ?>/" class="text-gray-300 hover:text-blue-400 transition">Home</a></li>
        <li><a href="<?php echo htmlspecialchars($basePath); ?>/games" class="text-gray-300 hover:text-blue-400 transition">All Games</a></li>
        <li><a href="<?php echo htmlspecialchars($basePath); ?>/categories" class="text-gray-300 hover:text-blue-400 transition">Categories</a></li>
        <li><a href="<?php echo htmlspecialchars($basePath); ?>/games?platform=PC" class="text-gray-300 hover:text-blue-400 transition">PC</a></li>
        <li><a href="<?php echo htmlspecialchars($basePath); ?>/games?platform=Mobile" class="text-gray-300 hover:text-blue-400 transition">Mobile</a></li>
      </ul>

      <!-- Search -->
      <form action="<?php echo htmlspecialchars($basePath); ?>/games" method="GET" class="hidden md:flex items-center gap-2 flex-1 max-w-xs">
        <div class="relative w-full">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input type="text" name="q" placeholder="Search games..." class="search-input w-full pl-10 pr-4 py-2 text-sm">
        </div>
      </form>

      <!-- Mobile hamburger -->
      <button id="hamburger" class="md:hidden p-2 text-gray-300 hover:text-blue-400" onclick="document.getElementById('mobile-menu').classList.toggle('open')">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </nav>

    <!-- Mobile menu -->
    <div id="mobile-menu" class="md:hidden border-t border-blue-900/40 px-4 py-4 space-y-3 text-sm font-semibold">
      <a href="<?php echo htmlspecialchars($basePath); ?>/" class="block text-gray-300 hover:text-blue-400">Home</a>
      <a href="<?php echo htmlspecialchars($basePath); ?>/games" class="block text-gray-300 hover:text-blue-400">All Games</a>
      <a href="<?php echo htmlspecialchars($basePath); ?>/categories" class="block text-gray-300 hover:text-blue-400">Categories</a>
      <a href="<?php echo htmlspecialchars($basePath); ?>/games?platform=PC" class="block text-gray-300 hover:text-blue-400">PC Games</a>
      <a href="<?php echo htmlspecialchars($basePath); ?>/games?platform=Mobile" class="block text-gray-300 hover:text-blue-400">Mobile/APK</a>
      <form action="<?php echo htmlspecialchars($basePath); ?>/games" method="GET" class="pt-2">
        <input type="text" name="q" placeholder="Search games..." class="search-input w-full text-sm">
      </form>
    </div>
  </header>

  <!-- Main Content -->
  <main class="min-h-screen">
    <?php echo $content ?? ''; ?>
  </main>

  <!-- ══ FOOTER ══ -->
  <footer style="background: linear-gradient(180deg, #080d1a 0%, #050912 100%); border-top: 1px solid rgba(37,99,235,.2);" class="mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-10">

        <!-- Brand -->
        <div class="col-span-2 md:col-span-1">
          <div class="flex items-center gap-2 mb-4">
            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-600 to-cyan-500 flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M7.97 16L5 19c-.67.67-1 .67-1.41.29A1.987 1.987 0 013 17.83V17c0-.55.45-1 1-1h3.03l.94-1zm5.76-16a5.5 5.5 0 015.5 5.5c0 1.85-.95 3.6-2.5 4.64L18 13h-2v-1.5A3.5 3.5 0 0012.5 8H7.38L4.12 4.74A5.5 5.5 0 0113.73 0z"/>
              </svg>
            </div>
            <span class="font-black text-lg gradient-text tracking-widest">JUSGAM</span>
          </div>
          <p class="text-gray-500 text-sm leading-relaxed">Free games portal. Download PC, Mobile, PS4/PS5 and emulator games. No sign-up needed.</p>
        </div>

        <!-- Games -->
        <div>
          <h5 class="text-blue-400 font-bold mb-4 text-sm uppercase tracking-wider">Games</h5>
          <ul class="space-y-2 text-sm text-gray-500">
            <li><a href="<?php echo htmlspecialchars($basePath); ?>/games?platform=PC" class="hover:text-blue-400 transition">PC Games</a></li>
            <li><a href="<?php echo htmlspecialchars($basePath); ?>/games?platform=Mobile" class="hover:text-blue-400 transition">Mobile/APK</a></li>
            <li><a href="<?php echo htmlspecialchars($basePath); ?>/games?platform=PS4" class="hover:text-blue-400 transition">PS4 Games</a></li>
            <li><a href="<?php echo htmlspecialchars($basePath); ?>/games?platform=PS5" class="hover:text-blue-400 transition">PS5 Games</a></li>
            <li><a href="<?php echo htmlspecialchars($basePath); ?>/games?platform=Emulator" class="hover:text-blue-400 transition">Emulators/ROMs</a></li>
          </ul>
        </div>

        <!-- Browse -->
        <div>
          <h5 class="text-blue-400 font-bold mb-4 text-sm uppercase tracking-wider">Browse</h5>
          <ul class="space-y-2 text-sm text-gray-500">
            <li><a href="<?php echo htmlspecialchars($basePath); ?>/games" class="hover:text-blue-400 transition">All Games</a></li>
            <li><a href="<?php echo htmlspecialchars($basePath); ?>/categories" class="hover:text-blue-400 transition">Categories</a></li>
            <li><a href="<?php echo htmlspecialchars($basePath); ?>/" class="hover:text-blue-400 transition">New Releases</a></li>
          </ul>
        </div>

        <!-- Info -->
        <div>
          <h5 class="text-blue-400 font-bold mb-4 text-sm uppercase tracking-wider">Info</h5>
          <ul class="space-y-2 text-sm text-gray-500">
            <li><a href="#" class="hover:text-blue-400 transition">Terms of Use</a></li>
            <li><a href="#" class="hover:text-blue-400 transition">Privacy Policy</a></li>
            <li><a href="#" class="hover:text-blue-400 transition">DMCA</a></li>
          </ul>
        </div>
      </div>

      <div class="border-t border-blue-900/30 pt-6 flex flex-col md:flex-row items-center justify-between text-xs text-gray-600 gap-3">
        <p>&copy; 2026 <strong class="text-blue-500">JUSGAM</strong> &bull; Tanzania 🇹🇿 &bull; All Rights Reserved</p>
        <p>Free gaming portal &mdash; no pay, no registration, just play.</p>
      </div>
    </div>
  </footer>

  <script>
    // Sticky header shadow on scroll
    window.addEventListener('scroll', () => {
      document.getElementById('main-header')
        .classList.toggle('scrolled', window.scrollY > 20);
    });
  </script>

  <?php if (file_exists(__DIR__ . '/../../assets/js/announcement-popup.js')): ?>
  <script src="<?php echo htmlspecialchars($basePath); ?>/assets/js/announcement-popup.js"></script>
  <?php endif; ?>
</body>
</html>
