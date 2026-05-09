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
$action = trim($_GET['action'] ?? 'upload');
$user   = Auth::user();
$guest  = Auth::guestToken();

$maxBytes = (defined('MAX_IMPORT_MB') ? MAX_IMPORT_MB : 50) * 1024 * 1024;
$allowed  = defined('ALLOWED_IMPORT_EXTS') ? ALLOWED_IMPORT_EXTS : ['epub', 'pdf', 'mobi', 'txt', 'html', 'htm', 'cbz', 'cbr'];
$storage  = defined('IMPORT_STORAGE_PATH') ? IMPORT_STORAGE_PATH : __DIR__ . '/../storage/books/';

if (!is_dir($storage)) {
    @mkdir($storage, 0755, true);
}

try {
    $pdo = Db::pdo();

    if ($action === 'upload' && $method === 'POST') {
        if (empty($_FILES['file'])) jsonError('No file uploaded', 400);
        $file = $_FILES['file'];
        if ($file['error'] !== UPLOAD_ERR_OK) jsonError('Upload error: ' . $file['error'], 400);
        if ($file['size'] > $maxBytes) jsonError('File exceeds ' . (MAX_IMPORT_MB ?? 50) . ' MB limit', 413);

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) {
            jsonError('Format not allowed. Accepted: ' . implode(', ', $allowed), 415);
        }

        $title = pathinfo($file['name'], PATHINFO_FILENAME);
        $token = $user ? null : $guest;
        $uid   = $user ? $user['id'] : null;
        $slug  = bin2hex(random_bytes(10)) . '.' . $ext;
        $dest  = $storage . $slug;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            jsonError('Failed to save file', 500);
        }

        $stmt = $pdo->prepare(
            'INSERT INTO imported_books (user_id, guest_token, filename, original_name, file_ext, file_size, title)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$uid, $token, $slug, $file['name'], $ext, $file['size'], $title]);
        $importedId = $pdo->lastInsertId();

        jsonSuccess([
            'id'       => $importedId,
            'title'    => $title,
            'filename' => $slug,
            'ext'      => $ext,
            'size'     => $file['size'],
            'type'     => 'imported',
        ]);
    }

    if ($action === 'list' && $method === 'GET') {
        if ($user) {
            $stmt = $pdo->prepare('SELECT id, title, original_name, file_ext, file_size, created_at FROM imported_books WHERE user_id = ? ORDER BY created_at DESC LIMIT 100');
            $stmt->execute([$user['id']]);
        } else {
            $stmt = $pdo->prepare('SELECT id, title, original_name, file_ext, file_size, created_at FROM imported_books WHERE guest_token = ? ORDER BY created_at DESC LIMIT 100');
            $stmt->execute([$guest]);
        }
        jsonSuccess($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    if ($action === 'delete' && $method === 'POST') {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $id   = (int)($body['id'] ?? 0);
        if (!$id) jsonError('id required', 400);

        if ($user) {
            $stmt = $pdo->prepare('SELECT filename FROM imported_books WHERE id = ? AND user_id = ?');
            $stmt->execute([$id, $user['id']]);
        } else {
            $stmt = $pdo->prepare('SELECT filename FROM imported_books WHERE id = ? AND guest_token = ?');
            $stmt->execute([$id, $guest]);
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) jsonError('Not found', 404);

        @unlink($storage . $row['filename']);

        if ($user) {
            $pdo->prepare('DELETE FROM imported_books WHERE id = ? AND user_id = ?')->execute([$id, $user['id']]);
        } else {
            $pdo->prepare('DELETE FROM imported_books WHERE id = ? AND guest_token = ?')->execute([$id, $guest]);
        }

        jsonSuccess(['deleted' => true]);
    }

    if ($action === 'serve' && $method === 'GET') {
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) jsonError('id required', 400);

        if ($user) {
            $stmt = $pdo->prepare('SELECT filename, original_name, file_ext FROM imported_books WHERE id = ? AND user_id = ?');
            $stmt->execute([$id, $user['id']]);
        } else {
            $stmt = $pdo->prepare('SELECT filename, original_name, file_ext FROM imported_books WHERE id = ? AND guest_token = ?');
            $stmt->execute([$id, $guest]);
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) { http_response_code(404); exit; }

        $path = $storage . $row['filename'];
        if (!file_exists($path)) { http_response_code(404); exit; }

        $mimeMap = [
            'epub' => 'application/epub+zip',
            'pdf'  => 'application/pdf',
            'mobi' => 'application/x-mobipocket-ebook',
            'txt'  => 'text/plain',
            'html' => 'text/html',
            'htm'  => 'text/html',
            'cbz'  => 'application/zip',
            'cbr'  => 'application/x-rar-compressed',
        ];
        $mime = $mimeMap[$row['file_ext']] ?? 'application/octet-stream';

        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . addslashes($row['original_name']) . '"');
        header('Content-Length: ' . filesize($path));
        header('X-Content-Type-Options: nosniff');
        readfile($path);
        exit;
    }

    jsonError('Bad request', 400);
} catch (Throwable $e) {
    jsonError($e->getMessage(), 500);
}
