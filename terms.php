<?php
/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

$pageTitle       = 'Terms of Service';
$pageDescription = 'AnyLibrary terms of service — your rights and responsibilities when using AnyLibrary.';
$activePage      = '';
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main class="container" style="padding-top:2.5rem;padding-bottom:5rem;max-width:720px;">
    <h1 style="font-weight:800;letter-spacing:-0.02em;margin-bottom:0.5rem;">Terms of Service</h1>
    <p style="color:var(--c-text-3);font-size:0.82rem;margin-bottom:2.5rem;font-family:var(--font-mono);">
        Last updated: <?= date('F j, Y') ?>
    </p>

    <div class="about-section">
        <h2 style="font-weight:700;margin-bottom:0.75rem;">1. What AnyLibrary is</h2>
        <p>AnyLibrary is a free, open-source book library application. It provides a user interface for browsing and reading books, manga, and audiobooks using publicly available, free APIs — specifically <a href="https://openlibrary.org/" target="_blank" rel="noopener noreferrer">Open Library</a>, <a href="https://mangadex.org" target="_blank" rel="noopener noreferrer">MangaDex</a>, and <a href="https://librivox.org" target="_blank" rel="noopener noreferrer">LibriVox</a>. AnyLibrary does not host any book files or copyrighted content directly.</p>

        <h2 style="font-weight:700;margin:1.5rem 0 0.75rem;">2. Acceptable use</h2>
        <p>You agree to use AnyLibrary only for lawful purposes. You must not:</p>
        <ul style="padding-left:1.5rem;margin-bottom:1rem;line-height:1.9;">
            <li>Upload copyrighted content via the import feature that you do not have the right to distribute</li>
            <li>Attempt to circumvent authentication, access other users' data, or exploit vulnerabilities</li>
            <li>Use AnyLibrary to scrape or automate requests in a way that harms the upstream APIs</li>
            <li>Impersonate other users or submit false information when creating an account</li>
        </ul>

        <h2 style="font-weight:700;margin:1.5rem 0 0.75rem;">3. Imported content</h2>
        <p>When you import a file (EPUB, PDF, etc.), you represent that you have the right to store and access that file. AnyLibrary does not verify ownership. You are solely responsible for the content you import. Imported files are stored privately and are not accessible to other users.</p>

        <h2 style="font-weight:700;margin:1.5rem 0 0.75rem;">4. Third-party content</h2>
        <p>Books, manga chapters, and audiobooks accessed through AnyLibrary are subject to the terms and licenses of their respective providers (Open Library / Internet Archive, MangaDex, LibriVox). AnyLibrary does not grant any rights to this content beyond what those services permit.</p>

        <h2 style="font-weight:700;margin:1.5rem 0 0.75rem;">5. Disclaimer of warranties</h2>
        <p>AnyLibrary is provided <strong>"as is"</strong> without warranty of any kind. We do not guarantee availability, accuracy of book metadata, or uninterrupted access to third-party APIs. The software is licensed under GPL-3.0 — see the <a href="https://www.gnu.org/licenses/gpl-3.0.html" target="_blank" rel="noopener noreferrer">full license text</a>.</p>

        <h2 style="font-weight:700;margin:1.5rem 0 0.75rem;">6. Open-source licence</h2>
        <p>AnyLibrary is released under the <a href="https://www.gnu.org/licenses/gpl-3.0.html" target="_blank" rel="noopener noreferrer">GNU General Public License v3.0</a>. The source code is available at <a href="https://github.com/Hexadecinull/AnyLibrary" target="_blank" rel="noopener noreferrer">github.com/Hexadecinull/AnyLibrary</a>. You are free to self-host, modify, and redistribute AnyLibrary under the same licence.</p>

        <h2 style="font-weight:700;margin:1.5rem 0 0.75rem;">7. Changes to these terms</h2>
        <p>These terms may be updated. Continued use of AnyLibrary after changes are published constitutes acceptance of the revised terms. Significant changes will be documented in the <a href="https://github.com/Hexadecinull/AnyLibrary/blob/main/CHANGELOG.md" target="_blank" rel="noopener noreferrer">CHANGELOG</a>.</p>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/app.js"></script>
</body>
</html>
