<?php
/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/response.php';
require_once __DIR__ . '/../includes/openlibrary.php';

$type   = trim($_GET['type']  ?? 'book');
$genre  = trim($_GET['genre'] ?? '');
$sort   = trim($_GET['sort']  ?? 'trending');
$lang   = trim($_GET['lang']  ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = min(40, max(12, (int)($_GET['limit'] ?? 20)));

if (!in_array($type, ['book', 'manga', 'audiobook'], true)) $type = 'book';

try {
    $offset = ($page - 1) * $limit;

    switch ($type) {
        case 'book':
            if ($genre) {
                $res = OpenLibrary::getSubject($genre, $limit, $offset);
                jsonSuccess(['results' => $res['results'], 'total' => $res['count'], 'page' => $page]);
            } else {
                $period = match($sort) {
                    'newest'  => 'daily',
                    'rating'  => 'weekly',
                    'title'   => 'monthly',
                    default   => 'daily',
                };
                $books = OpenLibrary::getTrending($period, $limit);
                jsonSuccess(['results' => $books, 'total' => count($books) + $limit, 'page' => $page]);
            }
            break;

        case 'manga':
            $res = MangaDex::search($genre ?: '', $limit, $offset);
            jsonSuccess(['results' => $res['results'], 'total' => $res['total'], 'page' => $page]);
            break;

        case 'audiobook':
            $res = LibriVox::search($genre, $limit, $offset);
            jsonSuccess(['results' => $res['results'], 'total' => count($res['results']) + $limit, 'page' => $page]);
            break;

        default:
            jsonError('Invalid type', 400);
    }
} catch (Throwable $e) {
    jsonError($e->getMessage(), 500);
}
