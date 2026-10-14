import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/principal.css', 'resources/js/principal.js'],
            refresh: true,
            fonts: [
                // Tipografía base — texto corrido
                bunny('Inter', {
                    weights: [400, 500, 600, 700],
                    styles: ['normal'],
                }),
                // Tipografía de display — títulos y headings
                bunny('Plus Jakarta Sans', {
                    weights: [600, 700, 800],
                    styles: ['normal'],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
