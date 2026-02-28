/**
 * JUSGAM Video Embed Utilities
 * Card hover previews, hero background video, screenshot lightbox
 */
var VideoEmbed = (function() {

    function parse(url) {
        url = (url || '').trim();

        // YouTube
        var yt = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_\-]{11})/);
        if (yt) return { type: 'youtube', videoId: yt[1], embedUrl: 'https://www.youtube.com/embed/' + yt[1] };

        // Vimeo
        var vm = url.match(/vimeo\.com\/(\d+)/);
        if (vm) return { type: 'vimeo', videoId: vm[1], embedUrl: 'https://player.vimeo.com/video/' + vm[1] };

        // Direct MP4/WebM/OGG
        if (/\.(mp4|webm|ogg)(\?|$)/i.test(url)) return { type: 'mp4', videoId: null, embedUrl: url };

        return { type: 'unknown', videoId: null, embedUrl: url };
    }

    function initCardPreviews() {
        var cards = document.querySelectorAll('.gc-img[data-preview-video]');
        if (!cards.length) return;

        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                var el = entry.target;
                if (entry.isIntersecting && !el._videoReady) {
                    el._videoReady = true;
                    var url = el.getAttribute('data-preview-video');
                    var parsed = parse(url);

                    // Only do hover preview for direct video files (iframes are too slow)
                    if (parsed.type === 'mp4') {
                        var video = document.createElement('video');
                        video.className = 'gc-preview-video';
                        video.src = parsed.embedUrl;
                        video.muted = true;
                        video.loop = true;
                        video.playsInline = true;
                        video.preload = 'metadata';
                        video.setAttribute('playsinline', '');
                        el.insertBefore(video, el.firstChild);
                        el._previewVideo = video;
                    }
                }
            });
        }, { rootMargin: '200px' });

        cards.forEach(function(card) {
            observer.observe(card);

            var gameCard = card.closest('.game-card');
            if (gameCard) {
                gameCard.addEventListener('mouseenter', function() {
                    if (card._previewVideo) {
                        card._previewVideo.currentTime = 0;
                        card._previewVideo.play().catch(function() {});
                        card._previewVideo.classList.add('playing');
                    }
                });
                gameCard.addEventListener('mouseleave', function() {
                    if (card._previewVideo) {
                        card._previewVideo.pause();
                        card._previewVideo.classList.remove('playing');
                    }
                });
            }
        });
    }

    function initLightbox() {
        document.querySelectorAll('[data-lightbox]').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                var src = this.href || this.querySelector('img').src;
                var overlay = document.createElement('div');
                overlay.className = 'lightbox-overlay';
                overlay.innerHTML = '<img src="' + src + '" alt="Screenshot">';
                overlay.addEventListener('click', function() { this.remove(); });
                document.addEventListener('keydown', function handler(ev) {
                    if (ev.key === 'Escape') { overlay.remove(); document.removeEventListener('keydown', handler); }
                });
                document.body.appendChild(overlay);
            });
        });
    }

    // Auto-initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        initCardPreviews();
        initLightbox();
    });

    return {
        parse: parse,
        initCardPreviews: initCardPreviews,
        initLightbox: initLightbox
    };
})();
