import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: '127.0.0.1', // Use '127.0.0.1' or 0.0.0.0 for external access'
        port: 5173,
        strictPort: true,

        /* descomentar si se desea acceder desde otra máquina en la red local
        cors: true,
        hmr: {
         host: '192.168.100.131', // Cambiar por la IP de tu máquina si es necesario
         },
         */
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
                'resources/js/cart/promo-code.js',

                // ✅ Scripts Viator
                'resources/js/viator/tour-reviews.js',
                'resources/js/viator/carousel-reviews.js',
                'resources/js/viator/review-carousel-grid.js',
            ],
            refresh: true,
        }),
    ],
});
