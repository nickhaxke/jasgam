<?php
$config   = require __DIR__ . '/../../config/app.php';
$basePath = rtrim($config['base_path'] ?? '', '/');

$by_platform     = $by_platform     ?? [];
$latest_games    = $latest_games    ?? [];
$featured_games  = $featured_games  ?? [];
$trending_games  = $trending_games  ?? [];
$most_downloaded = $most_downloaded ?? [];
$categories      = $categories      ?? [];
$total_games     = $total_games     ?? 0;
$platform_count  = $platform_count  ?? count($by_platform);

function gcBadge(string $t): string {
    $m = ['pc'=>'gc-badge-pc','mobile'=>'gc-badge-mobile','ps4'=>'gc-badge-ps4','ps5'=>'gc-badge-ps5','emulator'=>'gc-badge-emu','rom'=>'gc-badge-emu','cloud'=>'gc-badge-cloud'];
    return $m[strtolower($t)] ?? 'gc-badge-pc';
}

$platforms = [
    'PC'       => ['icon'=>'&#128421;','label'=>'PC Games'],
    'Mobile'   => ['icon'=>'&#128241;','label'=>'Mobile'],
    'PS4'      => ['icon'=>'&#127918;','label'=>'PS4 Games'],
    'PS5'      => ['icon'=>'&#128377;','label'=>'PS5 Games'],
    'Emulator' => ['icon'=>'&#128126;','label'=>'Emulators'],
    'Cloud'    => ['icon'=>'&#9729;','label'=>'Cloud Gaming'],
];

$title = $title ?? 'JUSGAM - Free Games Portal';

// Hero slides: use featured games if available, else latest 4
$heroGames = !empty($featured_games) ? $featured_games : array_slice($latest_games, 0, 4);

include __DIR__ . '/../layouts/user-header.php';

// Helper: render a game card with optional video preview data attribute
function renderGameCard(array $g, string $basePath, bool $showRating = true): string {
    $html = '<a href="' . htmlspecialchars($basePath) . '/product/' . (int)$g['id'] . '" class="game-card">';
    $html .= '<div class="gc-img"';
    if (!empty($g['preview_video_url'])) {
        $html .= ' data-preview-video="' . htmlspecialchars($g['preview_video_url']) . '"';
    }
    $html .= '>';
    if (!empty($g['image_url'])) {
        $html .= '<img src="' . htmlspecialchars($g['image_url']) . '" alt="' . htmlspecialchars($g['name']) . '" loading="lazy">';
    } else {
        $html .= '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:var(--text-500);">&#127918;</div>';
    }
    $html .= '<span class="gc-badge ' . gcBadge($g['game_type'] ?? 'pc') . '">' . htmlspecialchars($g['game_type'] ?? 'PC') . '</span>';
    $html .= '<div class="gc-overlay">';
    if ($showRating) {
        $html .= '<div class="gc-overlay-rating">&#9733;&#9733;&#9733;&#9733;&#9733; <span style="color:var(--text-300);">5.0</span></div>';
    }
    $html .= '<button class="gc-overlay-btn" onclick="event.preventDefault();">';
    $html .= '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>';
    $html .= 'Download</button></div></div>';
    $html .= '<div class="gc-body"><div class="gc-title">' . htmlspecialchars($g['name']) . '</div>';
    $html .= '<div class="gc-meta"><span class="gc-genre">' . htmlspecialchars($g['category_name'] ?? '') . '</span>';
    $html .= '<span class="gc-free"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Free</span>';
    $html .= '</div></div></a>';
    return $html;
}
?>

<!-- ═══ HERO SLIDER ═══ -->
<?php if (!empty($heroGames)): ?>
<div class="hero-slider" id="heroSlider">
    <div class="hero-slides" id="heroSlides">
        <?php foreach ($heroGames as $i => $hg):
            $bgImage = $hg['cover_image'] ?? $hg['image_url'] ?? '';
            $bgVideo = $hg['trailer_video_url'] ?? null;
            $videoData = $bgVideo ? \Core\VideoEmbed::parse($bgVideo) : null;
        ?>
        <div class="hero-slide<?= $i === 0 ? ' active' : '' ?>">
            <?php if ($videoData && $videoData['type'] === 'mp4'): ?>
                <video class="hero-slide-video" src="<?= htmlspecialchars($videoData['embed_url']) ?>" muted autoplay loop playsinline></video>
            <?php else: ?>
                <div class="hero-slide-bg" style="background-image:url('<?= htmlspecialchars($bgImage) ?>');"></div>
            <?php endif; ?>
            <div class="hero-slide-content">
                <div class="hero-badges">
                    <?php if (!empty($hg['game_type'])): ?>
                    <span class="hero-platform-badge"><?= htmlspecialchars($hg['game_type']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($hg['category_name'])): ?>
                    <span class="hero-platform-badge" style="background:rgba(52,211,153,0.15);border-color:rgba(52,211,153,0.4);color:#6ee7b7;"><?= htmlspecialchars($hg['category_name']) ?></span>
                    <?php endif; ?>
                    <span class="hero-platform-badge" style="background:rgba(52,211,153,0.15);border-color:rgba(52,211,153,0.4);color:#6ee7b7;">FREE</span>
                </div>
                <h2 class="hero-title"><?= htmlspecialchars($hg['name']) ?></h2>
                <div class="hero-rating">
                    <span class="hero-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                    <span class="hero-rating-text">Popular</span>
                </div>
                <div class="hero-actions">
                    <a href="<?= htmlspecialchars($basePath) ?>/product/<?= (int)$hg['id'] ?>" class="btn btn-primary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Watch Ad &amp; Download
                    </a>
                    <a href="<?= htmlspecialchars($basePath) ?>/product/<?= (int)$hg['id'] ?>" class="btn btn-glass">View Details</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <button class="hero-arrow hero-prev" id="heroPrev"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
    <button class="hero-arrow hero-next" id="heroNext"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
    <div class="hero-dots" id="heroDots">
        <?php foreach ($heroGames as $i => $hg): ?>
        <div class="hero-dot<?= $i === 0 ? ' active' : '' ?>" data-idx="<?= $i ?>"></div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="page-content">



