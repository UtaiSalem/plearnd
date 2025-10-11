import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
            detectTls: false, // Force HTTP for development
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js'
        }
    },
    define: {
        // Enable Vue template compilation features and runtime features
        __VUE_OPTIONS_API__: true,
        __VUE_PROD_DEVTOOLS__: false,
        __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: false
    },
    server: {
        // Fix CORS issues and hostname mismatches
        host: 'localhost', // Use localhost to match Laravel server
        port: 5173,
        cors: {
            origin: ['http://localhost:8000', 'http://127.0.0.1:8000'],
            credentials: true
        },
        strictPort: true,
        hmr: {
            host: 'localhost'
        }
    },
    optimizeDeps: {
        // Force pre-bundling of these core dependencies
        include: [
            'vue',
            '@inertiajs/vue3',
            'pinia'
        ]
    },
    build: {
        // Temporarily disable manual chunking to fix lexical declaration error
        // The complex chunking was causing cross-chunk dependency issues
        chunkSizeWarningLimit: 2500
    }
});
