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

$period = in_array($_GET['period'] ?? 'daily', ['daily', 'weekly', 'monthly', 'yearly'], true)
    ? $_GET['period']
    : 'daily';

$periodLabel = ['daily' => 'Today', 'weekly' => 'This Week', 'monthly' => 'This Month', 'yearly' => 'This Year'][$period];

$pageTitle       = "Popular $periodLabel";
$pageDescription = "The most-read books on Open Library right now — $periodLabel trending.";
$extraCss        = ['/assets/css/browse.css'];
$activePage      = 'trending';

require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main id="trending-page" data-period="<?= htmlspecialchars($period) ?>">
    <div class="browse-header container">
        <h1 class="browse-title">&#128200;&nbsp; Popular <?= htmlspecialchars($periodLabel) ?></h1>
        <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
            <?php foreach (['daily' => 'Today', 'weekly' => 'This Week', 'monthly' => 'This Month', 'yearly' => 'This Year'] as $p => $label): ?>
                <a href="/trending?period=<?= $p ?>" class="chip <?= $period===$p?'active':'' ?>"><?= htmlspecialchars($label) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="container" style="padding-bottom:5rem;">
        <div class="browse-grid" id="trending-grid"></div>
    </div>
</main>

<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/trending.js"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/app.js"></script>
</body>
</html>
