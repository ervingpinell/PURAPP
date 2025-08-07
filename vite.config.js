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
                'resources/css/tour-carousel.css',
                'resources/css/tour-review.css',
                'resources/css/reviews.css',
                'resources/css/homereview.css',
                'resources/js/app.js',
                'resources/js/public.js',
                'resources/js/tour-carousel.js',

                // ✅ Scripts Viator
                'resources/js/viator/tour-reviews.js',
                'resources/js/viator/carousel-reviews.js',
                'resources/js/viator/review-carousel-grid.js',
            ],
            refresh: true,
        }),
    ],
});
