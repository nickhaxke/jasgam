<!-- ═══ FOOTER ═══ -->
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                    <div style="width:32px;height:32px;border-radius:10px;background:var(--gradient-main);display:flex;align-items:center;justify-content:center;box-shadow:var(--glow-purple);">
                        <svg style="width:18px;height:18px;color:#fff;" fill="currentColor" viewBox="0 0 24 24"><path d="M21 6H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-10 7H8v3H6v-3H3v-2h3V8h2v3h3v2zm4.5 2c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm4-3c-.83 0-1.5-.67-1.5-1.5S18.67 9 19.5 9s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
                    </div>
                    <span class="text-gradient" style="font-weight:900;font-size:1.1rem;letter-spacing:0.08em;">JUSGAM</span>
                </div>
                <p style="color:var(--text-500);font-size:0.82rem;line-height:1.7;max-width:260px;">
                    Free games portal. Download PC, Mobile, PS4/PS5, Cloud &amp; Emulator games. Watch an ad, get the game. No account needed.
                </p>
            </div>
            <div>
                <div class="footer-heading">Platforms</div>
                <div class="footer-links">
                    <?php
                    $fp = [['PC','PC Games'],['Mobile','Mobile Games'],['PS4','PS4 Games'],['PS5','PS5 Games'],['Cloud','Cloud Gaming'],['Emulator','Emulators']];
                    foreach ($fp as [$k,$l]):
                    ?>
                    <a href="<?= htmlspecialchars($basePath) ?>/games?platform=<?= urlencode($k) ?>"><?= $l ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div>
                <div class="footer-heading">Quick Links</div>
                <div class="footer-links">
                    <a href="<?= htmlspecialchars($basePath) ?>/games">All Games</a>
                    <a href="<?= htmlspecialchars($basePath) ?>/categories">Categories</a>
                </div>
            </div>
            <div>
                <?php
                    $supportPhone = $appSettings['support_phone'] ?? '255621215237';
                    $whatsappNumber = $appSettings['whatsapp_number'] ?? $supportPhone;
                    $supportEmail = $appSettings['support_email'] ?? 'support@jusgam.com';
                ?>
                <div class="footer-heading">Contact</div>
                <div class="footer-links">
                    <a href="https://wa.me/<?= htmlspecialchars($whatsappNumber) ?>" target="_blank">WhatsApp</a>
                    <a href="mailto:<?= htmlspecialchars($supportEmail) ?>"><?= htmlspecialchars($supportEmail) ?></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; <?= date('Y') ?> <strong style="color:var(--accent-light);">JUSGAM</strong> &middot; Tanzania</span>
            <span>Free games &mdash; watch an ad, download, play.</span>
        </div>
    </div>
</footer>

</div><!-- /main-content -->

<?php
    $isLocalHost = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1'], true);
    $adsterraScript = trim($appSettings['adsterra_popunder_script'] ?? '');
?>
<?php if (!$isLocalHost && $adsterraScript !== ''): ?>
    <script src="<?= htmlspecialchars($adsterraScript) ?>"></script>
<?php endif; ?>
<script src="<?= htmlspecialchars($basePath) ?>/assets/js/video-embed.js"></script>
</body>
</html>
