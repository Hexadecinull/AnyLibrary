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

$id   = trim($_GET['id']   ?? '');
$type = trim($_GET['type'] ?? 'book');

if (!$id) { header('Location: /'); exit; }
if (!in_array($type, ['book', 'manga', 'audiobook'], true)) $type = 'book';

$pageTitle       = 'Loading…';
$pageDescription = 'Book details on AnyLibrary.';
$extraCss        = ['/assets/css/detail.css'];
$activePage      = '';

require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main id="detail-page" data-id="<?= htmlspecialchars($id) ?>" data-type="<?= htmlspecialchars($type) ?>">
    <div id="detail-backdrop" class="detail-backdrop"></div>
    <div class="container">
        <div class="detail-back-row">
            <button onclick="history.back()" class="back-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
                Back
            </button>
        </div>

        <div class="detail-container" id="detail-container">
            <div class="detail-poster skeleton" id="detail-poster" style="aspect-ratio:2/3;border-radius:var(--radius-l);"></div>
            <div class="detail-info" id="detail-info">
                <div class="skeleton" style="height:2.2rem;width:70%;border-radius:var(--radius-m);margin-bottom:0.75rem;"></div>
                <div class="skeleton" style="height:1rem;width:40%;border-radius:var(--radius-m);margin-bottom:1.25rem;"></div>
                <div class="skeleton" style="height:0.85rem;width:90%;border-radius:var(--radius-s);margin-bottom:0.4rem;"></div>
                <div class="skeleton" style="height:0.85rem;width:80%;border-radius:var(--radius-s);margin-bottom:0.4rem;"></div>
                <div class="skeleton" style="height:0.85rem;width:60%;border-radius:var(--radius-s);"></div>
            </div>
        </div>

        <div id="detail-sections"></div>
    </div>
</main>

<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/detail.js"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/app.js"></script>
</body>
</html>
