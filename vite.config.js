import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: '0.0.0.0', // Use '127.0.0.1' or 0.0.0.0 for external access'
        port: 5173,
        strictPort: true,
          cors: true, //eliminar el CORS para producción
         hmr: { //Eliminar el HMR para producción
            host: '192.168.100.131', // ✅ Borrar para producción
        },//Eliminar el HMR para producción
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
