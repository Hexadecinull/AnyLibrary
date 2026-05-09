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

$type    = $_GET['type']    ?? 'book';
$genre   = $_GET['genre']   ?? '';
$sort    = $_GET['sort']    ?? 'trending';
$lang    = $_GET['lang']    ?? '';

if (!in_array($type, ['book', 'manga', 'audiobook'], true)) $type = 'book';

$typeLabel = ['book' => 'Books', 'manga' => 'Manga', 'audiobook' => 'Audiobooks'][$type];

$pageTitle       = "Browse $typeLabel";
$pageDescription = "Browse all $typeLabel on AnyLibrary. Filter by genre, language, and more.";
$extraCss        = ['/assets/css/browse.css'];
$activePage      = 'browse';

require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main id="browse-page"
      data-type="<?= htmlspecialchars($type) ?>"
      data-genre="<?= htmlspecialchars($genre) ?>"
      data-sort="<?= htmlspecialchars($sort) ?>"
      data-lang="<?= htmlspecialchars($lang) ?>">
    <div class="browse-header container">
        <h1 class="browse-title">Browse <?= htmlspecialchars($typeLabel) ?></h1>
        <div class="browse-filters" id="browse-filters">
            <div class="filter-group">
                <label class="filter-label" for="type-filter">Type</label>
                <select class="select-field" id="type-filter">
                    <option value="book"      <?= $type==='book'      ?'selected':'' ?>>Books</option>
                    <option value="manga"     <?= $type==='manga'     ?'selected':'' ?>>Manga</option>
                    <option value="audiobook" <?= $type==='audiobook' ?'selected':'' ?>>Audiobooks</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="genre-filter">Genre / Subject</label>
                <select class="select-field" id="genre-filter">
                    <option value="">All genres</option>
                    <option value="science_fiction"     <?= $genre==='science_fiction'     ?'selected':'' ?>>Science Fiction</option>
                    <option value="fantasy"             <?= $genre==='fantasy'             ?'selected':'' ?>>Fantasy</option>
                    <option value="mystery"             <?= $genre==='mystery'             ?'selected':'' ?>>Mystery</option>
                    <option value="romance"             <?= $genre==='romance'             ?'selected':'' ?>>Romance</option>
                    <option value="horror"              <?= $genre==='horror'              ?'selected':'' ?>>Horror</option>
                    <option value="historical_fiction"  <?= $genre==='historical_fiction'  ?'selected':'' ?>>Historical Fiction</option>
                    <option value="biography"           <?= $genre==='biography'           ?'selected':'' ?>>Biography</option>
                    <option value="self_help"           <?= $genre==='self_help'           ?'selected':'' ?>>Self-Help</option>
                    <option value="classics"            <?= $genre==='classics'            ?'selected':'' ?>>Classics</option>
                    <option value="philosophy"          <?= $genre==='philosophy'          ?'selected':'' ?>>Philosophy</option>
                    <option value="science"             <?= $genre==='science'             ?'selected':'' ?>>Science</option>
                    <option value="history"             <?= $genre==='history'             ?'selected':'' ?>>History</option>
                    <option value="poetry"              <?= $genre==='poetry'              ?'selected':'' ?>>Poetry</option>
                    <option value="children"            <?= $genre==='children'            ?'selected':'' ?>>Children</option>
                    <option value="graphic_novels"      <?= $genre==='graphic_novels'      ?'selected':'' ?>>Graphic Novels</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="sort-filter">Sort</label>
                <select class="select-field" id="sort-filter">
                    <option value="trending"    <?= $sort==='trending'    ?'selected':'' ?>>Trending</option>
                    <option value="rating"      <?= $sort==='rating'      ?'selected':'' ?>>Highest Rated</option>
                    <option value="newest"      <?= $sort==='newest'      ?'selected':'' ?>>Newest</option>
                    <option value="title"       <?= $sort==='title'       ?'selected':'' ?>>A–Z</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="lang-filter">Language</label>
                <select class="select-field" id="lang-filter">
                    <option value="">Any</option>
                    <option value="en" <?= $lang==='en' ?'selected':'' ?>>English</option>
                    <option value="fr" <?= $lang==='fr' ?'selected':'' ?>>Français</option>
                    <option value="es" <?= $lang==='es' ?'selected':'' ?>>Español</option>
                    <option value="de" <?= $lang==='de' ?'selected':'' ?>>Deutsch</option>
                    <option value="ja" <?= $lang==='ja' ?'selected':'' ?>>日本語</option>
                    <option value="zh" <?= $lang==='zh' ?'selected':'' ?>>中文</option>
                    <option value="pt" <?= $lang==='pt' ?'selected':'' ?>>Português</option>
                    <option value="it" <?= $lang==='it' ?'selected':'' ?>>Italiano</option>
                    <option value="ru" <?= $lang==='ru' ?'selected':'' ?>>Русский</option>
                    <option value="ar" <?= $lang==='ar' ?'selected':'' ?>>العربية</option>
                </select>
            </div>
        </div>
    </div>

    <div class="container" style="padding-bottom:5rem;">
        <div class="browse-grid" id="browse-grid"></div>
        <div style="text-align:center;padding:2.5rem 0;display:none;" id="browse-load-more-wrap">
            <button class="btn btn-secondary" id="browse-load-more">Load more</button>
        </div>
        <div class="empty-state" id="browse-empty" style="display:none;">
            <div class="empty-state-icon">&#128214;</div>
            <h3>Nothing found</h3>
            <p>Try adjusting your filters or <a href="/browse" style="color:var(--c-accent)">clearing all</a>.</p>
        </div>
    </div>
</main>

<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/browse.js"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/app.js"></script>
</body>
</html>
