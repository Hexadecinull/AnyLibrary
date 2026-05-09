<?php
/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/response.php';
require_once __DIR__ . '/../includes/openlibrary.php';

$section = $_GET['section'] ?? 'featured';

try {
    switch ($section) {
        case 'featured':
            $books = OpenLibrary::getTrending('daily', 8);
            $out   = [];
            foreach (array_slice($books, 0, 6) as $b) {
                $out[] = [
                    'id'          => $b['id'],
                    'title'       => $b['title'],
                    'authors'     => $b['authors'],
                    'overview'    => '',
                    'cover_url'   => $b['cover_url'],
                    'rating'      => $b['rating'],
                    'year'        => $b['year'],
                    'subjects'    => $b['subjects'],
                    'type'        => $b['type'],
                ];
            }
            jsonSuccess($out);
            break;

        case 'trending_books':
            jsonSuccess(OpenLibrary::getTrending('daily', 12));
            break;

        case 'trending_manga':
            jsonSuccess(MangaDex::trending(12));
            break;

        case 'audiobooks_new':
            jsonSuccess(LibriVox::recent(12));
            break;

        case 'subject':
            $sub = trim($_GET['subject'] ?? 'fantasy');
            $res = OpenLibrary::getSubject($sub, 12);
            jsonSuccess($res['results']);
            break;

        default:
            jsonError('Unknown section', 400);
    }
} catch (Throwable $e) {
    jsonError($e->getMessage(), 500);
}
