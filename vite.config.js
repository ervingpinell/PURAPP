import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: '127.0.0.1',
        port: 5173,
        strictPort: true,
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/home.css',
                'resources/css/gv.css',
                'resources/css/tour.css',
                'resources/css/review.css',
                'resources/css/homereview.css',
                'resources/js/app.js',
                'resources/js/public.js',

                // ✅ Scripts Viator
                'resources/js/viator/all-reviews.js',
                'resources/js/viator/product-reviews.js',
                'resources/js/viator/carousel-reviews.js',
                'resources/js/viator/render-reviews.js',
                'resources/js/viator/review-carousel-grid.js',
            ],
            refresh: true,
        }),
    ],
});
