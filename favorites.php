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

$pageTitle       = 'Favorites';
$pageDescription = 'Your favorited books, manga, and audiobooks on AnyLibrary.';
$extraCss        = ['/assets/css/browse.css'];
$activePage      = 'favorites';

require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main id="favorites-page">
    <div class="browse-header container">
        <h1 class="browse-title">&#10084;&#65039;&nbsp; Favorites</h1>
        <div style="display:flex;gap:0.4rem;" id="fav-type-tabs">
            <button class="chip active" data-type="all">All</button>
            <button class="chip" data-type="book">Books</button>
            <button class="chip" data-type="manga">Manga</button>
            <button class="chip" data-type="audiobook">Audiobooks</button>
        </div>
    </div>
    <div class="container" style="padding-bottom:5rem;">
        <div class="browse-grid" id="favorites-grid"></div>
        <div class="empty-state" id="favorites-empty" style="display:none;">
            <div class="empty-state-icon">&#10084;&#65039;</div>
            <h3>No favorites yet</h3>
            <p>Tap the heart icon on any book, manga, or audiobook to save it here.</p>
            <a href="/" class="btn btn-primary">Discover books</a>
        </div>
    </div>
</main>

<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/favorites.js"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/app.js"></script>
</body>
</html>
