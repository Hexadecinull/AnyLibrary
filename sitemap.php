<?php
/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

require_once __DIR__ . '/includes/bootstrap.php';

header('Content-Type: application/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';

$base  = defined('APP_URL') ? rtrim(APP_URL, '/') : 'https://anylibrary.example.com';
$today = date('Y-m-d');

$static = [
    ['loc' => '/',           'changefreq' => 'daily',   'priority' => '1.0'],
    ['loc' => '/browse',     'changefreq' => 'daily',   'priority' => '0.9'],
    ['loc' => '/manga',      'changefreq' => 'daily',   'priority' => '0.9'],
    ['loc' => '/audiobooks', 'changefreq' => 'daily',   'priority' => '0.9'],
    ['loc' => '/trending',   'changefreq' => 'hourly',  'priority' => '0.8'],
    ['loc' => '/search',     'changefreq' => 'monthly', 'priority' => '0.5'],
    ['loc' => '/privacy',    'changefreq' => 'yearly',  'priority' => '0.3'],
    ['loc' => '/terms',      'changefreq' => 'yearly',  'priority' => '0.3'],
];

echo "\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
foreach ($static as $p) {
    $loc = htmlspecialchars($base . $p['loc'], ENT_XML1, 'UTF-8');
    echo "\n  <url><loc>{$loc}</loc><lastmod>{$today}</lastmod>"
        . "<changefreq>{$p['changefreq']}</changefreq><priority>{$p['priority']}</priority></url>";
}

try {
    $pdo  = Db::pdo();
    $rows = $pdo->query("SELECT cache_key FROM ol_cache WHERE cache_key LIKE 'work:%' LIMIT 2000")
                ->fetchAll(PDO::FETCH_COLUMN);
    foreach ($rows as $key) {
        $id  = str_replace('work:', '', $key);
        if (!$id) continue;
        $loc = htmlspecialchars($base . '/detail?id=' . urlencode($id) . '&type=book', ENT_XML1, 'UTF-8');
        echo "\n  <url><loc>{$loc}</loc><changefreq>weekly</changefreq><priority>0.6</priority></url>";
    }
} catch (Throwable) {}

echo "\n</urlset>\n";
