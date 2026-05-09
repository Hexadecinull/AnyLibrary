<?php
/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

$pageTitle  = 'Settings';
$activePage = 'settings';
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main class="container" style="padding-top:2rem;padding-bottom:5rem;max-width:680px;">
    <h1 style="font-weight:800;letter-spacing:-0.02em;margin-bottom:0.4rem;">Settings</h1>
    <p style="color:var(--c-text-3);font-size:0.875rem;margin-bottom:2.5rem;">
        All settings are stored locally in your browser and apply only to this device.
    </p>
    <p style="color:var(--c-text-2);font-size:0.875rem;padding:1rem 1.25rem;background:var(--c-bg-3);border:1px solid var(--c-border);border-radius:var(--radius-m);">
        Use the <strong>Settings</strong> button (&#9881;&#65039;) in the top navigation to access all preferences — appearance, reading options, library management, and about.
    </p>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/app.js"></script>
</body>
</html>
