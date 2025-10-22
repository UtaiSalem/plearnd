import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import store from './store';

// Import global styles
import '../css/app.css';

// Create app instance
const app = createApp({
    // Global data
    data() {
        return {
            isLoading: false,
            showBackToTop: false,
        };
    },
    
    // Global methods
    methods: {
        // Show loading indicator
        showLoading() {
            this.isLoading = true;
        },
        
        // Hide loading indicator
        hideLoading() {
            this.isLoading = false;
        },
        
        // Scroll to top
        scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        },
        
        // Handle scroll events
        handleScroll() {
            this.showBackToTop = window.pageYOffset > 300;
        },
    },
    
    // Lifecycle hooks
    mounted() {
        // Add scroll event listener
        window.addEventListener('scroll', this.handleScroll);
        
        // Initialize app
        this.initializeApp();
    },
    
    beforeUnmount() {
        // Remove scroll event listener
        window.removeEventListener('scroll', this.handleScroll);
    },
    
    methods: {
        // Initialize app
        async initializeApp() {
            this.showLoading();
            
            try {
                // Check if user is authenticated
                if (store.state.token) {
                    await store.actions.fetchUser();
                }
                
                // Fetch static data
                await store.actions.fetchStaticData();
                
                // Initialize other services
                this.initializeServices();
            } catch (error) {
                console.error('App initialization error:', error);
            } finally {
                this.hideLoading();
            }
        },
        
        // Initialize other services
        initializeServices() {
            // Initialize notifications
            if (store.state.user) {
                store.actions.fetchNotifications();
            }
            
            // Initialize other services as needed
        },
    },
});

// Use plugins
app.use(createPinia());
app.use(router);

// Global properties
app.config.globalProperties.$store = store;

// Global error handler
app.config.errorHandler = (err, vm, info) => {
    console.error('Global error:', err);
    console.error('Error info:', info);
    
    // You can send error to logging service here
};

// Global warning handler
app.config.warnHandler = (msg, vm, trace) => {
    console.warn('Global warning:', msg);
    console.warn('Warning trace:', trace);
};

// Custom directive for lazy loading images
app.directive('lazy', {
    mounted(el, binding) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    el.src = binding.value;
                    observer.unobserve(el);
                }
            });
        });
        
        observer.observe(el);
    }
});

// Custom directive for click outside
app.directive('click-outside', {
    mounted(el, binding) {
        el.clickOutsideEvent = function(event) {
            if (!(el === event.target || el.contains(event.target))) {
                binding.value(event);
            }
        };
        document.body.addEventListener('click', el.clickOutsideEvent);
    },
    unmounted(el) {
        document.body.removeEventListener('click', el.clickOutsideEvent);
    }
});

// Mount app
app.mount('#app');

// Export app instance for testing
export default app;