/*
 * StreamSuite — Free, open-source streaming website
 * Copyright (C) 2026  StreamSuite Contributors
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

export default [
    {
        files: ['assets/js/**/*.js'],
        languageOptions: {
            ecmaVersion: 2022,
            sourceType: 'script',
            globals: {
                window: 'readonly',
                document: 'readonly',
                console: 'readonly',
                localStorage: 'readonly',
                sessionStorage: 'readonly',
                fetch: 'readonly',
                crypto: 'readonly',
                URL: 'readonly',
                URLSearchParams: 'readonly',
                setTimeout: 'readonly',
                clearTimeout: 'readonly',
                setInterval: 'readonly',
                clearInterval: 'readonly',
                requestAnimationFrame: 'readonly',
                parseInt: 'readonly',
                parseFloat: 'readonly',
                encodeURIComponent: 'readonly',
                Promise: 'readonly',
                JSON: 'readonly',
                Math: 'readonly',
                Date: 'readonly',
                Array: 'readonly',
                Object: 'readonly',
                Event: 'readonly',
                CustomEvent: 'readonly',
                MutationObserver: 'readonly',
                IntersectionObserver: 'readonly',
                Api: 'readonly',
                App: 'readonly',
                UI: 'readonly',
                History: 'readonly',
                Favorites: 'readonly',
                Player: 'readonly',
                SourceManager: 'readonly',
                ProgressTracker: 'readonly',
            },
        },
        rules: {
            'no-undef': 'warn',
            'no-unused-vars': 'warn',
            'no-console': 'off',
            'eqeqeq': 'error',
            'semi': ['error', 'always'],
            'no-var': 'error',
        },
    },
];
