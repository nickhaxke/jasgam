/**
 * Announcement Popup System
 * Fetches and displays active announcements as modals
 */

(function() {
    'use strict';

    // Configuration
    const BASE_PATH = (window.BASE_PATH || '').replace(/\/+$/, '');
    const ANNOUNCEMENT_API = BASE_PATH + '/api/announcements/active';
    const DISMISS_API = BASE_PATH + '/api/announcements/dismiss';
    const STORAGE_KEY = 'dismissed_announcements';
    const CHECK_INTERVAL = 60000; // Check every 60 seconds

    // Get dismissed announcements from localStorage
    function getDismissedAnnouncements() {
        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            return stored ? JSON.parse(stored) : [];
        } catch (e) {
            return [];
        }
    }

    // Save dismissed announcement ID
    function markAsDismissed(announcementId) {
        try {
            const dismissed = getDismissedAnnouncements();
            if (!dismissed.includes(announcementId)) {
                dismissed.push(announcementId);
                localStorage.setItem(STORAGE_KEY, JSON.stringify(dismissed));
            }
        } catch (e) {
            console.error('Failed to save dismissed announcement', e);
        }
    }

    // Check if announcement was already dismissed
    function wasDismissed(announcementId) {
        return getDismissedAnnouncements().includes(announcementId);
    }

    // Create modal HTML
    function createAnnouncementModal(announcement) {
        const modalHTML = `
            <div id="announcement-modal" class="announcement-modal" style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.75);
                backdrop-filter: blur(8px);
                z-index: 999999;
                display: flex;
                align-items: center;
                justify-content: center;
                animation: fadeIn 0.3s ease;
            ">
                <div class="announcement-content" style="
                    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
                    border: 1px solid rgba(65, 209, 255, 0.3);
                    border-radius: 16px;
                    padding: 24px;
                    max-width: 500px;
                    width: 90%;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5), 0 0 100px rgba(65, 209, 255, 0.1);
                    animation: slideUp 0.4s ease;
                    position: relative;
                ">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                        <div style="
                            width: 40px;
                            height: 40px;
                            background: linear-gradient(135deg, #41d1ff, #2196f3);
                            border-radius: 10px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 20px;
                        ">📢</div>
                        <h3 style="
                            color: #fff;
                            margin: 0;
                            font-size: 1.3rem;
                            font-weight: 600;
                        ">${escapeHtml(announcement.title)}</h3>
                    </div>
                    
                    <div style="
                        color: #cbd5e1;
                        font-size: 0.95rem;
                        line-height: 1.6;
                        margin-bottom: 20px;
                    ">${escapeHtml(announcement.message)}</div>
                    
                    <button id="close-announcement" style="
                        background: linear-gradient(135deg, #41d1ff, #2196f3);
                        color: #fff;
                        border: none;
                        padding: 12px 24px;
                        border-radius: 8px;
                        font-weight: 600;
                        cursor: pointer;
                        width: 100%;
                        font-size: 0.95rem;
                        transition: all 0.2s;
                    " onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                        Got it! ✓
                    </button>
                </div>
            </div>
            
            <style>
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                
                @keyframes slideUp {
                    from {
                        transform: translateY(30px);
                        opacity: 0;
                    }
                    to {
                        transform: translateY(0);
                        opacity: 1;
                    }
                }
            </style>
        `;

        return modalHTML;
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Close modal and track dismissal
    function closeModal(announcementId) {
        const modal = document.getElementById('announcement-modal');
        if (modal) {
            modal.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => {
                modal.remove();
            }, 300);
        }

        // Mark as dismissed
        markAsDismissed(announcementId);

        // Track dismissal on server
        fetch(DISMISS_API + '/' + announcementId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        }).catch(err => console.warn('Failed to track dismissal', err));
    }

    // Auto-close timer
    function startAutoClose(announcementId, seconds) {
        if (seconds > 0) {
            setTimeout(() => {
                closeModal(announcementId);
            }, seconds * 1000);
        }
    }

    // Fetch and display announcement
    function fetchAnnouncement() {
        fetch(ANNOUNCEMENT_API)
            .then(response => response.json())
            .then(announcement => {
                if (!announcement || !announcement.id) {
                    return; // No active announcement
                }

                // Check if already dismissed
                if (wasDismissed(announcement.id)) {
                    return;
                }

                // Check target users (if user role is available)
                // For now, show to all users
                
                // Remove existing modal if any
                const existingModal = document.getElementById('announcement-modal');
                if (existingModal) {
                    existingModal.remove();
                }

                // Create and inject modal
                const modalHTML = createAnnouncementModal(announcement);
                document.body.insertAdjacentHTML('beforeend', modalHTML);

                // Add close handler
                const closeBtn = document.getElementById('close-announcement');
                if (closeBtn) {
                    closeBtn.addEventListener('click', () => {
                        closeModal(announcement.id);
                    });
                }

                // Auto-close if configured
                if (announcement.auto_close_seconds) {
                    startAutoClose(announcement.id, announcement.auto_close_seconds);
                }
            })
            .catch(err => {
                console.warn('Failed to fetch announcement', err);
            });
    }

    // Initialize on page load
    function init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fetchAnnouncement);
        } else {
            fetchAnnouncement();
        }

        // Check periodically for new announcements
        setInterval(fetchAnnouncement, CHECK_INTERVAL);
    }

    // Start the system
    init();

    // Add CSS for fadeOut animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    `;
    document.head.appendChild(style);

})();
