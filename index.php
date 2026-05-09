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

$pageTitle       = 'Discover';
$pageDescription = 'Read everything. Own nothing. Pay nothing. — Books, manga, and audiobooks, free and open source.';
$extraCss        = ['/assets/css/home.css'];
$activePage      = 'home';
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main id="home-page">
    <div id="featured-container" class="featured-section skeleton" style="min-height:68vh;"></div>

    <div class="content-rows">
        <div class="container">
            <section class="content-row" id="continue-row" hidden>
                <div class="row-header">
                    <div class="row-header-left">
                        <div class="row-icon">&#128214;</div>
                        <h2 class="text-xl">Continue Reading</h2>
                    </div>
                    <a href="/history" class="see-all">History &rarr;</a>
                </div>
                <div class="row-items" id="continue-items"></div>
            </section>
            <div id="rows-container"></div>
        </div>
    </div>
</main>

<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/home.js"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/app.js"></script>
</body>
</html>
