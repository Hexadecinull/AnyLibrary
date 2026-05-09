<?php
/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

$pageTitle       = 'Privacy Policy';
$pageDescription = 'AnyLibrary privacy policy — what we collect, what we do not collect, and your rights.';
$activePage      = '';
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main class="container" style="padding-top:2.5rem;padding-bottom:5rem;max-width:720px;">
    <h1 style="font-weight:800;letter-spacing:-0.02em;margin-bottom:0.5rem;">Privacy Policy</h1>
    <p style="color:var(--c-text-3);font-size:0.82rem;margin-bottom:2.5rem;font-family:var(--font-mono);">
        Last updated: <?= date('F j, Y') ?>
    </p>

    <div class="about-section">
        <h2 style="font-weight:700;margin-bottom:0.75rem;">1. What AnyLibrary does not do</h2>
        <p>AnyLibrary does not show advertisements. AnyLibrary does not sell, share, or monetise any data about you. AnyLibrary does not use third-party analytics services (no Google Analytics, no Mixpanel, no Hotjar). AnyLibrary does not use tracking cookies.</p>

        <h2 style="font-weight:700;margin:1.5rem 0 0.75rem;">2. What is stored in your browser</h2>
        <p>AnyLibrary stores the following data in your browser's <code>localStorage</code>:</p>
        <ul style="padding-left:1.5rem;margin-bottom:1rem;line-height:1.9;">
            <li><strong>al_guest</strong> — A random anonymous token used to associate your favorites and reading history without requiring an account.</li>
            <li><strong>al_token</strong> — A JWT authentication token if you create an account and sign in.</li>
            <li><strong>al_theme, al_font, al_reader_*</strong> — Your appearance and reading preferences.</li>
        </ul>
        <p>You can clear all of this data at any time via your browser's developer tools or by clearing site data.</p>

        <h2 style="font-weight:700;margin:1.5rem 0 0.75rem;">3. What is stored server-side</h2>
        <p>If you use AnyLibrary without an account, your favorites and reading history are stored in the database keyed to your anonymous <code>al_guest</code> token. No personally identifiable information is collected.</p>
        <p>If you create an account, we store: your display name, email address (hashed for password verification), and optionally an avatar image you upload. We do not collect your real name, phone number, or payment information.</p>

        <h2 style="font-weight:700;margin:1.5rem 0 0.75rem;">4. Imported book files</h2>
        <p>Files you import (EPUB, PDF, etc.) are stored on the server and associated with your guest token or account. They are only accessible to you via authenticated or guest-token-verified requests. They are never shared with other users.</p>

        <h2 style="font-weight:700;margin:1.5rem 0 0.75rem;">5. Third-party data sources</h2>
        <p>Book metadata is fetched from <a href="https://openlibrary.org/" target="_blank" rel="noopener noreferrer">Open Library</a> (Internet Archive). Manga data from <a href="https://mangadex.org" target="_blank" rel="noopener noreferrer">MangaDex</a>. Audiobooks from <a href="https://librivox.org" target="_blank" rel="noopener noreferrer">LibriVox</a>. These requests are made server-side; your IP address is <em>not</em> sent to these services when you browse AnyLibrary. Cover images, however, are loaded client-side from Open Library's CDN and MangaDex's image servers, which may log your IP as part of normal web server operation.</p>

        <h2 style="font-weight:700;margin:1.5rem 0 0.75rem;">6. Your rights (GDPR / CCPA)</h2>
        <p>You have the right to access, correct, export, and delete any data associated with your account or guest token. To exercise these rights, open a GitHub issue at <a href="https://github.com/Hexadecinull/AnyLibrary/issues" target="_blank" rel="noopener noreferrer">github.com/Hexadecinull/AnyLibrary</a> or contact the instance operator directly.</p>
        <p>If you are in France or the EU, you may contact the CNIL at <a href="https://www.cnil.fr" target="_blank" rel="noopener noreferrer">cnil.fr</a> to file a complaint.</p>

        <h2 style="font-weight:700;margin:1.5rem 0 0.75rem;">7. Self-hosted instances</h2>
        <p>AnyLibrary is open-source software. If you are using a self-hosted instance operated by a third party, their privacy practices may differ from this policy. Contact the operator of that instance for their specific privacy terms.</p>

        <h2 style="font-weight:700;margin:1.5rem 0 0.75rem;">8. Changes to this policy</h2>
        <p>This policy may be updated from time to time. Significant changes will be noted in the <a href="https://github.com/Hexadecinull/AnyLibrary/blob/main/CHANGELOG.md" target="_blank" rel="noopener noreferrer">CHANGELOG</a>.</p>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/app.js"></script>
</body>
</html>
