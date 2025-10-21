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
                'resources/css/faqs.css',
                'resources/css/tour-carousel.css',
                'resources/css/reviews-carousel.css',
                'resources/css/homereviews.css',
                'resources/css/reviews.css',
                'resources/css/reviews-embed.css',
                'resources/css/contact.css',
                'resources/css/policies.css',
                'resources/js/app.js',
                'resources/js/cart/promo-code.js',
                'resources/js/reviews-embed.js',
                'resources/js/reviews-carousel.js',
                'resources/js/reviews-index.js',
            ],
            refresh: true,
        }),
    ],
});
