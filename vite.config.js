import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/mis-overrides.css',
                'resources/js/app.js',
                'resources/css/marketing-site.css',
                'resources/js/marketing-home.js',
                'resources/js/marketing-book.js',
            ],
            refresh: true,
        }),
    ],
});
