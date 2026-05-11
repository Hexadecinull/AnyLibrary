<?php
/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

class OpenLibrary {
    private const BASE       = 'https://openlibrary.org';
    private const COVERS     = 'https://covers.openlibrary.org';
    private const CACHE_TTL  = 43200;

    private static function cacheGet(string $key): mixed {
        try {
            $pdo = Db::pdo();
            $row = $pdo->prepare('SELECT data FROM ol_cache WHERE cache_key = ? AND expires_at > NOW()');
            $row->execute([$key]);
            $hit = $row->fetchColumn();
            return $hit !== false ? json_decode($hit, true) : null;
        } catch (Throwable) {
            return null;
        }
    }

    private static function cacheSet(string $key, mixed $data, int $ttl = self::CACHE_TTL): void {
        try {
            $pdo  = Db::pdo();
            $stmt = $pdo->prepare(
                'INSERT INTO ol_cache (cache_key, data, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL ? SECOND))
                 ON DUPLICATE KEY UPDATE data = VALUES(data), expires_at = VALUES(expires_at)'
            );
            $stmt->execute([$key, json_encode($data), $ttl]);
        } catch (Throwable) {
        }
    }

    private static function http(string $url): array {
        if (!function_exists('curl_init')) {
            throw new RuntimeException('cURL is not available on this server.');
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 3,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_ENCODING       => '',
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
                'Accept-Language: en-US,en;q=0.9',
                'User-Agent: Mozilla/5.0 (compatible; AnyLibrary/1.0; +https://github.com/Hexadecinull/AnyLibrary)',
            ],
        ]);
        $raw  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($raw === false || $err) {
            throw new RuntimeException("HTTP request failed ($err): $url");
        }
        if ($code >= 400) {
            throw new RuntimeException("HTTP $code from: $url");
        }
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            $preview = substr(strip_tags($raw), 0, 120);
            throw new RuntimeException("Invalid JSON from: $url — got: $preview");
        }
        return $data;
    }

    public static function searchBooks(string $query, int $page = 1, int $limit = 20, string $lang = ''): array {
        $cacheKey = "search:{$query}:{$page}:{$limit}:{$lang}";
        $cached   = self::cacheGet($cacheKey);
        if ($cached) return $cached;

        $q   = urlencode($query);
        $off = ($page - 1) * $limit;
        $url = self::BASE . "/search.json?q={$q}&limit={$limit}&offset={$off}&fields=key,title,author_name,cover_i,first_publish_year,subject,ratings_average,number_of_pages_median,edition_count";
        if ($lang) $url .= '&language=' . urlencode($lang);

        $raw     = self::http($url);
        $results = array_map([self::class, 'normalizeSearchDoc'], $raw['docs'] ?? []);
        $out     = ['results' => $results, 'total' => $raw['numFound'] ?? 0, 'page' => $page];

        self::cacheSet($cacheKey, $out, 3600);
        return $out;
    }

    public static function getWork(string $workId): array {
        $cacheKey = "work:{$workId}";
        $cached   = self::cacheGet($cacheKey);
        if ($cached) return $cached;

        $work    = self::http(self::BASE . "/works/{$workId}.json");
        $ratings = self::http(self::BASE . "/works/{$workId}/ratings.json");
        $editions = self::http(self::BASE . "/works/{$workId}/editions.json?limit=5");

        $authors = [];
        foreach ($work['authors'] ?? [] as $aRef) {
            $aid = basename($aRef['author']['key'] ?? '');
            if ($aid) {
                try {
                    $aData = self::http(self::BASE . "/authors/{$aid}.json");
                    $authors[] = ['name' => $aData['name'] ?? 'Unknown', 'key' => $aid];
                } catch (\Throwable) {}
            }
        }

        $description = '';
        if (isset($work['description'])) {
            $description = is_array($work['description']) ? ($work['description']['value'] ?? '') : $work['description'];
        }

        $subjects = array_slice($work['subjects'] ?? [], 0, 20);

        $coverId = null;
        if (!empty($work['covers'])) {
            $coverId = $work['covers'][0];
        } elseif (!empty($editions['entries'][0]['covers'])) {
            $coverId = $editions['entries'][0]['covers'][0];
        }

        $out = [
            'id'           => $workId,
            'title'        => $work['title'] ?? 'Unknown Title',
            'authors'      => $authors,
            'description'  => $description,
            'subjects'     => $subjects,
            'cover_id'     => $coverId,
            'cover_url'    => $coverId ? self::COVERS . "/b/id/{$coverId}-L.jpg" : null,
            'rating'       => round($ratings['summary']['average'] ?? 0, 1),
            'rating_count' => $ratings['summary']['count'] ?? 0,
            'first_year'   => $work['first_publish_year'] ?? null,
            'type'         => 'book',
            'links'        => $work['links'] ?? [],
        ];

        self::cacheSet($cacheKey, $out);
        return $out;
    }

    public static function getSubject(string $subject, int $limit = 20, int $offset = 0): array {
        $cacheKey = "subject:{$subject}:{$limit}:{$offset}";
        $cached   = self::cacheGet($cacheKey);
        if ($cached) return $cached;

        $slug = strtolower(str_replace(' ', '_', $subject));
        $raw  = self::http(self::BASE . "/subjects/{$slug}.json?limit={$limit}&offset={$offset}");
        $out  = [
            'name'    => $raw['name'] ?? $subject,
            'count'   => $raw['work_count'] ?? 0,
            'results' => array_map([self::class, 'normalizeSubjectWork'], $raw['works'] ?? []),
        ];

        self::cacheSet($cacheKey, $out, 7200);
        return $out;
    }

    public static function getTrending(string $period = 'daily', int $limit = 20): array {
        $cacheKey = "trending:{$period}:{$limit}";
        $cached   = self::cacheGet($cacheKey);
        if ($cached) return $cached;

        $raw  = self::http(self::BASE . "/trending/{$period}.json?limit={$limit}");
        $out  = array_map([self::class, 'normalizeSearchDoc'], $raw['works'] ?? []);

        self::cacheSet($cacheKey, $out, 3600);
        return $out;
    }

    public static function searchAuthors(string $query, int $limit = 10): array {
        $q   = urlencode($query);
        $raw = self::http(self::BASE . "/search/authors.json?q={$q}&limit={$limit}");
        return $raw['docs'] ?? [];
    }

    public static function coverUrl(int $coverId, string $size = 'M'): string {
        return self::COVERS . "/b/id/{$coverId}-{$size}.jpg";
    }

    private static function normalizeSearchDoc(array $doc): array {
        $coverId = $doc['cover_i'] ?? null;
        return [
            'id'        => basename($doc['key'] ?? ''),
            'key'       => $doc['key'] ?? '',
            'title'     => $doc['title'] ?? 'Unknown',
            'authors'   => $doc['author_name'] ?? [],
            'year'      => $doc['first_publish_year'] ?? null,
            'cover_id'  => $coverId,
            'cover_url' => $coverId ? self::coverUrl($coverId, 'M') : null,
            'rating'    => round($doc['ratings_average'] ?? 0, 1),
            'pages'     => $doc['number_of_pages_median'] ?? null,
            'subjects'  => array_slice($doc['subject'] ?? [], 0, 5),
            'type'      => 'book',
        ];
    }

    private static function normalizeSubjectWork(array $w): array {
        $coverId = $w['cover_id'] ?? null;
        return [
            'id'        => basename($w['key'] ?? ''),
            'key'       => $w['key'] ?? '',
            'title'     => $w['title'] ?? 'Unknown',
            'authors'   => array_column($w['authors'] ?? [], 'name'),
            'year'      => $w['first_publish_year'] ?? null,
            'cover_id'  => $coverId,
            'cover_url' => $coverId ? self::coverUrl($coverId, 'M') : null,
            'rating'    => round($w['rating']['average'] ?? 0, 1),
            'subjects'  => [],
            'type'      => 'book',
        ];
    }
}

