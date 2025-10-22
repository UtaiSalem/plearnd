import { createRouter, createWebHistory } from 'vue-router';
import store from '../store';

// Import components
import Dashboard from '../Pages/Dashboard.vue';
import Newsfeed from '../Pages/Newsfeed.vue';
import NewsfeedV2 from '../Pages/NewsfeedV2.vue';
import NewsfeedBasic from '../Pages/NewsfeedBasic.vue';
import Post from '../Pages/Post.vue';
import Academy from '../Pages/Academy.vue';
import Support from '../Pages/Support.vue';
import UserProfile from '../Pages/UserProfile.vue';
import PrivacyPolicy from '../Pages/PrivacyPolicy.vue';
import TermsOfService from '../Pages/TermsOfService.vue';
import Welcome from '../Pages/Welcome.vue';
import Test from '../Pages/Test.vue';
import EditPost from '../Pages/EditPost.vue';
import MentalMath from '../Pages/MentalMath.vue';

// Auth routes
import Login from '../Pages/Auth/Login.vue';
import Register from '../Pages/Auth/Register.vue';
import ForgotPassword from '../Pages/Auth/ForgotPassword.vue';
import ResetPassword from '../Pages/Auth/ResetPassword.vue';

// Lazy loading for better performance
const Login = () => import('../Pages/Auth/Login.vue');
const Register = () => import('../Pages/Auth/Register.vue');
const ForgotPassword = () => import('../Pages/Auth/ForgotPassword.vue');
const ResetPassword = () => import('../Pages/Auth/ResetPassword.vue');

// Route guard for authenticated routes
const requireAuth = (to, from, next) => {
    if (store.getters.isAuthenticated) {
        next();
    } else {
        next('/login');
    }
};

// Route guard for guest routes (login, register, etc.)
const requireGuest = (to, from, next) => {
    if (!store.getters.isAuthenticated) {
        next();
    } else {
        next('/newsfeed-v2');
    }
};

const routes = [
    {
        path: '/',
        name: 'dashboard',
        component: Dashboard,
        beforeEnter: requireAuth,
        meta: {
            title: 'Dashboard'
        }
    },
    {
        path: '/newsfeed',
        name: 'newsfeed',
        component: Newsfeed,
        beforeEnter: requireAuth,
        meta: {
            title: 'Newsfeed'
        }
    },
    {
        path: '/newsfeed-v2',
        name: 'newsfeed-v2',
        component: NewsfeedV2,
        beforeEnter: requireAuth,
        meta: {
            title: 'Newsfeed V2'
        }
    },
    {
        path: '/newsfeed-basic',
        name: 'newsfeed-basic',
        component: NewsfeedBasic,
        beforeEnter: requireAuth,
        meta: {
            title: 'Newsfeed Basic'
        }
    },
    {
        path: '/post/:id',
        name: 'post',
        component: Post,
        beforeEnter: requireAuth,
        meta: {
            title: 'Post'
        }
    },
    {
        path: '/post/:id/edit',
        name: 'edit-post',
        component: EditPost,
        beforeEnter: requireAuth,
        meta: {
            title: 'Edit Post'
        }
    },
    {
        path: '/academy',
        name: 'academy',
        component: Academy,
        beforeEnter: requireAuth,
        meta: {
            title: 'Academy'
        }
    },
    {
        path: '/supports',
        name: 'supports',
        component: Support,
        beforeEnter: requireAuth,
        meta: {
            title: 'Supports'
        }
    },
    {
        path: '/profile/:id',
        name: 'user-profile',
        component: UserProfile,
        beforeEnter: requireAuth,
        meta: {
            title: 'User Profile'
        }
    },
    {
        path: '/mental-math',
        name: 'mental-math',
        component: MentalMath,
        beforeEnter: requireAuth,
        meta: {
            title: 'Mental Math'
        }
    },
    {
        path: '/privacy-policy',
        name: 'privacy-policy',
        component: PrivacyPolicy,
        meta: {
            title: 'Privacy Policy'
        }
    },
    {
        path: '/terms-of-service',
        name: 'terms-of-service',
        component: TermsOfService,
        meta: {
            title: 'Terms of Service'
        }
    },
    {
        path: '/welcome',
        name: 'welcome',
        component: Welcome,
        beforeEnter: requireAuth,
        meta: {
            title: 'Welcome'
        }
    },
    {
        path: '/test',
        name: 'test',
        component: Test,
        beforeEnter: requireAuth,
        meta: {
            title: 'Test'
        }
    },
    // Auth routes
    {
        path: '/login',
        name: 'login',
        component: Login,
        beforeEnter: requireGuest,
        meta: {
            title: 'Login'
        }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        beforeEnter: requireGuest,
        meta: {
            title: 'Register'
        }
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: ForgotPassword,
        beforeEnter: requireGuest,
        meta: {
            title: 'Forgot Password'
        }
    },
    {
        path: '/reset-password',
        name: 'reset-password',
        component: ResetPassword,
        beforeEnter: requireGuest,
        meta: {
            title: 'Reset Password'
        }
    },
    // Catch all route
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('../Pages/NotFound.vue'),
        meta: {
            title: 'Page Not Found'
        }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { top: 0 };
        }
    }
});

// Global navigation guards
router.beforeEach(async (to, from, next) => {
    // Update page title
    document.title = to.meta.title ? `${to.meta.title} - Plearnd` : 'Plearnd';
    
    // Show loading indicator
    const app = document.getElementById('app');
    if (app) {
        app.classList.add('loading');
    }
    
    // Check if user is authenticated
    if (store.state.token && !store.state.user) {
        await store.actions.fetchUser();
    }
    
    next();
});

router.afterEach(() => {
    // Hide loading indicator
    const app = document.getElementById('app');
    if (app) {
        app.classList.remove('loading');
    }
});

export default router;