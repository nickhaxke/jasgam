<?php
$user = $_SESSION['user'] ?? null;
require_once __DIR__ . '/../../core/Settings.php';
$appSettings = \Core\Settings::all();
$config   = require __DIR__ . '/../../config/app.php';
$basePath = rtrim($config['base_path'] ?? '', '/');
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$isActive = function(string $path) use ($currentUri, $basePath) {
    $full = $basePath . $path;
    return ($currentUri === $full || strpos($currentUri, $full . '?') === 0) ? ' active' : '';
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'JUSGAM - Free Games') ?></title>
    <meta name="description" content="JUSGAM - Download free games for PC, Mobile, PS4/PS5 and Emulators. No registration required.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= htmlspecialchars($basePath) ?>/assets/css/app.css">
    <?php if (!empty($additionalStyles)): ?>
    <style><?= $additionalStyles ?></style>
    <?php endif; ?>
</head>
<body>

<!-- ═══ NAVBAR ═══ -->
<nav class="navbar">
    <a href="<?= htmlspecialchars($basePath) ?>/" class="nav-logo">
        <div class="nav-logo-icon">
            <svg fill="currentColor" viewBox="0 0 24 24"><path d="M21 6H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-10 7H8v3H6v-3H3v-2h3V8h2v3h3v2zm4.5 2c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm4-3c-.83 0-1.5-.67-1.5-1.5S18.67 9 19.5 9s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
        </div>
        <span class="nav-logo-text">JUSGAM</span>
    </a>

    <div class="nav-search">
        <form action="<?= htmlspecialchars($basePath) ?>/games" method="GET" id="searchForm">
            <svg class="nav-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="q" id="liveSearchInput" placeholder="Search games..." autocomplete="off">
        </form>
        <div class="live-search-dropdown" id="liveSearchDropdown" style="display:none;"></div>
    </div>

    <div class="nav-actions">
        <a href="<?= htmlspecialchars($basePath) ?>/games" class="nav-cta">Browse Games</a>
    </div>

    <button class="nav-toggle" id="navToggle" aria-label="Menu">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
</nav>

<!-- ═══ SIDEBAR OVERLAY ═══ -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ═══ SIDEBAR ═══ -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-section">
        <div class="sidebar-label">Menu</div>
        <nav class="sidebar-nav">
            <a href="<?= htmlspecialchars($basePath) ?>/" class="sidebar-link<?= $isActive('/') ?>">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
                Home
            </a>
            <a href="<?= htmlspecialchars($basePath) ?>/games" class="sidebar-link<?= $isActive('/games') ?>">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                All Games
            </a>
        </nav>
    </div>

    <div class="sidebar-divider"></div>

    <div class="sidebar-section">
        <div class="sidebar-label">Platforms</div>
        <nav class="sidebar-nav">
            <a href="<?= htmlspecialchars($basePath) ?>/games?platform=PC" class="sidebar-link">
                <span class="s-icon">&#128421;</span> PC Games
            </a>
            <a href="<?= htmlspecialchars($basePath) ?>/games?platform=Mobile" class="sidebar-link">
                <span class="s-icon">&#128241;</span> Mobile
            </a>
            <a href="<?= htmlspecialchars($basePath) ?>/games?platform=PS4" class="sidebar-link">
                <span class="s-icon">&#127918;</span> PS4
            </a>
            <a href="<?= htmlspecialchars($basePath) ?>/games?platform=PS5" class="sidebar-link">
                <span class="s-icon">&#128377;</span> PS5
            </a>
            <a href="<?= htmlspecialchars($basePath) ?>/games?platform=Cloud" class="sidebar-link">
                <span class="s-icon">&#9729;</span> Cloud
            </a>
            <a href="<?= htmlspecialchars($basePath) ?>/games?platform=Emulator" class="sidebar-link">
                <span class="s-icon">&#128126;</span> Emulators
            </a>
        </nav>
    </div>

    <div class="sidebar-divider"></div>

    <div class="sidebar-section">
        <div class="sidebar-label">Browse</div>
        <nav class="sidebar-nav">
            <a href="<?= htmlspecialchars($basePath) ?>/categories" class="sidebar-link">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                Categories
            </a>
        </nav>
    </div>
</aside>

<!-- ═══ MAIN CONTENT ═══ -->
<div class="main-content">
<script>
(function(){
    var toggle=document.getElementById('navToggle'),
        sidebar=document.getElementById('sidebar'),
        overlay=document.getElementById('sidebarOverlay');
    function closeSidebar(){sidebar.classList.remove('open');overlay.classList.remove('open');}
    if(toggle&&sidebar){
        toggle.addEventListener('click',function(){
            sidebar.classList.toggle('open');
            overlay.classList.toggle('open');
        });
        overlay.addEventListener('click',closeSidebar);
    }

    // Live Search
    var input=document.getElementById('liveSearchInput'),
        dropdown=document.getElementById('liveSearchDropdown'),
        timer,BASE='<?= htmlspecialchars($basePath, ENT_QUOTES) ?>';
    if(input&&dropdown){
        input.addEventListener('input',function(){
            clearTimeout(timer);
            var q=this.value.trim();
            if(q.length<2){dropdown.style.display='none';return;}
            timer=setTimeout(function(){
                fetch(BASE+'/api/search?q='+encodeURIComponent(q))
                .then(function(r){return r.json();})
                .then(function(data){
                    if(!data.results||!data.results.length){
                        dropdown.innerHTML='<div class="live-search-empty">No games found</div>';
                    }else{
                        var h='';
                        data.results.forEach(function(r){
                            h+='<a href="'+esc(r.url)+'" class="live-search-item">';
                            if(r.image_url)h+='<img src="'+esc(r.image_url)+'" alt="" loading="lazy">';
                            h+='<div><div class="ls-title">'+esc(r.name)+'</div>';
                            h+='<div class="ls-meta">'+esc(r.game_type||'')+' &middot; '+esc(r.category_name||'')+'</div></div></a>';
                        });
                        dropdown.innerHTML=h;
                    }
                    dropdown.style.display='block';
                }).catch(function(){dropdown.style.display='none';});
            },300);
        });
        document.addEventListener('click',function(e){
            if(!e.target.closest('.nav-search'))dropdown.style.display='none';
        });
    }
    function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
})();
</script>
