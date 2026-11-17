<?php

namespace App\Support;

/**
 * Mengambil ID video YouTube dari berbagai bentuk tautan, lalu membangun alamat embed.
 * Alamat iframe SELALU dibuat dari ID yang sudah divalidasi (11 karakter), tidak pernah dari
 * teks mentah, sehingga data di database tidak bisa dipakai untuk menyisipkan alamat lain.
 */
class YouTube
{
    private const ID_PATTERN = '/^[A-Za-z0-9_-]{11}$/';

    public static function id(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if (preg_match(self::ID_PATTERN, $url)) {
            return $url;
        }

        $parts = parse_url($url);

        if (! is_array($parts) || empty($parts['host'])) {
            return null;
        }

        $host = preg_replace('/^(www|m|music)\./', '', strtolower($parts['host']));
        $path = $parts['path'] ?? '';
        $id = null;

        if ($host === 'youtu.be') {
            $id = trim($path, '/');
        } elseif (in_array($host, ['youtube.com', 'youtube-nocookie.com'], true)) {
            if ($path === '/watch') {
                parse_str($parts['query'] ?? '', $query);
                $id = $query['v'] ?? null;
            } elseif (preg_match('#^/(embed|shorts|live|v)/([^/?]+)#', $path, $m)) {
                $id = $m[2];
            }
        }

        return is_string($id) && preg_match(self::ID_PATTERN, $id) ? $id : null;
    }

    /** Domain "nocookie" tidak memasang cookie pelacak sebelum video diputar. */
    public static function embedUrl(string $id): string
    {
        return 'https://www.youtube-nocookie.com/embed/' . $id . '?rel=0&modestbranding=1';
    }
}
