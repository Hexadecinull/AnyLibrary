<?php
/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/response.php';
require_once __DIR__ . '/../includes/openlibrary.php';

$period = in_array($_GET['period'] ?? 'daily', ['daily', 'weekly', 'monthly', 'yearly'], true)
    ? $_GET['period']
    : 'daily';
$limit = min(60, max(12, (int)($_GET['limit'] ?? 20)));

try {
    $books = OpenLibrary::getTrending($period, $limit);
    jsonSuccess(['results' => $books, 'period' => $period]);
} catch (Throwable $e) {
    jsonError($e->getMessage(), 500);
}
