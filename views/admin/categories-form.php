<?php ob_start(); ?>

<!-- Header Section -->
<div class="section-header" style="margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
        <a href="<?= $basePath ?>/admin/categories" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: var(--bg-2); border: 1px solid var(--line); border-radius: 10px; text-decoration: none; font-size: 1.2rem; transition: all 0.2s ease;">
            ←
        </a>
        <h2 style="font-size: 1.75rem; font-weight: 700; margin: 0; background: linear-gradient(135deg, #a855f7 0%, #3af2ff 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            <?= $isEdit ? '✏️ Edit Category' : '➕ Add New Category' ?>
        </h2>
    </div>
    <p style="color: var(--muted); margin: 0; font-size: 0.9rem;">
        <?= $isEdit ? 'Update category information below' : 'Create a new category for organizing products' ?>
    </p>
</div>

<?php if (isset($_GET['error'])): ?>
<div class="error-alert" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(239, 68, 68, 0.05) 100%); border: 2px solid rgba(239, 68, 68, 0.4); color: #ef4444; padding: 1.25rem 1.5rem; border-radius: 14px; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.1);">
    <span style="font-size: 1.5rem;">❌</span>
    <span style="font-weight: 600;"><?= htmlspecialchars($_GET['error']) ?></span>
</div>
<?php endif; ?>

<!-- Form Card -->
<div class="form-card" style="background: var(--card); border: 1px solid var(--line); border-radius: 16px; padding: 2.5rem; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); max-width: 800px;">
    <form method="POST" action="<?= $isEdit ? $basePath . '/admin/categories/update/' . $category['id'] : $basePath . '/admin/categories/store' ?>" id="categoryForm">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">
        
        <!-- Category Name -->
        <div class="form-group" style="margin-bottom: 2rem;">
            <label for="name" style="display: block; font-weight: 600; color: var(--text); margin-bottom: 0.75rem; font-size: 0.95rem;">
                📝 Category Name <span style="color: #ef4444;">*</span>
            </label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="<?= htmlspecialchars($category['name'] ?? '') ?>"
                required
                placeholder="E.g., PlayStation Games, Xbox Controllers, PC Accessories"
                style="width: 100%; padding: 1rem 1.25rem; background: var(--bg-2); border: 2px solid var(--line); border-radius: 12px; color: var(--text); font-size: 1rem; transition: all 0.3s ease;"
                oninput="generateSlugPreview()"
            >
            <small style="color: var(--muted); font-size: 0.85rem; margin-top: 0.5rem; display: block;">
                Enter a descriptive name for this category
            </small>
        </div>
        
        <!-- Slug Preview -->
        <div class="form-group" style="margin-bottom: 2.5rem;">
            <label style="display: block; font-weight: 600; color: var(--text); margin-bottom: 0.75rem; font-size: 0.95rem;">
                🔗 URL Slug (Auto-generated)
            </label>
            <div style="padding: 1rem 1.25rem; background: var(--bg-2); border: 2px solid var(--line); border-radius: 12px; font-family: monospace; color: #a855f7; font-size: 0.95rem;">
                <span id="slugPreview"><?= htmlspecialchars($category['slug'] ?? 'your-category-slug') ?></span>
            </div>
            <small style="color: var(--muted); font-size: 0.85rem; margin-top: 0.5rem; display: block;">
                This will be used in URLs (automatically generated from the name)
            </small>
        </div>
        
        <!-- Action Buttons -->
        <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--line); padding-top: 2rem;">
            <a href="<?= $basePath ?>/admin/categories" 
               class="btn-secondary"
               style="padding: 0.875rem 1.75rem; background: var(--bg-2); border: 2px solid var(--line); color: var(--text); border-radius: 10px; font-weight: 600; text-decoration: none; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 0.5rem;">
                <span>✕</span>
                <span>Cancel</span>
            </a>
            <button 
                type="submit"
                class="btn-primary"
                style="padding: 0.875rem 1.75rem; background: linear-gradient(135deg, #a855f7 0%, #3af2ff 100%); border: none; color: #000; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 15px rgba(168, 85, 247, 0.3);">
                <span><?= $isEdit ? '💾' : '✓' ?></span>
                <span><?= $isEdit ? 'Update Category' : 'Create Category' ?></span>
            </button>
        </div>
    </form>
</div>

<!-- Info Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 2rem; max-width: 800px;">
    <div style="background: linear-gradient(135deg, rgba(168, 85, 247, 0.1) 0%, rgba(168, 85, 247, 0.05) 100%); border: 1px solid rgba(168, 85, 247, 0.3); border-radius: 12px; padding: 1.5rem;">
        <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">💡</div>
        <h4 style="font-weight: 600; color: var(--text); margin: 0 0 0.5rem 0;">Category Tips</h4>
        <p style="color: var(--muted); font-size: 0.9rem; margin: 0;">Use clear, descriptive names that help users find products easily. Categories help organize your inventory.</p>
    </div>
    
    <div style="background: linear-gradient(135deg, rgba(58, 242, 255, 0.1) 0%, rgba(58, 242, 255, 0.05) 100%); border: 1px solid rgba(58, 242, 255, 0.3); border-radius: 12px; padding: 1.5rem;">
        <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🔗</div>
        <h4 style="font-weight: 600; color: var(--text); margin: 0 0 0.5rem 0;">URL Slugs</h4>
        <p style="color: var(--muted); font-size: 0.9rem; margin: 0;">Slugs are auto-generated from the category name and used in URLs. They're SEO-friendly and human-readable.</p>
    </div>
</div>

<style>
    input:focus {
        outline: none;
        border-color: #a855f7;
        box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1);
    }
    
    .btn-secondary:hover {
        background: var(--bg-1);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(168, 85, 247, 0.4);
    }
    
    .section-header a:hover {
        background: var(--bg-1);
        border-color: #a855f7;
        color: #a855f7;
    }
    
    @media (max-width: 768px) {
        .form-card {
            padding: 1.5rem;
        }
    }
</style>

<script>
    function generateSlugPreview() {
        const nameInput = document.getElementById('name');
        const slugPreview = document.getElementById('slugPreview');
        
        if (nameInput.value) {
            let slug = nameInput.value.toLowerCase().trim();
            slug = slug.replace(/[^a-z0-9-\s]/g, '');
            slug = slug.replace(/\s+/g, '-');
            slug = slug.replace(/-+/g, '-');
            slug = slug.replace(/^-|-$/g, '');
            
            slugPreview.textContent = slug || 'your-category-slug';
        } else {
            slugPreview.textContent = 'your-category-slug';
        }
    }
    
    // Form validation
    document.getElementById('categoryForm').addEventListener('submit', function(e) {
        const nameInput = document.getElementById('name');
        
        if (!nameInput.value.trim()) {
            e.preventDefault();
            alert('⚠️ Category name is required');
            nameInput.focus();
            return false;
        }
        
        return true;
    });
    
    // Auto-hide error alert
    const errorAlert = document.querySelector('.error-alert');
    if (errorAlert) {
        setTimeout(() => {
            errorAlert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            errorAlert.style.opacity = '0';
            errorAlert.style.transform = 'translateY(-20px)';
            setTimeout(() => errorAlert.remove(), 500);
        }, 5000);
    }
    
    // Initialize slug preview on edit
    <?php if ($isEdit): ?>
    generateSlugPreview();
    <?php endif; ?>
</script>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin.php';
