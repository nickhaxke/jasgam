<?php
$config   = require __DIR__ . '/../../config/app.php';
$basePath = rtrim($config['base_path'] ?? '', '/');

$games           = $games           ?? [];
$platforms       = $platforms       ?? [];
$categories      = $categories      ?? [];
$active_platform = $active_platform ?? '';
$active_category = $active_category ?? 0;
$search_query    = $search_query    ?? '';

function gamePlatBadge(string $t): string {
    $m = ['pc'=>'gc-badge-pc','mobile'=>'gc-badge-mobile','ps4'=>'gc-badge-ps4','ps5'=>'gc-badge-ps5','emulator'=>'gc-badge-emu','rom'=>'gc-badge-emu','cloud'=>'gc-badge-cloud'];
    return $m[strtolower($t)] ?? 'gc-badge-pc';
}

$platformIcons = ['PC'=>'&#128421;','Mobile'=>'&#128241;','PS4'=>'&#127918;','PS5'=>'&#128377;','Emulator'=>'&#128126;','Cloud'=>'&#9729;'];

$title = $title ?? 'Games | JUSGAM';

include __DIR__ . '/../layouts/user-header.php';
?>

<div class="page-content">

<!-- Page heading -->
<div style="margin-bottom:var(--gap-lg);">
    <h1 style="font-size:1.6rem;font-weight:900;margin-bottom:4px;">
        <?php if ($active_platform): ?>
            <?= ($platformIcons[$active_platform] ?? '&#127918;') . ' ' . htmlspecialchars($active_platform) ?> Games
        <?php elseif ($search_query): ?>
            Search: &ldquo;<?= htmlspecialchars($search_query) ?>&rdquo;
        <?php else: ?>
            All Games
        <?php endif; ?>
    </h1>
    <p style="font-size:0.85rem;color:var(--text-400);"><?= count($games) ?> game<?= count($games) !== 1 ? 's' : '' ?> found</p>
</div>

<!-- ═══ FILTER BAR ═══ -->
<form class="filter-bar" method="GET" action="<?= htmlspecialchars($basePath) ?>/games" id="filterForm">
    <div class="filter-search">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" name="q" value="<?= htmlspecialchars($search_query) ?>" placeholder="Search games...">
    </div>

    <select name="platform" class="filter-select" onchange="this.form.submit()">
        <option value="">All Platforms</option>
        <?php foreach ($platforms as $p): ?>
        <option value="<?= htmlspecialchars($p) ?>" <?= $active_platform === $p ? 'selected' : '' ?>>
            <?= htmlspecialchars($p) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <?php if (!empty($categories)): ?>
    <select name="category" class="filter-select" onchange="this.form.submit()">
        <option value="">All Genres</option>
        <?php foreach ($categories as $cat): ?>
        <option value="<?= (int)$cat['id'] ?>" <?= $active_category === (int)$cat['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
        </option>
        <?php endforeach; ?>
    </select>
    <?php endif; ?>
</form>

<!-- ═══ PLATFORM QUICK TABS ═══ -->
<?php if (!empty($platforms)): ?>
<div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:var(--gap-lg);">
    <a href="<?= htmlspecialchars($basePath) ?>/games<?= $search_query ? '?q=' . urlencode($search_query) : '' ?>"
       class="btn btn-sm <?= !$active_platform ? 'btn-primary' : 'btn-ghost' ?>">All</a>
    <?php foreach ($platforms as $p): ?>
    <a href="<?= htmlspecialchars($basePath) ?>/games?platform=<?= urlencode($p) ?><?= $search_query ? '&q=' . urlencode($search_query) : '' ?>"
       class="btn btn-sm <?= $active_platform === $p ? 'btn-primary' : 'btn-ghost' ?>">
        <?= ($platformIcons[$p] ?? '&#127918;') . ' ' . htmlspecialchars($p) ?>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ═══ GAMES GRID ═══ -->
<?php if (!empty($games)): ?>
<div class="game-grid" style="margin-bottom:var(--gap-2xl);">
    <?php foreach ($games as $g): ?>
    <a href="<?= htmlspecialchars($basePath) ?>/product/<?= (int)$g['id'] ?>" class="game-card">
        <div class="gc-img"<?php if (!empty($g['preview_video_url'])): ?> data-preview-video="<?= htmlspecialchars($g['preview_video_url']) ?>"<?php endif; ?>>
            <?php if (!empty($g['image_url'])): ?>
                <img src="<?= htmlspecialchars($g['image_url']) ?>" alt="<?= htmlspecialchars($g['name']) ?>" loading="lazy">
            <?php else: ?>
                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:var(--text-500);">&#127918;</div>
            <?php endif; ?>
            <span class="gc-badge <?= gamePlatBadge($g['game_type'] ?? 'pc') ?>"><?= htmlspecialchars($g['game_type'] ?? 'PC') ?></span>
            <div class="gc-overlay">
                <div class="gc-overlay-rating">&#9733;&#9733;&#9733;&#9733;&#9733; <span style="color:var(--text-300);">5.0</span></div>
                <button class="gc-overlay-btn" onclick="event.preventDefault();">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Quick Download
                </button>
            </div>
        </div>
        <div class="gc-body">
            <div class="gc-title"><?= htmlspecialchars($g['name']) ?></div>
            <div class="gc-meta">
                <span class="gc-genre"><?= htmlspecialchars($g['category_name'] ?? '') ?></span>
                <span class="gc-free">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Free
                </span>
            </div>
        </div>
    </a>
    <?php endforeach; ?>
</div>
<?php else: ?>
<!-- Empty state -->
<div style="text-align:center;padding:80px 20px;">
    <div style="font-size:3.5rem;margin-bottom:16px;opacity:0.5;">&#128269;</div>
    <h3 style="font-size:1.15rem;font-weight:800;margin-bottom:8px;">No games found</h3>
    <p style="color:var(--text-400);font-size:0.9rem;margin-bottom:var(--gap-lg);max-width:360px;margin-left:auto;margin-right:auto;">
        <?php if ($search_query): ?>
            No results for &ldquo;<?= htmlspecialchars($search_query) ?>&rdquo;. Try a different search term.
        <?php elseif ($active_platform): ?>
            No <?= htmlspecialchars($active_platform) ?> games available yet. Check back soon!
        <?php else: ?>
            No games available at the moment.
        <?php endif; ?>
    </p>
    <a href="<?= htmlspecialchars($basePath) ?>/games" class="btn btn-primary">Browse All Games</a>
</div>
<?php endif; ?>

</div><!-- /page-content -->

<?php include __DIR__ . '/../layouts/user-footer.php'; ?>