class MangaDex {
    private const BASE = 'https://api.mangadex.org';

    private static function http(string $url): array {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 3,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_ENCODING       => '',
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
                'User-Agent: Mozilla/5.0 (compatible; AnyLibrary/1.0; +https://github.com/Hexadecinull/AnyLibrary)',
            ],
        ]);
        $raw  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($raw === false || $code >= 400) {
            throw new RuntimeException("MangaDex HTTP $code: $url");
        }
        $data = json_decode($raw, true);
        if (!is_array($data)) throw new RuntimeException("Invalid JSON from MangaDex: $url");
        return $data;
    }

    public static function search(string $query, int $limit = 20, int $offset = 0, array $tags = []): array {
        $params = http_build_query([
            'title'                     => $query,
            'limit'                     => $limit,
            'offset'                    => $offset,
            'includes[]'                => ['cover_art', 'author'],
            'order[relevance]'          => 'desc',
            'contentRating[]'           => ['safe', 'suggestive'],
        ]);
        $raw = self::http(self::BASE . "/manga?{$params}");
        return [
            'results' => array_map([self::class, 'normalize'], $raw['data'] ?? []),
            'total'   => $raw['total'] ?? 0,
        ];
    }

    public static function getManga(string $id): array {
        $params = http_build_query(['includes[]' => ['cover_art', 'author', 'artist']]);
        $raw    = self::http(self::BASE . "/manga/{$id}?{$params}");
        return self::normalize($raw['data'] ?? []);
    }

    public static function getChapters(string $mangaId, string $lang = 'en', int $limit = 100): array {
        $params = http_build_query([
            'manga'              => $mangaId,
            'translatedLanguage[]' => $lang,
            'order[chapter]'     => 'desc',
            'limit'              => $limit,
        ]);
        $raw = self::http(self::BASE . "/chapter?{$params}");
        return array_map(fn($c) => [
            'id'        => $c['id'],
            'chapter'   => $c['attributes']['chapter'] ?? '?',
            'title'     => $c['attributes']['title'] ?? '',
            'pages'     => $c['attributes']['pages'] ?? 0,
            'lang'      => $c['attributes']['translatedLanguage'] ?? $lang,
            'published' => $c['attributes']['publishAt'] ?? '',
        ], $raw['data'] ?? []);
    }

    public static function getChapterPages(string $chapterId): array {
        $raw = self::http(self::BASE . "/at-home/server/{$chapterId}");
        $base = $raw['baseUrl'] ?? '';
        $hash = $raw['chapter']['hash'] ?? '';
        $data = $raw['chapter']['data'] ?? [];
        return array_map(fn($f) => "{$base}/data/{$hash}/{$f}", $data);
    }

    public static function trending(int $limit = 20): array {
        $params = http_build_query([
            'limit'               => $limit,
            'includes[]'          => ['cover_art', 'author'],
            'order[followedCount]'=> 'desc',
            'contentRating[]'     => ['safe', 'suggestive'],
        ]);
        $raw = self::http(self::BASE . "/manga?{$params}");
        return array_map([self::class, 'normalize'], $raw['data'] ?? []);
    }

    private static function normalize(array $m): array {
        if (empty($m)) return [];
        $attr    = $m['attributes'] ?? [];
        $rels    = $m['relationships'] ?? [];
        $title   = $attr['title']['en'] ?? reset($attr['title'] ?? []) ?? 'Unknown';
        $desc    = $attr['description']['en'] ?? reset($attr['description'] ?? []) ?? '';

        $coverId = null;
        $authors = [];
        foreach ($rels as $r) {
            if ($r['type'] === 'cover_art') $coverId = $r['attributes']['fileName'] ?? null;
            if ($r['type'] === 'author')    $authors[] = $r['attributes']['name'] ?? '';
        }
        $coverUrl = $coverId ? "https://uploads.mangadex.org/covers/{$m['id']}/{$coverId}.256.jpg" : null;

        return [
            'id'       => $m['id'],
            'title'    => $title,
            'authors'  => $authors,
            'desc'     => $desc,
            'status'   => $attr['status'] ?? 'unknown',
            'year'     => $attr['year'] ?? null,
            'tags'     => array_map(fn($t) => $t['attributes']['name']['en'] ?? '', $attr['tags'] ?? []),
            'cover_url'=> $coverUrl,
            'rating'   => round(($attr['rating']['average'] ?? 0), 1),
            'type'     => 'manga',
        ];
    }
}

