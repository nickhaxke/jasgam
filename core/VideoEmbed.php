<?php

namespace Core;

class VideoEmbed
{
    /**
     * Detect video type from URL and return embed data.
     */
    public static function parse(string $url): array
    {
        $url = trim($url);

        // YouTube: youtube.com/watch?v=ID, youtu.be/ID, youtube.com/embed/ID
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_\-]{11})/', $url, $m)) {
            return [
                'type'      => 'youtube',
                'video_id'  => $m[1],
                'embed_url' => 'https://www.youtube.com/embed/' . $m[1],
                'thumbnail' => 'https://img.youtube.com/vi/' . $m[1] . '/maxresdefault.jpg',
            ];
        }

        // Vimeo: vimeo.com/123456
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
            return [
                'type'      => 'vimeo',
                'video_id'  => $m[1],
                'embed_url' => 'https://player.vimeo.com/video/' . $m[1],
                'thumbnail' => null,
            ];
        }

        // Direct video file (MP4, WebM, OGG)
        if (preg_match('/\.(mp4|webm|ogg)(\?|$)/i', $url)) {
            return [
                'type'      => 'mp4',
                'video_id'  => null,
                'embed_url' => $url,
                'thumbnail' => null,
            ];
        }

        return [
            'type'      => 'unknown',
            'video_id'  => null,
            'embed_url' => $url,
            'thumbnail' => null,
        ];
    }

    /**
     * Render an iframe or video element for the given URL.
     *
     * @param string $url   Video URL
     * @param array  $attrs Options: class, autoplay, muted, loop
     */
    public static function renderPlayer(string $url, array $attrs = []): string
    {
        $data     = self::parse($url);
        $class    = htmlspecialchars($attrs['class'] ?? '', ENT_QUOTES);
        $autoplay = !empty($attrs['autoplay']);
        $muted    = !empty($attrs['muted']);
        $loop     = !empty($attrs['loop']);

        switch ($data['type']) {
            case 'youtube':
                $params = '?rel=0&modestbranding=1';
                if ($autoplay) $params .= '&autoplay=1&mute=1';
                if ($loop)     $params .= '&loop=1&playlist=' . $data['video_id'];
                return '<iframe class="' . $class . '" src="' . htmlspecialchars($data['embed_url'] . $params)
                    . '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen loading="lazy"></iframe>';

            case 'vimeo':
                $params = '?title=0&byline=0&portrait=0';
                if ($autoplay) $params .= '&autoplay=1&muted=1';
                if ($loop)     $params .= '&loop=1';
                return '<iframe class="' . $class . '" src="' . htmlspecialchars($data['embed_url'] . $params)
                    . '" frameborder="0" allow="autoplay" allowfullscreen loading="lazy"></iframe>';

            case 'mp4':
                $a = $autoplay ? ' autoplay' : '';
                $m = $muted    ? ' muted'    : '';
                $l = $loop     ? ' loop'     : '';
                return '<video class="' . $class . '" src="' . htmlspecialchars($data['embed_url'])
                    . '"' . $a . $m . $l . ' playsinline preload="metadata"></video>';

            default:
                return '<a href="' . htmlspecialchars($url) . '" target="_blank" rel="noopener" class="btn btn-glass">Watch Video</a>';
        }
    }
}
