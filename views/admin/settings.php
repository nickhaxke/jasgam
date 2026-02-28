<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$title = 'System Settings';
$supportPhone = $settings['support_phone'] ?? '';
$whatsappNumber = $settings['whatsapp_number'] ?? '';
$supportEmail = $settings['support_email'] ?? '';
$adsterraSmartlinkUrl = $settings['adsterra_smartlink_url'] ?? '';
$adsterraPopunderScript = $settings['adsterra_popunder_script'] ?? '';
$adsterraBannerKey = $settings['adsterra_banner_key'] ?? '';
$adsterraBannerInvokeHost = $settings['adsterra_banner_invoke_host'] ?? '';
ob_start();
?>

<div class="grid">
    <div class="glass" style="padding: 20px;">
        <div class="section-title">
            <h3>System Settings</h3>
        </div>

        <?php if (!empty($_GET['saved'])): ?>
            <div style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.35); border-radius: 12px; padding: 14px; margin-bottom: 16px;">
                <span class="badge badge-success">Settings saved successfully</span>
            </div>
        <?php endif; ?>

        <form method="POST" action="/hasheem/admin/settings/save" style="display: grid; gap: 18px;">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px;">
                <div>
                    <label style="color: var(--muted); font-size: 0.85rem; display: block; margin-bottom: 6px;">Support Phone</label>
                    <input class="search" name="support_phone" value="<?= htmlspecialchars($supportPhone) ?>" placeholder="255700000000" style="padding: 10px; width: 100%;">
                </div>
                <div>
                    <label style="color: var(--muted); font-size: 0.85rem; display: block; margin-bottom: 6px;">WhatsApp Number</label>
                    <input class="search" name="whatsapp_number" value="<?= htmlspecialchars($whatsappNumber) ?>" placeholder="255700000000" style="padding: 10px; width: 100%;">
                </div>
                <div>
                    <label style="color: var(--muted); font-size: 0.85rem; display: block; margin-bottom: 6px;">Support Email</label>
                    <input class="search" name="support_email" value="<?= htmlspecialchars($supportEmail) ?>" placeholder="support@example.com" style="padding: 10px; width: 100%;">
                </div>
            </div>

            <div class="glass" style="padding: 16px; background: rgba(20, 27, 45, 0.52);">
                <div style="margin-bottom: 12px; color: var(--text); font-weight: 600;">Adsterra Ads</div>
                <div style="color: var(--muted); font-size: 0.85rem; margin-bottom: 12px;">Weka links/keys rasmi kutoka Adsterra account yako.</div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 12px;">
                    <div>
                        <label style="color: var(--muted); font-size: 0.85rem; display: block; margin-bottom: 6px;">Smartlink URL</label>
                        <input class="search" name="adsterra_smartlink_url" value="<?= htmlspecialchars($adsterraSmartlinkUrl) ?>" placeholder="https://..." style="padding: 10px; width: 100%;">
                    </div>
                    <div>
                        <label style="color: var(--muted); font-size: 0.85rem; display: block; margin-bottom: 6px;">Popunder Script URL</label>
                        <input class="search" name="adsterra_popunder_script" value="<?= htmlspecialchars($adsterraPopunderScript) ?>" placeholder="https://...js" style="padding: 10px; width: 100%;">
                    </div>
                    <div>
                        <label style="color: var(--muted); font-size: 0.85rem; display: block; margin-bottom: 6px;">Banner Key</label>
                        <input class="search" name="adsterra_banner_key" value="<?= htmlspecialchars($adsterraBannerKey) ?>" placeholder="ad unit key" style="padding: 10px; width: 100%;">
                    </div>
                    <div>
                        <label style="color: var(--muted); font-size: 0.85rem; display: block; margin-bottom: 6px;">Banner Invoke Host</label>
                        <input class="search" name="adsterra_banner_invoke_host" value="<?= htmlspecialchars($adsterraBannerInvokeHost) ?>" placeholder="https://www.highperformanceformat.com" style="padding: 10px; width: 100%;">
                    </div>
                </div>
            </div>

            <div class="glass" style="padding: 16px; background: rgba(20, 27, 45, 0.52);">
                <div style="margin-bottom: 12px; color: var(--text); font-weight: 600;">Payment Methods</div>
                <div style="color: var(--muted); font-size: 0.85rem; margin-bottom: 12px;">Numbers shown in checkout & payment instructions.</div>

                <div id="methodsList" style="display: grid; gap: 12px;">
                    <?php foreach ($paymentMethods as $method): ?>
                        <div style="display: grid; grid-template-columns: 1.2fr 1fr 1fr auto; gap: 10px; align-items: center;">
                            <input class="search" name="method_name[]" value="<?= htmlspecialchars($method['name'] ?? '') ?>" placeholder="Method name" style="padding: 8px;">
                            <input class="search" name="method_receiver[]" value="<?= htmlspecialchars($method['receiver'] ?? '') ?>" placeholder="Receiver name" style="padding: 8px;">
                            <input class="search" name="method_number[]" value="<?= htmlspecialchars($method['number'] ?? '') ?>" placeholder="Number" style="padding: 8px;">
                            <button type="button" class="btn btn-danger" onclick="removeMethod(this)">Remove</button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="button" class="btn btn-outline" onclick="addMethod()" style="margin-top: 10px;">+ Add Payment Method</button>
            </div>

            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>

<script>
    function addMethod() {
        const list = document.getElementById('methodsList');
        const row = document.createElement('div');
        row.style.cssText = 'display: grid; grid-template-columns: 1.2fr 1fr 1fr auto; gap: 10px; align-items: center;';
        row.innerHTML = `
            <input class="search" name="method_name[]" placeholder="Method name" style="padding: 8px;">
            <input class="search" name="method_receiver[]" placeholder="Receiver name" style="padding: 8px;">
            <input class="search" name="method_number[]" placeholder="Number" style="padding: 8px;">
            <button type="button" class="btn btn-danger" onclick="removeMethod(this)">Remove</button>
        `;
        list.appendChild(row);
    }

    function removeMethod(button) {
        button.parentElement.remove();
    }
</script>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin.php'; ?>
