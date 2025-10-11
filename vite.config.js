import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
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
            '@': '/resources/js',
        }
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: (id) => {
                    // Vendor chunk for Vue core
                    if (id.includes('vue') && !id.includes('primevue')) {
                        return 'vue-core';
                    }
                    
                    // PrimeVue core
                    if (id.includes('primevue/config') || 
                        id.includes('primevue/api') ||
                        id.includes('primevue/basecomponent')) {
                        return 'primevue-core';
                    }
                    
                    // PrimeVue data components (heavy components)
                    if (id.includes('primevue/datatable') || 
                        id.includes('primevue/dataview') ||
                        id.includes('primevue/tree') ||
                        id.includes('primevue/treetable') ||
                        id.includes('primevue/virtualscroller')) {
                        return 'primevue-data';
                    }
                    
                    // Other PrimeVue components
                    if (id.includes('primevue')) {
                        return 'primevue';
                    }
                    
                    // Third-party UI libraries
                    if (id.includes('@headlessui/vue') || 
                        id.includes('@iconify/vue') || 
                        id.includes('flowbite') || 
                        id.includes('sweetalert2') ||
                        id.includes('vue-sweetalert2')) {
                        return 'ui-libs';
                    }
                    
                    // Utilities and file handling
                    if (id.includes('file-saver') || 
                        id.includes('xlsx') || 
                        id.includes('html2canvas') || 
                        id.includes('qrcode-vue3') || 
                        id.includes('canvas-confetti') ||
                        id.includes('velocity-animate')) {
                        return 'utilities';
                    }
                    
                    // Date pickers and form components
                    if (id.includes('@vuepic/vue-datepicker') || 
                        id.includes('vue-tailwind-datepicker')) {
                        return 'date-components';
                    }
                    
                    // Inertia and axios
                    if (id.includes('@inertiajs') || id.includes('axios')) {
                        return 'inertia';
                    }
                    
                    // Large page components (academy, courses, etc.)
                    if (id.includes('Pages/Learn/Academy') || 
                        id.includes('Pages/Academy')) {
                        return 'academy-pages';
                    }
                    
                    if (id.includes('Pages/Learn/Course') || 
                        id.includes('PlearndComponents/learn/courses')) {
                        return 'course-pages';
                    }
                    
                    if (id.includes('Pages/Support') || 
                        id.includes('Pages/Play')) {
                        return 'support-play-pages';
                    }
                    
                    // Node modules vendor chunk
                    if (id.includes('node_modules')) {
                        return 'vendor';
                    }
                }
            }
        },
        // Increase the chunk size warning limit to 2000kb since we have proper chunking
        chunkSizeWarningLimit: 2000
    }
});
