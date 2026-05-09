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

$pageTitle       = 'Reading History';
$pageDescription = 'Your reading history on AnyLibrary. Pick up where you left off.';
$extraCss        = ['/assets/css/browse.css'];
$activePage      = 'history';

require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main id="history-page">
    <div class="browse-header container">
        <div style="display:flex;align-items:center;justify-content:space-between;width:100%;flex-wrap:wrap;gap:1rem;">
            <h1 class="browse-title">&#128336;&nbsp; Reading History</h1>
            <button class="btn btn-ghost btn-sm" id="clear-all-history-btn" style="color:var(--c-red);">Clear all</button>
        </div>
    </div>
    <div class="container" style="padding-bottom:5rem;">
        <div class="browse-grid" id="history-grid"></div>
        <div class="empty-state" id="history-empty" style="display:none;">
            <div class="empty-state-icon">&#128336;</div>
            <h3>Nothing here yet</h3>
            <p>Books, manga, and audiobooks you open will appear here so you can easily continue reading.</p>
            <a href="/" class="btn btn-primary">Start reading</a>
        </div>
    </div>
</main>

<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/history.js"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/app.js"></script>
</body>
</html>
