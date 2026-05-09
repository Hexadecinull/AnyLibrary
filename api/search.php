<?php
/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/response.php';
require_once __DIR__ . '/../includes/openlibrary.php';

$query  = trim($_GET['q']    ?? '');
$type   = trim($_GET['type'] ?? 'all');
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = min(40, max(10, (int)($_GET['limit'] ?? 20)));

if ($query === '') {
    jsonError('Query is required', 400);
}

if (!in_array($type, ['all', 'book', 'manga', 'audiobook'], true)) $type = 'all';

try {
    $results = [];
    $total   = 0;

    if ($type === 'all' || $type === 'book') {
        $res      = OpenLibrary::searchBooks($query, $page, $limit);
        $results  = array_merge($results, $res['results']);
        $total   += $res['total'];
    }

    if ($type === 'all' || $type === 'manga') {
        $res     = MangaDex::search($query, (int)($limit / ($type === 'all' ? 3 : 1)));
        $results = array_merge($results, $res['results']);
        $total  += $res['total'];
    }

    if ($type === 'all' || $type === 'audiobook') {
        $res     = LibriVox::search($query, (int)($limit / ($type === 'all' ? 3 : 1)));
        $results = array_merge($results, $res['results']);
    }

    jsonSuccess(['results' => $results, 'total' => $total, 'page' => $page, 'query' => $query]);
} catch (Throwable $e) {
    jsonError($e->getMessage(), 500);
}
