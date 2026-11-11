import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/assets/js/app.js'],
            refresh: ['resources/views/**'],
        }),
    ],
    build: {
        rolldownOptions: {
            output: {
                // Vendor code changes rarely, so keep it in separate, long-cached chunks.
                codeSplitting: {
                    groups: [
                        {name: 'pdf', test: /node_modules[\\/](pdfmake|jszip)[\\/]/, priority: 20},
                        // globals.js must stay with the vendor code: it sets window.jQuery before the jQuery plugins load.
                        {name: 'vendor', test: /node_modules|assets[\\/]js[\\/]globals/, priority: 10},
                    ],
                },
            },
        },
    },
});
