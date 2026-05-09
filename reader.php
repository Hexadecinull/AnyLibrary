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
$src  = trim($_GET['src']  ?? '');

if (!$id && !$src) { header('Location: /'); exit; }

$extraCss   = ['/assets/css/reader.css'];
$pageTitle  = 'Reader';
$activePage = '';

require_once __DIR__ . '/includes/head.php';
?>
<body data-reader-theme="default">

<div id="reader-page"
     data-id="<?= htmlspecialchars($id) ?>"
     data-type="<?= htmlspecialchars($type) ?>"
     data-src="<?= htmlspecialchars($src) ?>">

    <!-- Top bar -->
    <div class="reader-topbar" id="reader-topbar">
        <button onclick="history.back()" class="btn btn-ghost btn-icon" aria-label="Back" title="Back to details">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <span class="reader-topbar-title" id="reader-topbar-title">Loading…</span>
        <div class="reader-topbar-actions">
            <button class="btn btn-ghost btn-icon" id="reader-toc-btn" aria-label="Table of contents" title="Table of contents">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="15" y2="12"/><line x1="3" y1="18" x2="10" y2="18"/></svg>
            </button>
            <button class="btn btn-ghost btn-icon" id="reader-bm-btn" aria-label="Bookmark this page" title="Bookmark">
                <svg width="13" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
            </button>
            <button class="btn btn-ghost btn-icon" id="reader-print-btn" aria-label="Print" title="Print / Save as PDF" onclick="window.print()">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            </button>
            <button class="btn btn-ghost btn-icon" id="reader-settings-btn" aria-label="Reader settings" title="Reader settings">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            </button>
        </div>
    </div>

    <!-- Reading progress indicator -->
    <div class="reader-progress-bar" role="progressbar" aria-label="Reading progress">
        <div class="reader-progress-fill" id="reader-progress-fill" style="width:0%"></div>
    </div>

    <!-- Reader settings panel -->
    <div class="reader-settings-panel" id="reader-settings-panel">
        <h3>Display</h3>
        <div style="margin-bottom:1rem;">
            <div style="font-size:0.78rem;color:var(--c-text-3);margin-bottom:0.5rem;">Font size</div>
            <div class="reader-font-sizes">
                <button class="reader-font-size-btn" data-size="0.875rem">S</button>
                <button class="reader-font-size-btn active" data-size="1rem">M</button>
                <button class="reader-font-size-btn" data-size="1.125rem">L</button>
                <button class="reader-font-size-btn" data-size="1.25rem">XL</button>
            </div>
        </div>
        <div style="margin-bottom:1rem;">
            <div style="font-size:0.78rem;color:var(--c-text-3);margin-bottom:0.5rem;">Theme</div>
            <div class="reader-theme-btns">
                <button class="reader-theme-btn active" data-theme="default">Dark</button>
                <button class="reader-theme-btn" data-theme="sepia">Sepia</button>
                <button class="reader-theme-btn" data-theme="night">Night</button>
            </div>
        </div>
        <div>
            <div style="font-size:0.78rem;color:var(--c-text-3);margin-bottom:0.5rem;">Font family</div>
            <select class="select-field" id="reader-font-select" style="width:100%;font-size:0.82rem;">
                <option value="var(--font-body)">Satoshi (Default)</option>
                <option value="Georgia, serif">Georgia (Serif)</option>
                <option value="'Times New Roman', serif">Times New Roman</option>
                <option value="system-ui, sans-serif">System UI</option>
                <option value="var(--font-mono)">Monospace</option>
            </select>
        </div>
    </div>

    <!-- Actual reading content rendered here by reader.js -->
    <div class="reader-body" id="reader-body">
        <div class="reader-content" id="reader-content">
            <div style="text-align:center;padding:4rem 0;color:var(--c-text-3);">
                <div class="skeleton" style="height:2rem;width:60%;margin:0 auto 1rem;border-radius:var(--radius-m);"></div>
                <div class="skeleton" style="height:1rem;width:90%;margin:0 auto 0.5rem;border-radius:var(--radius-s);"></div>
                <div class="skeleton" style="height:1rem;width:85%;margin:0 auto 0.5rem;border-radius:var(--radius-s);"></div>
                <div class="skeleton" style="height:1rem;width:70%;margin:0 auto;border-radius:var(--radius-s);"></div>
            </div>
        </div>
    </div>
</div>

<div class="toast-container" id="toast-container"></div>

<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script src="/assets/js/reader.js"></script>
</body>
</html>