class LibriVox {
    private const BASE = 'https://librivox.org/api/feed/audiobooks';

    private static function curl(string $url): string|false {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_ENCODING       => '',
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
                'User-Agent: Mozilla/5.0 (compatible; AnyLibrary/1.0; +https://github.com/Hexadecinull/AnyLibrary)',
            ],
        ]);
        $raw  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($raw !== false && $code < 400) ? $raw : false;
    }

    public static function search(string $query, int $limit = 20, int $offset = 0): array {
        $params = http_build_query([
            'title'   => $query,
            'format'  => 'json',
            'extended'=> 1,
            'limit'   => $limit,
            'offset'  => $offset,
        ]);
        $raw = self::curl(self::BASE . "?{$params}");
        if (!$raw) return ['results' => [], 'total' => 0];
        $data = json_decode($raw, true);
        $books = $data['books'] ?? [];
        return ['results' => array_map([self::class, 'normalize'], is_array($books) ? $books : [])];
    }

    public static function getBook(string $id): array {
        $raw  = self::curl(self::BASE . "?id={$id}&format=json&extended=1");
        $data = json_decode($raw ?: '{}', true);
        $books = $data['books'] ?? [];
        return isset($books[0]) ? self::normalize($books[0]) : [];
    }

    public static function recent(int $limit = 20): array {
        $raw  = self::curl(self::BASE . "?format=json&extended=1&limit={$limit}");
        $data = json_decode($raw ?: '{}', true);
        $books = $data['books'] ?? [];
        return array_map([self::class, 'normalize'], is_array($books) ? $books : []);
    }

    private static function normalize(array $b): array {
        $authors = [];
        foreach ($b['authors'] ?? [] as $a) {
            $first = $a['first_name'] ?? '';
            $last  = $a['last_name'] ?? '';
            $authors[] = trim("$first $last");
        }
        return [
            'id'         => $b['id'] ?? '',
            'title'      => $b['title'] ?? 'Unknown',
            'authors'    => $authors,
            'description'=> strip_tags($b['description'] ?? ''),
            'url_zip'    => $b['url_zip_file'] ?? '',
            'url_rss'    => $b['url_rss'] ?? '',
            'cover_url'  => null,
            'sections'   => $b['sections'] ?? [],
            'language'   => $b['language'] ?? 'English',
            'genres'     => array_map(fn($g) => $g['name'] ?? '', $b['genres'] ?? []),
            'year'       => substr($b['copyright_year'] ?? '', 0, 4),
            'type'       => 'audiobook',
        ];
    }
}
