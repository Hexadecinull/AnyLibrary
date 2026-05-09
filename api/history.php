<?php
/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/response.php';
require_once __DIR__ . '/../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = trim($_GET['action'] ?? 'list');
$user   = Auth::user();
$guest  = Auth::guestToken();

try {
    $pdo = Db::pdo();

    if ($method === 'GET' && $action === 'list') {
        if ($user) {
            $stmt = $pdo->prepare(
                'SELECT item_id, item_type, cover_url, title, authors,
                        progress_pct, last_position, last_read_at
                 FROM reading_history WHERE user_id = ?
                 ORDER BY last_read_at DESC LIMIT 200'
            );
            $stmt->execute([$user['id']]);
        } else {
            $stmt = $pdo->prepare(
                'SELECT item_id, item_type, cover_url, title, authors,
                        progress_pct, last_position, last_read_at
                 FROM reading_history WHERE guest_token = ?
                 ORDER BY last_read_at DESC LIMIT 200'
            );
            $stmt->execute([$guest]);
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$r) {
            $r['authors']       = json_decode($r['authors'] ?? '[]', true);
            $r['last_position'] = json_decode($r['last_position'] ?? 'null', true);
        }
        jsonSuccess($rows);
    }

    if ($method === 'POST') {
        $body         = json_decode(file_get_contents('php://input'), true) ?? [];
        $itemId       = trim($body['item_id']       ?? '');
        $itemType     = trim($body['item_type']     ?? 'book');
        $coverUrl     = trim($body['cover_url']     ?? '');
        $title        = trim($body['title']         ?? '');
        $authors      = $body['authors']      ?? [];
        $progressPct  = min(100, max(0, (float)($body['progress_pct']  ?? 0)));
        $lastPosition = $body['last_position'] ?? null;

        if (!$itemId) jsonError('item_id required', 400);

        if ($action === 'upsert') {
            if ($user) {
                $stmt = $pdo->prepare(
                    'INSERT INTO reading_history
                        (user_id, item_id, item_type, cover_url, title, authors, progress_pct, last_position, last_read_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                     ON DUPLICATE KEY UPDATE
                        cover_url     = VALUES(cover_url),
                        title         = VALUES(title),
                        authors       = VALUES(authors),
                        progress_pct  = VALUES(progress_pct),
                        last_position = VALUES(last_position),
                        last_read_at  = NOW()'
                );
                $stmt->execute([
                    $user['id'], $itemId, $itemType, $coverUrl, $title,
                    json_encode($authors), $progressPct, json_encode($lastPosition),
                ]);
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO reading_history
                        (guest_token, item_id, item_type, cover_url, title, authors, progress_pct, last_position, last_read_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                     ON DUPLICATE KEY UPDATE
                        cover_url     = VALUES(cover_url),
                        title         = VALUES(title),
                        authors       = VALUES(authors),
                        progress_pct  = VALUES(progress_pct),
                        last_position = VALUES(last_position),
                        last_read_at  = NOW()'
                );
                $stmt->execute([
                    $guest, $itemId, $itemType, $coverUrl, $title,
                    json_encode($authors), $progressPct, json_encode($lastPosition),
                ]);
            }
            jsonSuccess(['saved' => true]);
        }

        if ($action === 'remove') {
            if ($user) {
                $stmt = $pdo->prepare('DELETE FROM reading_history WHERE user_id = ? AND item_id = ?');
                $stmt->execute([$user['id'], $itemId]);
            } else {
                $stmt = $pdo->prepare('DELETE FROM reading_history WHERE guest_token = ? AND item_id = ?');
                $stmt->execute([$guest, $itemId]);
            }
            jsonSuccess(['removed' => true]);
        }

        if ($action === 'clear') {
            if ($user) {
                $pdo->prepare('DELETE FROM reading_history WHERE user_id = ?')->execute([$user['id']]);
            } else {
                $pdo->prepare('DELETE FROM reading_history WHERE guest_token = ?')->execute([$guest]);
            }
            jsonSuccess(['cleared' => true]);
        }
    }

    jsonError('Bad request', 400);
} catch (Throwable $e) {
    jsonError($e->getMessage(), 500);
}
