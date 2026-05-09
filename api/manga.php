<?php
/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/response.php';
require_once __DIR__ . '/../includes/openlibrary.php';

$action = trim($_GET['action'] ?? 'search');

try {
    switch ($action) {
        case 'search':
            $q      = trim($_GET['q']    ?? '');
            $limit  = min(40, max(12, (int)($_GET['limit'] ?? 20)));
            $offset = max(0, (int)($_GET['offset'] ?? 0));
            $tag    = trim($_GET['tag'] ?? '');
            $res    = MangaDex::search($q, $limit, $offset, $tag ? [$tag] : []);
            jsonSuccess($res);
            break;

        case 'trending':
            $limit = min(40, max(12, (int)($_GET['limit'] ?? 20)));
            jsonSuccess(MangaDex::trending($limit));
            break;

        case 'chapter_pages':
            $chapterId = trim($_GET['chapter_id'] ?? '');
            if (!$chapterId) jsonError('chapter_id required', 400);
            $pages = MangaDex::getChapterPages($chapterId);
            jsonSuccess(['pages' => $pages, 'count' => count($pages)]);
            break;

        default:
            jsonError('Unknown action', 400);
    }
} catch (Throwable $e) {
    jsonError($e->getMessage(), 500);
}
