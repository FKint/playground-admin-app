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
                // pdfmake and its fonts are ~1.7 MB and only needed when exporting; keep them out of the main bundle.
                codeSplitting: {
                    groups: [
                        {name: 'pdf', test: /node_modules[\\/](pdfmake|jszip)[\\/]/, priority: 20},
                    ],
                },
            },
        },
    },
});
