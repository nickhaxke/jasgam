<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isEdit = isset($announcement);
$title = $isEdit ? 'Edit Announcement' : 'Create Announcement';
ob_start();
?>

<style>
.form-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.form-header h2 {
    font-size: 1.75rem;
    background: linear-gradient(135deg, var(--accent), #667eea);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.btn-back {
    padding: 0.75rem 1.5rem;
    background: var(--bg-2);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    color: var(--ink-1);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-back:hover {
    background: var(--bg-1);
    border-color: var(--accent);
    transform: translateX(-4px);
}

.form-container {
    background: var(--bg-1);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 2rem;
    max-width: 900px;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 600;
    color: var(--ink-1);
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.form-input, .form-textarea, .form-select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    background: var(--bg-2);
    color: var(--ink-1);
    font-size: 0.95rem;
    transition: all 0.2s;
}

.form-input:focus, .form-textarea:focus, .form-select:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 120px;
}

.form-hint {
    display: block;
    font-size: 0.85rem;
    color: var(--ink-2);
    margin-top: 0.5rem;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.checkbox-card {
    background: rgba(99, 102, 241, 0.1);
    border: 1px solid rgba(99, 102, 241, 0.2);
    border-radius: 12px;
    padding: 1.25rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: var(--ink-1);
    font-weight: 600;
    cursor: pointer;
}

.checkbox-input {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn-submit {
    flex: 1;
    padding: 0.875rem 1.5rem;
    background: linear-gradient(135deg, var(--accent), #667eea);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.btn-cancel {
    padding: 0.875rem 1.5rem;
    background: var(--bg-2);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    color: var(--ink-1);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
    display: inline-block;
    text-align: center;
}

.btn-cancel:hover {
    background: var(--bg-1);
    border-color: #ef4444;
    color: #ef4444;
}
</style>

<div class="form-header">
    <a href="<?= htmlspecialchars($basePath ?? '') ?>/admin/announcements" class="btn-back">← Back</a>
    <h2><?= $isEdit ? '✏️ Edit' : '➕ Create' ?> Announcement</h2>
</div>

<div class="form-container">
    <form method="POST" action="<?= htmlspecialchars($basePath ?? '') ?>/admin/announcements/<?= $isEdit ? 'update/' . $announcement['id'] : 'store' ?>">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

        <div class="form-group">
            <label class="form-label">Title *</label>
            <input type="text" name="title" required maxlength="200" 
                   value="<?= htmlspecialchars($announcement['title'] ?? '') ?>" 
                   class="form-input" 
                   placeholder="e.g., System Maintenance Notice">
        </div>

        <div class="form-group">
            <label class="form-label">Message *</label>
            <textarea name="message" required class="form-textarea" 
                      placeholder="Enter your announcement message..."><?= htmlspecialchars($announcement['message'] ?? '') ?></textarea>
            <span class="form-hint">This message will be displayed to users on the homepage</span>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Priority</label>
                <input type="number" name="priority" min="0" max="100" 
                       value="<?= htmlspecialchars($announcement['priority'] ?? 0) ?>" 
                       class="form-input">
                <span class="form-hint">Higher priority (0-100) shows first</span>
            </div>

            <div class="form-group">
                <label class="form-label">Auto Close (seconds)</label>
                <input type="number" name="auto_close_seconds" min="0" 
                       value="<?= htmlspecialchars($announcement['auto_close_seconds'] ?? 30) ?>" 
                       class="form-input">
                <span class="form-hint">0 = manual close only</span>
            </div>

            <div class="form-group">
                <label class="form-label">Target Users</label>
                <select name="target_users" class="form-select">
                    <option value="all" <?= ($announcement['target_users'] ?? 'all') === 'all' ? 'selected' : '' ?>>All Users</option>
                    <option value="users" <?= ($announcement['target_users'] ?? 'all') === 'users' ? 'selected' : '' ?>>Users Only</option>
                    <option value="admins" <?= ($announcement['target_users'] ?? 'all') === 'admins' ? 'selected' : '' ?>>Admins Only</option>
                </select>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Start Time (Optional)</label>
                <input type="datetime-local" name="start_time" 
                       value="<?= $isEdit && $announcement['start_time'] ? date('Y-m-d\TH:i', strtotime($announcement['start_time'])) : '' ?>" 
                       class="form-input">
                <span class="form-hint">When to start showing</span>
            </div>

            <div class="form-group">
                <label class="form-label">End Time (Optional)</label>
                <input type="datetime-local" name="end_time" 
                       value="<?= $isEdit && $announcement['end_time'] ? date('Y-m-d\TH:i', strtotime($announcement['end_time'])) : '' ?>" 
                       class="form-input">
                <span class="form-hint">When to stop showing</span>
            </div>
        </div>

        <div class="checkbox-card">
            <label class="checkbox-label">
                <input type="checkbox" name="active" value="1" 
                       <?= ($announcement['active'] ?? 0) ? 'checked' : '' ?> 
                       class="checkbox-input">
                <span>🔔 Activate announcement immediately</span>
            </label>
            <span class="form-hint" style="margin-left: 1.75rem;">
                When active, this announcement will be shown to users on the website
            </span>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">
                <?= $isEdit ? '💾 Update' : '➕ Create' ?> Announcement
            </button>
            <a href="<?= htmlspecialchars($basePath ?? '') ?>/admin/announcements" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin.php'; ?>
