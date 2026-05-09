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

$query = trim($_GET['q'] ?? '');
$type  = in_array($_GET['type'] ?? 'all', ['all', 'book', 'manga', 'audiobook'], true)
    ? ($_GET['type'] ?? 'all')
    : 'all';

$pageTitle       = $query ? "Search: $query" : 'Search';
$pageDescription = $query ? "Search results for "$query" on AnyLibrary." : 'Search for books, manga, and audiobooks.';
$extraCss        = ['/assets/css/browse.css'];
$activePage      = 'search';

require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main id="search-page"
      data-query="<?= htmlspecialchars($query) ?>"
      data-type="<?= htmlspecialchars($type) ?>">
    <div class="browse-header container">
        <h1 class="browse-title">
            <?php if ($query): ?>
                Results for <em style="color:var(--c-accent);">"<?= htmlspecialchars($query) ?>"</em>
            <?php else: ?>
                Search
            <?php endif; ?>
        </h1>
        <div style="display:flex;gap:0.4rem;flex-wrap:wrap;align-items:center;">
            <div class="chip-group" id="search-type-tabs">
                <button class="chip <?= $type==='all'       ?'active':'' ?>" data-type="all">All</button>
                <button class="chip <?= $type==='book'      ?'active':'' ?>" data-type="book">Books</button>
                <button class="chip <?= $type==='manga'     ?'active':'' ?>" data-type="manga">Manga</button>
                <button class="chip <?= $type==='audiobook' ?'active':'' ?>" data-type="audiobook">Audiobooks</button>
            </div>
        </div>
    </div>
    <div class="container" style="padding-bottom:5rem;">
        <?php if (!$query): ?>
            <div class="empty-state">
                <div class="empty-state-icon">&#128270;</div>
                <h3>What are you looking for?</h3>
                <p>Search by title, author, ISBN, or subject to find books, manga, and audiobooks.</p>
            </div>
        <?php else: ?>
            <div class="browse-grid" id="search-grid"></div>
            <div style="text-align:center;padding:2.5rem 0;display:none;" id="search-load-more-wrap">
                <button class="btn btn-secondary" id="search-load-more">Load more</button>
            </div>
            <div class="empty-state" id="search-empty" style="display:none;">
                <div class="empty-state-icon">&#128270;</div>
                <h3>No results</h3>
                <p>Nothing matched <strong>"<?= htmlspecialchars($query) ?>"</strong>. Try a different search.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/search.js"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/app.js"></script>
</body>
</html>
