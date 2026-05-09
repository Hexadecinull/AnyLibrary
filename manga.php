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

$pageTitle       = 'Manga';
$pageDescription = 'Browse thousands of free manga titles on AnyLibrary. Read online, track your progress, and discover new series.';
$extraCss        = ['/assets/css/manga.css'];
$activePage      = 'manga';

require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main id="manga-page">
    <div class="manga-hero">
        <div class="manga-hero-inner">
            <h1>Manga</h1>
            <p>Thousands of series available to read free, via <a href="https://mangadex.org" target="_blank" rel="noopener noreferrer" style="color:var(--c-accent)">MangaDex</a>. Track your progress. No account required.</p>
            <div class="manga-filters" id="manga-tag-filters">
                <button class="chip active" data-tag="">All</button>
                <button class="chip" data-tag="4d32cc48-9f4b-4249-86fc-1f00a012f6f0">Action</button>
                <button class="chip" data-tag="cdad7e68-1419-41dd-bdce-27753074a640">Adventure</button>
                <button class="chip" data-tag="33771934-028e-4cb3-8744-691e866a923e">Comedy</button>
                <button class="chip" data-tag="b9af3a63-f058-46de-a9a0-e0c13906197a">Drama</button>
                <button class="chip" data-tag="cdc58593-87dd-415e-bbc0-2ec27bf404cc">Fantasy</button>
                <button class="chip" data-tag="e5301a23-ebd9-49dd-a0cb-2add944c7fe9">Slice of Life</button>
                <button class="chip" data-tag="256c8bd9-4904-4360-bf4f-508a76d67183">Sci-Fi</button>
                <button class="chip" data-tag="0234a31e-a729-4e28-9d6a-3f87c4966b9e">Horror</button>
                <button class="chip" data-tag="f8f62932-27da-4fe4-8ee1-6779a8c5edba">Thriller</button>
                <button class="chip" data-tag="87cc87cd-a395-47af-b27a-93258283bbc6">Romance</button>
            </div>
        </div>
    </div>

    <div class="container" style="padding-bottom:5rem;">
        <div id="manga-grid-container">
            <div class="manga-grid" id="manga-grid">
                <?php for ($i = 0; $i < 20; $i++): ?>
                    <div class="card">
                        <div class="card-poster skeleton" style="aspect-ratio:3/4;"></div>
                        <div class="card-body">
                            <div class="skeleton" style="height:0.75rem;width:80%;border-radius:4px;margin-bottom:0.35rem;"></div>
                            <div class="skeleton" style="height:0.65rem;width:50%;border-radius:4px;"></div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
            <div style="text-align:center;padding:2.5rem 0;" id="manga-load-more-wrap">
                <button class="btn btn-secondary" id="manga-load-more">Load more</button>
            </div>
        </div>
    </div>
</main>

<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/manga.js"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/app.js"></script>
</body>
</html>
