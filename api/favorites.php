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
$token  = $user ? null : $guest;

try {
    $pdo = Db::pdo();

    if ($method === 'GET' && $action === 'list') {
        if ($user) {
            $stmt = $pdo->prepare(
                'SELECT item_id, item_type, cover_url, title, authors, added_at
                 FROM favorites WHERE user_id = ? ORDER BY added_at DESC LIMIT 200'
            );
            $stmt->execute([$user['id']]);
        } else {
            $stmt = $pdo->prepare(
                'SELECT item_id, item_type, cover_url, title, authors, added_at
                 FROM favorites WHERE guest_token = ? ORDER BY added_at DESC LIMIT 200'
            );
            $stmt->execute([$token]);
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$r) {
            $r['authors'] = json_decode($r['authors'] ?? '[]', true);
        }
        jsonSuccess($rows);
    }

    if ($method === 'POST') {
        $body     = json_decode(file_get_contents('php://input'), true) ?? [];
        $itemId   = trim($body['item_id']   ?? '');
        $itemType = trim($body['item_type'] ?? 'book');
        $coverUrl = trim($body['cover_url'] ?? '');
        $title    = trim($body['title']     ?? '');
        $authors  = $body['authors'] ?? [];

        if (!$itemId) jsonError('item_id required', 400);
        if (!in_array($itemType, ['book', 'manga', 'audiobook'], true)) $itemType = 'book';

        if ($action === 'add') {
            if ($user) {
                $stmt = $pdo->prepare(
                    'INSERT IGNORE INTO favorites (user_id, item_id, item_type, cover_url, title, authors)
                     VALUES (?, ?, ?, ?, ?, ?)'
                );
                $stmt->execute([$user['id'], $itemId, $itemType, $coverUrl, $title, json_encode($authors)]);
            } else {
                $stmt = $pdo->prepare(
                    'INSERT IGNORE INTO favorites (guest_token, item_id, item_type, cover_url, title, authors)
                     VALUES (?, ?, ?, ?, ?, ?)'
                );
                $stmt->execute([$token, $itemId, $itemType, $coverUrl, $title, json_encode($authors)]);
            }
            jsonSuccess(['favorited' => true]);
        }

        if ($action === 'remove') {
            if ($user) {
                $stmt = $pdo->prepare('DELETE FROM favorites WHERE user_id = ? AND item_id = ?');
                $stmt->execute([$user['id'], $itemId]);
            } else {
                $stmt = $pdo->prepare('DELETE FROM favorites WHERE guest_token = ? AND item_id = ?');
                $stmt->execute([$token, $itemId]);
            }
            jsonSuccess(['favorited' => false]);
        }

        if ($action === 'check') {
            if ($user) {
                $stmt = $pdo->prepare('SELECT 1 FROM favorites WHERE user_id = ? AND item_id = ?');
                $stmt->execute([$user['id'], $itemId]);
            } else {
                $stmt = $pdo->prepare('SELECT 1 FROM favorites WHERE guest_token = ? AND item_id = ?');
                $stmt->execute([$token, $itemId]);
            }
            jsonSuccess(['favorited' => (bool)$stmt->fetchColumn()]);
        }
    }

    jsonError('Bad request', 400);
} catch (Throwable $e) {
    jsonError($e->getMessage(), 500);
}
