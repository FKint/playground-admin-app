import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// The Blade views use inline scripts that rely on the globals ($, moment, ...) being defined
// synchronously, so the JS entry is built as a classic (non-module) script and included with a
// plain <script src> tag (see layouts/app.blade.php), not with @vite.
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/assets/js/app.js'],
        }),
    ],
    build: {
        cssCodeSplit: false, // one stylesheet, emitted as `style.css` in the manifest
        rollupOptions: {
            output: { format: 'iife' },
        },
    },
});
