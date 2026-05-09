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

function verifyJWT(string $token): ?int {
    if (strlen($token) < 16) return null;
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT user_id FROM sessions WHERE id = ? AND expires_at > NOW()'
        );
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        return $row ? (int) $row['user_id'] : null;
    } catch (Throwable) {
        return null;
    }
}

function generateSessionToken(): string {
    return bin2hex(random_bytes(32));
}

function createSession(int $userId, string $ip = '', string $userAgent = ''): string {
    $token = generateSessionToken();
    $db    = getDB();
    $db->prepare(
        'INSERT INTO sessions (id, user_id, ip, user_agent, expires_at)
         VALUES (?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY))'
    )->execute([$token, $userId, $ip, $userAgent]);
    return $token;
}

function deleteSession(string $token): void {
    try {
        $db = getDB();
        $db->prepare('DELETE FROM sessions WHERE id = ?')->execute([$token]);
    } catch (Throwable) {}
}

function pruneExpiredSessions(): void {
    try {
        $db = getDB();
        $db->exec('DELETE FROM sessions WHERE expires_at < NOW()');
    } catch (Throwable) {}
}

function mergeGuestData(int $userId, string $guestToken): void {
    if (!$guestToken) return;
    try {
        $db = getDB();

        $db->prepare(
            'UPDATE reading_history SET user_id = ?, guest_token = NULL
             WHERE guest_token = ? AND user_id IS NULL'
        )->execute([$userId, $guestToken]);

        $db->prepare(
            'INSERT IGNORE INTO favorites (user_id, catalog_id, added_at)
             SELECT ?, catalog_id, added_at FROM favorites
             WHERE guest_token = ? AND user_id IS NULL'
        )->execute([$userId, $guestToken]);

        $db->prepare(
            'DELETE FROM favorites WHERE guest_token = ? AND user_id IS NULL'
        )->execute([$guestToken]);
    } catch (Throwable) {}
}
