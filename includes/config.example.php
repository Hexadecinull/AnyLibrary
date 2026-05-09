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

// ─── Database ────────────────────────────────────────────────────────────────
// InfinityFree example: sql123.infinityfree.com
define('DB_HOST', 'localhost');
// InfinityFree example: epiz_XXXXXXX_anylibrary
define('DB_NAME', 'anylibrary');
// InfinityFree example: epiz_XXXXXXX
define('DB_USER', 'root');
define('DB_PASS', '');

// ─── Open Library ────────────────────────────────────────────────────────────
// No API key needed for Open Library. https://openlibrary.org/developers/api
// Set your preferred language for book metadata
define('OL_LANG', 'en');

// ─── MangaDex ────────────────────────────────────────────────────────────────
// No API key required for public endpoints. https://api.mangadex.org
// Optionally set a MangaDex username/password here for higher rate limits.
define('MANGADEX_USER', '');
define('MANGADEX_PASS', '');

// ─── LibriVox ────────────────────────────────────────────────────────────────
// No API key required. https://librivox.org/api/
// Public domain audiobooks, no authentication needed.

// ─── Session Secret ──────────────────────────────────────────────────────────
// IMPORTANT: Must be a static, long, random string.
// Never use random_bytes() here — it regenerates on every request and breaks all sessions.
// Generate once with: php -r "echo bin2hex(random_bytes(32));"
define('JWT_SECRET', 'REPLACE_WITH_A_LONG_RANDOM_STRING');

// ─── Application ─────────────────────────────────────────────────────────────
define('APP_URL',  'https://anylibrary.example.com');
define('APP_ENV',  'production');    // Set to 'development' locally to see PHP errors

// ─── File Import ─────────────────────────────────────────────────────────────
// Maximum upload size in MB for imported books
define('MAX_IMPORT_MB', 50);
// Allowed import formats
define('ALLOWED_IMPORT_EXTS', ['epub', 'pdf', 'mobi', 'txt', 'html', 'htm', 'cbz', 'cbr']);
// Where to store imported book files (must be writable, outside webroot ideally)
define('IMPORT_STORAGE_PATH', __DIR__ . '/../storage/books/');

// ─── Internal ────────────────────────────────────────────────────────────────
define('GUEST_TOKEN_HEADER', 'HTTP_X_GUEST_TOKEN');
