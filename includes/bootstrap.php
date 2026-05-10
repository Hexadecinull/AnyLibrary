<?php
/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

$_config = __DIR__ . '/config.php';
if (!file_exists($_config)) {
    http_response_code(503);
    echo json_encode(['success' => false, 'error' => 'config.php not found — see includes/config.example.php']);
    exit;
}
require_once $_config;
require_once __DIR__ . '/db.php';

if (defined('APP_ENV') && APP_ENV === 'development') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

set_exception_handler(function (Throwable $e): void {
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
    }
    $msg = defined('APP_ENV') && APP_ENV === 'development'
        ? $e->getMessage() . ' in ' . basename($e->getFile()) . ':' . $e->getLine()
        : $e->getMessage();
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
});

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
