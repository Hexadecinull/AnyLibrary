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

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/response.php';

$action = $_GET['action'] ?? '';
$input  = json_decode(file_get_contents('php://input'), true) ?? [];

if ($action === 'login') {
    $email    = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';

    if (!validateEmail($email) || strlen($password) < 8) {
        jsonError('Invalid credentials', 401);
    }

    try {
        $db   = getDB();
        $stmt = $db->prepare('SELECT id, password, display_name FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
    } catch (Throwable) {
        jsonError('Service unavailable', 503);
    }

    if (!$user || !password_verify($password, $user['password'])) {
        jsonError('Invalid credentials', 401);
    }

    $ip        = $_SERVER['REMOTE_ADDR'] ?? '';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $token     = createSession((int) $user['id'], $ip, $userAgent);

    $guestToken = getGuestToken();
    if ($guestToken) mergeGuestData((int) $user['id'], $guestToken);

    jsonSuccess([
        'token' => $token,
        'user'  => [
            'id'           => (int) $user['id'],
            'display_name' => $user['display_name'],
            'email'        => $email,
        ],
    ]);
}

if ($action === 'register') {
    $email       = trim($input['email'] ?? '');
    $password    = $input['password'] ?? '';
    $displayName = trim($input['display_name'] ?? '') ?: explode('@', $email)[0];

    if (!validateEmail($email)) jsonError('Invalid email address', 422);
    if (strlen($password) < 8)  jsonError('Password must be at least 8 characters', 422);
    if (strlen($displayName) > 100) $displayName = substr($displayName, 0, 100);

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $db   = getDB();

    try {
        $db->prepare(
            'INSERT INTO users (email, password, display_name) VALUES (?, ?, ?)'
        )->execute([$email, $hash, $displayName]);

        $userId    = (int) $db->lastInsertId();
        $ip        = $_SERVER['REMOTE_ADDR'] ?? '';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $token     = createSession($userId, $ip, $userAgent);

        $guestToken = getGuestToken();
        if ($guestToken) mergeGuestData($userId, $guestToken);

        jsonSuccess([
            'token' => $token,
            'user'  => [
                'id'           => $userId,
                'display_name' => $displayName,
                'email'        => $email,
            ],
        ], 201);
    } catch (PDOException $e) {
        if ((int) ($e->errorInfo[1] ?? 0) === 1062) jsonError('Email already registered', 409);
        jsonError('Registration failed', 500);
    }
}

if ($action === 'logout') {
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (str_starts_with($header, 'Bearer ')) {
        deleteSession(substr($header, 7));
    }
    jsonSuccess(['success' => true]);
}

if ($action === 'me') {
    $userId = requireAuth();
    try {
        $db   = getDB();
        $stmt = $db->prepare('SELECT id, email, display_name, theme, font, created_at FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
    } catch (Throwable) {
        jsonError('Service unavailable', 503);
    }
    if (!$user) jsonError('User not found', 404);
    jsonSuccess($user);
}

jsonError('Invalid action', 400);
