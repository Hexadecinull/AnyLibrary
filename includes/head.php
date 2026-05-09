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

if (!isset($pageTitle))       $pageTitle       = 'AnyLibrary';
if (!isset($pageDescription)) $pageDescription = 'Read everything. Own nothing. Pay nothing.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — AnyLibrary</title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta name="robots" content="index, follow">
    <link rel="icon" type="image/svg+xml" href="/assets/img/logo-mark.svg">
    <link rel="canonical" href="<?= htmlspecialchars((defined('APP_URL') ? APP_URL : '') . strtok($_SERVER['REQUEST_URI'] ?? '/', '?')) ?>">

    <!--
        Fonts loaded from Bunny Fonts CDN (privacy-friendly, GDPR compliant, no Google tracking).
        DM Mono: https://fonts.bunny.net
        Space Grotesk: https://fonts.bunny.net
        If you self-host the WOFF2 files in /assets/fonts/, the @font-face in tokens.css
        will serve them locally and these CDN links become unused fallbacks.
    -->
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=dm-mono:400|space-grotesk:300,400,500,600,700&display=swap">
    <!--
        Satoshi isn't on Bunny Fonts — load from Fontshare (free, no tracking).
    -->
    <link rel="preconnect" href="https://api.fontshare.com" crossorigin>
    <link rel="stylesheet" href="https://api.fontshare.com/v2/css?f[]=satoshi@1,2,3,4,5,6,7,8,9&display=swap">

    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/reset.css">
    <link rel="stylesheet" href="/assets/css/tokens.css">
    <link rel="stylesheet" href="/assets/css/themes.css">
    <link rel="stylesheet" href="/assets/css/layout.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/nav.css">
    <?php if (isset($extraCss)): foreach ((array) $extraCss as $css): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
    <?php endforeach; endif; ?>

    <!-- SVG icon sprite (inline so icons are available immediately) -->
    <?php
    $spritePath = __DIR__ . '/../assets/icons/sprite.svg';
    if (file_exists($spritePath)) {
        readfile($spritePath);
    }
    ?>
</head>
<body>
