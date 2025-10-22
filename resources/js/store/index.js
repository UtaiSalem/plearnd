import { reactive, computed } from 'vue';
import axios from 'axios';

// State
const state = reactive({
    user: null,
    token: localStorage.getItem('token') || null,
    activities: [],
    currentPage: 0,
    lastPage: 1,
    isLoadingActivities: false,
    isLoadingNewContent: false,
    filters: {
        type: 'all', // all, posts, academy, courses, donations
        search: ''
    },
    notifications: [],
    showNotifications: false,
    peopleMayKnow: [],
    pendingFriends: [],
    donates: [],
    advertises: [],
});

// Getters
const isAuthenticated = computed(() => !!state.token);
const filteredActivities = computed(() => {
    let filtered = [...state.activities];
    
    // Filter by type
    if (state.filters.type !== 'all') {
        filtered = filtered.filter(activity => {
            switch (state.filters.type) {
                case 'posts':
                    return activity.action_to === 'Post';
                case 'academy':
                    return activity.action_to === 'AcademyPost';
                case 'courses':
                    return activity.action_to === 'CoursePost';
                case 'donations':
                    return activity.action_to === 'Donate' || activity.action_to === 'DonateRecipient';
                default:
                    return true;
            }
        });
    }
    
    // Filter by search query
    if (state.filters.search.trim()) {
        const query = state.filters.search.toLowerCase();
        filtered = filtered.filter(activity => {
            // Search in post content
            if (activity.target_resource?.content) {
                return activity.target_resource.content.toLowerCase().includes(query);
            }
            // Search in user name
            if (activity.action_by?.name) {
                return activity.action_by.name.toLowerCase().includes(query);
            }
            return false;
        });
    }
    
    return filtered;
});

// Actions
const actions = {
    // Authentication
    async login(credentials) {
        try {
            const response = await axios.post('/api/login', credentials);
            const { token, user } = response.data;
            
            state.token = token;
            state.user = user;
            
            localStorage.setItem('token', token);
            
            // Set default auth header
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            
            return { success: true, user };
        } catch (error) {
            console.error('Login error:', error);
            return { 
                success: false, 
                message: error.response?.data?.message || 'Login failed' 
            };
        }
    },
    
    async logout() {
        try {
            await axios.post('/api/logout');
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            state.token = null;
            state.user = null;
            
            localStorage.removeItem('token');
            delete axios.defaults.headers.common['Authorization'];
        }
    },
    
    async fetchUser() {
        if (!state.token) return;
        
        try {
            const response = await axios.get('/api/user');
            state.user = response.data;
        } catch (error) {
            console.error('Fetch user error:', error);
            // Token might be expired, logout
            actions.logout();
        }
    },
    
    // Activities
    async fetchActivities(page = 1, reset = false) {
        if (state.isLoadingActivities) return;
        
        state.isLoadingActivities = true;
        
        try {
            const response = await axios.get(`/api/newsfeed-v2/activities?page=${page}&per_page=5`);
            
            if (response.data.success) {
                const newActivities = response.data.activities || [];
                
                if (reset || page === 1) {
                    state.activities = newActivities;
                } else {
                    state.activities = [...state.activities, ...newActivities];
                }
                
                state.currentPage = response.data.pagination?.current_page || page;
                state.lastPage = response.data.pagination?.last_page || 1;
            }
        } catch (error) {
            console.error('Fetch activities error:', error);
        } finally {
            state.isLoadingActivities = false;
        }
    },
    
    async refreshActivities() {
        if (state.isLoadingNewContent) return;
        
        state.isLoadingNewContent = true;
        
        try {
            await actions.fetchActivities(1, true);
        } catch (error) {
            console.error('Refresh activities error:', error);
        } finally {
            state.isLoadingNewContent = false;
        }
    },
    
    async addNewActivity(activity) {
        state.activities.unshift(activity);
    },
    
    async deleteActivity(index) {
        state.activities.splice(index, 1);
    },
    
    // Filters
    setFilterType(type) {
        state.filters.type = type;
    },
    
    setSearchQuery(query) {
        state.filters.search = query;
    },
    
    // Notifications
    async fetchNotifications() {
        try {
            const response = await axios.get('/api/notifications');
            state.notifications = response.data || [];
        } catch (error) {
            console.error('Fetch notifications error:', error);
        }
    },
    
    toggleNotifications() {
        state.showNotifications = !state.showNotifications;
    },
    
    // Static data
    async fetchStaticData() {
        try {
            const response = await axios.get('/api/newsfeed-v2/static-data');
            
            state.peopleMayKnow = response.data.peopleMayKnow || [];
            state.pendingFriends = response.data.pendingFriends || [];
            state.donates = response.data.donates || [];
            state.advertises = response.data.advertises || [];
            state.notifications = response.data.notifications || [];
        } catch (error) {
            console.error('Fetch static data error:', error);
        }
    },
};

// Initialize auth if token exists
if (state.token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${state.token}`;
    actions.fetchUser();
}

export default {
    state,
    getters: {
        isAuthenticated,
        filteredActivities,
    },
    actions,
};