<!-- ═══ TRENDING GAMES ═══ -->
<?php if (!empty($trending_games)): ?>
<section style="margin-bottom:var(--gap-2xl);">
    <div class="section-head">
        <div class="section-title"><span class="glow-dot"></span> Trending Games</div>
        <a href="<?= htmlspecialchars($basePath) ?>/games" class="section-link">View All &rarr;</a>
    </div>
    <div class="game-grid">
        <?php foreach ($trending_games as $g): ?>
        <?= renderGameCard($g, $basePath) ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- ═══ LATEST GAMES ═══ -->
<?php if (!empty($latest_games)): ?>
<section style="margin-bottom:var(--gap-2xl);">
    <div class="section-head">
        <div class="section-title"><span class="glow-dot"></span> Latest Games</div>
        <a href="<?= htmlspecialchars($basePath) ?>/games" class="section-link">View All &rarr;</a>
    </div>
    <div class="game-grid">
        <?php foreach ($latest_games as $g): ?>
        <?= renderGameCard($g, $basePath) ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- ═══ MOST DOWNLOADED ═══ -->
<?php if (!empty($most_downloaded)): ?>
<section style="margin-bottom:var(--gap-2xl);">
    <div class="section-head">
        <div class="section-title"><span class="glow-dot"></span> Most Downloaded</div>
        <a href="<?= htmlspecialchars($basePath) ?>/games" class="section-link">View All &rarr;</a>
    </div>
    <div class="game-grid">
        <?php foreach ($most_downloaded as $g): ?>
        <?= renderGameCard($g, $basePath) ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- ═══ GAMES BY PLATFORM ═══ -->
<?php foreach ($by_platform as $platform => $pGames): ?>
<?php if (empty($pGames)) continue; ?>
<section style="margin-bottom:var(--gap-2xl);">
    <?php $pi = $platforms[$platform] ?? $platforms[ucfirst(strtolower($platform))] ?? ['icon'=>'&#127918;','label'=>$platform]; ?>
    <div class="section-head">
        <div class="section-title"><span class="glow-dot"></span> <?= $pi['icon'] ?> <?= htmlspecialchars($pi['label'] ?? $platform) ?></div>
        <a href="<?= htmlspecialchars($basePath) ?>/games?platform=<?= urlencode($platform) ?>" class="section-link">All (<?= count($pGames) ?>) &rarr;</a>
    </div>
    <div class="game-grid">
        <?php foreach (array_slice($pGames, 0, 5) as $g): ?>
        <?= renderGameCard($g, $basePath, false) ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endforeach; ?>

<!-- ═══ HOW IT WORKS ═══ -->
<section style="margin-bottom:var(--gap-2xl);">
    <div class="section-head" style="justify-content:center;">
        <div class="section-title"><span class="glow-dot"></span> How It Works</div>
    </div>
    <div class="how-grid">
        <div class="how-card">
            <div class="how-num">1</div>
            <div class="how-title">Find a Game</div>
            <div class="how-desc">Browse PC, Mobile, PS4, PS5, and Emulator games.</div>
        </div>
        <div class="how-card">
            <div class="how-num">2</div>
            <div class="how-title">Watch a Short Ad</div>
            <div class="how-desc">A quick ad unlocks your download. No account needed.</div>
        </div>
        <div class="how-card">
            <div class="how-num">3</div>
            <div class="how-title">Download Free</div>
            <div class="how-desc">Get the game file instantly &mdash; completely free.</div>
        </div>
    </div>
</section>

</div><!-- /page-content -->

<!-- Hero Slider JS -->
<script>
(function(){
    var slides=document.getElementById('heroSlides');
    var dots=document.querySelectorAll('#heroDots .hero-dot');
    var allSlides=document.querySelectorAll('.hero-slide');
    if(!slides||!allSlides.length) return;
    var cur=0,total=allSlides.length,timer;

    function goTo(n){
        cur=((n%total)+total)%total;
        slides.style.transform='translateX(-'+(cur*100)+'%)';
        allSlides.forEach(function(s,i){s.classList.toggle('active',i===cur);});
        dots.forEach(function(d,i){d.classList.toggle('active',i===cur);});
    }
    function next(){goTo(cur+1);}
    function startAuto(){timer=setInterval(next,5000);}
    function resetAuto(){clearInterval(timer);startAuto();}

    document.getElementById('heroNext').addEventListener('click',function(){next();resetAuto();});
    document.getElementById('heroPrev').addEventListener('click',function(){goTo(cur-1);resetAuto();});
    dots.forEach(function(d){d.addEventListener('click',function(){goTo(+this.dataset.idx);resetAuto();});});
    startAuto();
})();
</script>

<?php include __DIR__ . '/../layouts/user-footer.php'; ?>
