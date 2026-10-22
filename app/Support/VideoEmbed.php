<?php

namespace App\Support;

class VideoEmbed
{
    /**
     * Mengenali URL video.
     *
     * @return array{provider: string, id?: string, url?: string}|null
     *         provider: youtube | vimeo | file
     */
    public static function parse(?string $url): ?array
    {
        if (! $url) {
            return null;
        }

        if (preg_match('~^https?://(?:www\.|m\.)?(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})~i', $url, $m)) {
            return ['provider' => 'youtube', 'id' => $m[1]];
        }

        if (preg_match('~^https?://(?:www\.)?vimeo\.com/(?:video/)?(\d+)~i', $url, $m)) {
            return ['provider' => 'vimeo', 'id' => $m[1]];
        }

        if (preg_match('~^https?://.+\.(mp4|webm|ogg|m3u8)(\?.*)?$~i', $url)) {
            return ['provider' => 'file', 'url' => $url];
        }

        return null;
    }
}
