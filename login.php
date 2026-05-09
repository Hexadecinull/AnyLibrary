<?php
/*
 * AnyLibrary — Free, open-source streaming website
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

$pageTitle       = 'Login';
$pageDescription = 'Sign in to your AnyLibrary account.';
require_once __DIR__ . '/includes/head.php';
?>
<style>
.auth-page {
    min-height: calc(100vh - var(--header-h));
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}
.auth-card {
    background: var(--c-bg-2);
    border: 1px solid var(--c-border);
    border-radius: var(--radius-l);
    padding: 2.5rem;
    width: 100%;
    max-width: 400px;
    box-shadow: var(--shadow-l);
}
.auth-title {
    font-weight: 800;
    letter-spacing: -0.02em;
    margin-bottom: 0.5rem;
}
.auth-subtitle {
    color: var(--c-text-3);
    font-size: 0.85rem;
    margin-bottom: 2rem;
}
.form-group {
    margin-bottom: 1.25rem;
}
.form-label {
    display: block;
    font-size: 0.8rem;
    font-family: var(--font-mono);
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--c-text-2);
    margin-bottom: 0.4rem;
}
.form-input {
    width: 100%;
    background: var(--c-bg-3);
    border: 1px solid var(--c-border);
    border-radius: var(--radius-m);
    color: var(--c-text);
    padding: 0.65rem 0.9rem;
    font-size: 0.9rem;
    transition: border-color var(--transition);
}
.form-input:focus {
    outline: none;
    border-color: var(--c-accent);
}
.auth-error {
    color: var(--c-red);
    font-size: 0.82rem;
    margin-bottom: 1rem;
    padding: 0.6rem 0.9rem;
    background: rgba(224, 92, 92, 0.08);
    border: 1px solid rgba(224, 92, 92, 0.2);
    border-radius: var(--radius-m);
    display: none;
}
.auth-footer {
    text-align: center;
    margin-top: 1.5rem;
    font-size: 0.85rem;
    color: var(--c-text-3);
}
.auth-footer a {
    color: var(--c-accent);
}
.guest-note {
    margin-top: 1.5rem;
    padding: 1rem;
    background: var(--c-accent-dim);
    border-radius: var(--radius-m);
    font-size: 0.8rem;
    color: var(--c-text-2);
    text-align: center;
}
</style>

<header class="site-header">
    <div class="container header-inner">
        <a href="/" class="logo">
            <img src="/assets/img/logo-mark.svg" alt="" class="logo-mark">
            <span>AnyLibrary</span>
        </a>
        <button onclick="history.back()" class="btn btn-ghost">&#8592; Back</button>
    </div>
</header>

<div class="auth-page">
    <div class="auth-card">
        <h1 class="auth-title text-2xl">Welcome back</h1>
        <p class="auth-subtitle">Sign in to sync your data across devices.</p>

        <div id="auth-error" class="auth-error"></div>

        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input class="form-input" type="email" id="email" autocomplete="email" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input class="form-input" type="password" id="password" autocomplete="current-password" required>
        </div>

        <button id="login-btn" class="btn btn-primary w-full" style="width:100%;justify-content:center;">
            Sign In
        </button>

        <div class="guest-note">
            No account needed to watch — everything works as a guest. Login only required for cross-device sync.
        </div>

        <div class="auth-footer">
            No account? <a href="/register">Create one free</a>
        </div>
    </div>
</div>

<script src="/assets/js/api.js"></script>
<script src="/assets/js/ui.js"></script>
<script>
const loginBtn = document.getElementById('login-btn');
const errorEl  = document.getElementById('auth-error');

function showError(msg) {
    errorEl.textContent = msg;
    errorEl.style.display = 'block';
}

loginBtn.addEventListener('click', async () => {
    const email    = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    errorEl.style.display = 'none';

    if (!email || !password) { showError('Please fill in all fields.'); return; }

    loginBtn.textContent = 'Signing in...';
    loginBtn.disabled = true;

    try {
        const data = await Api.post('/auth.php?action=login', { email, password });
        localStorage.setItem('ss_auth_token', data.token);
        localStorage.setItem('ss_user', JSON.stringify(data.user));
        const redirect = new URLSearchParams(window.location.search).get('next') || '/';
        window.location.href = redirect;
    } catch (e) {
        showError(e.message || 'Invalid email or password.');
        loginBtn.textContent = 'Sign In';
        loginBtn.disabled = false;
    }
});

document.addEventListener('keydown', e => {
    if (e.key === 'Enter') loginBtn.click();
});
</script>
</body>
</html>
