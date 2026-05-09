<?php
/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/response.php';
require_once __DIR__ . '/../includes/openlibrary.php';

$id   = trim($_GET['id']   ?? '');
$type = trim($_GET['type'] ?? 'book');

if ($id === '') jsonError('id is required', 400);
if (!in_array($type, ['book', 'manga', 'audiobook'], true)) $type = 'book';

try {
    switch ($type) {
        case 'book':
            $data = OpenLibrary::getWork($id);
            if (empty($data)) jsonError('Book not found', 404);

            $related = [];
            if (!empty($data['subjects'])) {
                $sub     = preg_replace('/[^a-zA-Z0-9 _]/', '', $data['subjects'][0] ?? '');
                $subRes  = OpenLibrary::getSubject($sub, 7);
                $related = array_filter($subRes['results'], fn($r) => $r['id'] !== $id);
                $related = array_values(array_slice($related, 0, 6));
            }
            $data['related'] = $related;
            jsonSuccess($data);
            break;

        case 'manga':
            $manga    = MangaDex::getManga($id);
            if (empty($manga)) jsonError('Manga not found', 404);
            $chapters = MangaDex::getChapters($id, 'en', 100);
            $manga['chapters'] = $chapters;
            jsonSuccess($manga);
            break;

        case 'audiobook':
            $book = LibriVox::getBook($id);
            if (empty($book)) jsonError('Audiobook not found', 404);
            jsonSuccess($book);
            break;

        default:
            jsonError('Invalid type', 400);
    }
} catch (Throwable $e) {
    jsonError($e->getMessage(), 500);
}
