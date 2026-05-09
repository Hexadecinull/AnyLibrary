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

$pageTitle       = 'Audiobooks';
$pageDescription = 'Listen to thousands of free public-domain audiobooks on AnyLibrary, powered by LibriVox.';
$extraCss        = ['/assets/css/manga.css'];
$activePage      = 'audiobooks';

require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main id="audiobooks-page">
    <div class="manga-hero" style="background:linear-gradient(135deg,rgba(92,224,138,0.07) 0%,var(--c-bg-3) 100%);">
        <div class="manga-hero-inner">
            <h1>Audiobooks</h1>
            <p>Free public-domain audiobooks read by volunteers worldwide, via <a href="https://librivox.org" target="_blank" rel="noopener noreferrer" style="color:var(--c-green)">LibriVox</a>. No account required. Works in your browser.</p>
            <div class="manga-filters" id="audio-genre-filters">
                <button class="chip active" data-genre="">All</button>
                <button class="chip" data-genre="Fiction">Fiction</button>
                <button class="chip" data-genre="Non-fiction">Non-fiction</button>
                <button class="chip" data-genre="Mystery &amp; Thriller">Mystery</button>
                <button class="chip" data-genre="Science Fiction">Sci-Fi</button>
                <button class="chip" data-genre="Fantasy">Fantasy</button>
                <button class="chip" data-genre="Horror">Horror</button>
                <button class="chip" data-genre="Biography &amp; Autobiography">Biography</button>
                <button class="chip" data-genre="History">History</button>
                <button class="chip" data-genre="Philosophy">Philosophy</button>
                <button class="chip" data-genre="Children">Children</button>
            </div>
        </div>
    </div>

    <div class="container" style="padding-bottom:5rem;">
        <div class="manga-grid" id="audiobooks-grid">
            <?php for ($i = 0; $i < 20; $i++): ?>
                <div class="card card-audio">
                    <div class="card-poster skeleton" style="aspect-ratio:1/1;"></div>
                    <div class="card-body">
                        <div class="skeleton" style="height:0.75rem;width:80%;border-radius:4px;margin-bottom:0.35rem;"></div>
                        <div class="skeleton" style="height:0.65rem;width:50%;border-radius:4px;"></div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        <div style="text-align:center;padding:2.5rem 0;" id="audio-load-more-wrap">
            <button class="btn btn-secondary" id="audio-load-more">Load more</button>
        </div>
    </div>
</main>

<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/audiobooks.js"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/app.js"></script>
</body>
</html>
