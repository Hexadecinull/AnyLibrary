<?php
/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

http_response_code(404);
$pageTitle       = 'Page Not Found';
$pageDescription = 'The page you requested could not be found on AnyLibrary.';
$activePage      = '';
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main style="display:flex;align-items:center;justify-content:center;min-height:calc(100vh - var(--header-h) - 160px);padding:4rem 1.5rem;text-align:center;">
    <div>
        <div style="font-size:5rem;margin-bottom:1rem;opacity:0.4;">&#128218;</div>
        <h1 style="font-size:3rem;font-weight:800;letter-spacing:-0.03em;margin-bottom:0.5rem;">404</h1>
        <p style="color:var(--c-text-2);font-size:1rem;margin-bottom:2rem;max-width:320px;margin-left:auto;margin-right:auto;">
            That page doesn't exist in our library. Maybe it was shelved somewhere else.
        </p>
        <div style="display:flex;gap:0.75rem;justify-content:center;flex-wrap:wrap;">
            <a href="/" class="btn btn-primary">Go Home</a>
            <a href="/browse" class="btn btn-secondary">Browse Books</a>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/app.js"></script>
</body>
</html>
