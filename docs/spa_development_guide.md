# คู่มือการพัฒนาตามหลักการ SPA (Single Page Application)

## ภาพรวม

Single Page Application (SPA) คือเว็บแอปพลิเคชันที่ทำงานบนหน้าเว็บเดียว โดยไม่ต้องรีเฟรชหน้าเว็บเมื่อเปลี่ยนหน้า ทำให้ประสบการณ์การใช้งานเหมือนแอปพลิเคชันบนมือถือ

## โครงสร้างไฟล์สำหรับ SPA

```
resources/js/
├── app_spa.js              # ไฟล์หลักสำหรับ SPA
├── store/
│   └── index.js            # State management ด้วย Vue 3 Composition API
├── router/
│   └── index.js            # Vue Router สำหรับการจัดการ routing
├── Layouts/
│   └── SPALayout.vue       # Layout หลักสำหรับ SPA
├── Pages/
│   ├── NewsfeedV2.vue      # หน้าหลักของ Newsfeed
│   ├── NotFound.vue        # หน้า 404
│   └── ...                 # หน้าอื่นๆ
└── Components/
    ├── newsfeed/           # Components สำหรับ Newsfeed
    └── ...                 # Components อื่นๆ
```

## ฟีเจอร์หลักของ SPA

### 1. State Management

ใช้ Vue 3 Composition API กับ reactive state ในการจัดการข้อมูลทั่วทั้งแอปพลิเคชัน:

```javascript
// store/index.js
const state = reactive({
    user: null,
    token: localStorage.getItem('token') || null,
    activities: [],
    // ... ข้อมูลอื่นๆ
});

const actions = {
    async login(credentials) {
        // ... การล็อกอิน
    },
    
    async fetchActivities(page = 1) {
        // ... การดึงข้อมูล activities
    },
    // ... actions อื่นๆ
};
```

### 2. Routing

ใช้ Vue Router สำหรับการจัดการการนำทางภายในแอปพลิเคชัน:

```javascript
// router/index.js
const routes = [
    {
        path: '/newsfeed-v2',
        name: 'newsfeed-v2',
        component: NewsfeedV2,
        beforeEnter: requireAuth,
        meta: {
            title: 'Newsfeed V2'
        }
    },
    // ... routes อื่นๆ
];
```

### 3. Navigation

สร้าง navigation bar และ sidebar สำหรับการนำทางภายในแอปพลิเคชัน:

```html
<!-- SPALayout.vue -->
<header class="bg-white shadow-sm border-b border-gray-200 fixed top-0 left-0 right-0 z-40">
    <!-- ... -->
</header>

<main class="pt-16">
    <router-view />
</main>
```

### 4. API Integration

ใช้ axios สำหรับการเรียก API และจัดการข้อมูล:

```javascript
// store/index.js
const actions = {
    async fetchActivities(page = 1) {
        try {
            const response = await axios.get(`/api/newsfeed-v2/activities?page=${page}&per_page=5`);
            
            if (response.data.success) {
                // ... จัดการข้อมูล
            }
        } catch (error) {
            console.error('Fetch activities error:', error);
        }
    }
};
```

## การเริ่มต้นใช้งาน

### 1. การติดตั้ง

1. ติดตั้ง Vue Router:
   ```bash
   npm install vue-router@4
   ```

2. ติดตั้ง Pinia (ถ้าต้องการ):
   ```bash
   npm install pinia
   ```

### 2. การตั้งค่า

1. แก้ไข `vite.config.js` หรือ `webpack.config.js` สำหรับการใช้งาน SPA:
   ```javascript
   export default defineConfig({
       // ...
       build: {
           rollupOptions: {
               input: 'resources/js/app_spa.js'
           }
       }
   });
   ```

2. อัปเดต `package.json`:
   ```json
   {
       "scripts": {
           "dev": "vite",
           "build": "vite build",
           "build-spa": "vite build --mode spa"
       }
   }
   ```

### 3. การใช้งาน

1. สร้างไฟล์ `resources/views/app.blade.php`:
   ```html
   <!DOCTYPE html>
   <html lang="th">
   <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>{{ $pageTitle ?? 'Plearnd' }}</title>
       @vite(['resources/css/app.css', 'resources/js/app_spa.js'])
   </head>
   <body>
       <div id="app"></div>
   </body>
   </html>
   ```

2. สร้าง Route ใน Laravel:
   ```php
   // routes/web.php
   Route::get('/{path?}', function() {
       return view('app');
   })->where('path', '^(?!api|storage|_debugbar).*');
   ```

## การจัดการการยืนยันต์

### 1. การล็อกอิน

```javascript
// store/index.js
const actions = {
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
            return { success: false, message: error.response?.data?.message || 'Login failed' };
        }
    }
};
```

### 2. Route Guards

```javascript
// router/index.js
const requireAuth = (to, from, next) => {
    if (store.getters.isAuthenticated) {
        next();
    } else {
        next('/login');
    }
};

const routes = [
    {
        path: '/newsfeed-v2',
        name: 'newsfeed-v2',
        component: NewsfeedV2,
        beforeEnter: requireAuth,
    },
    // ...
];
```

## การจัดการข้อมูล

### 1. การดึงข้อมูล

```javascript
// store/index.js
const actions = {
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
    }
};
```

### 2. การกรองข้อมูล

```javascript
// store/index.js
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
                // ...
                default:
                    return true;
            }
        });
    }
    
    // Filter by search query
    if (state.filters.search.trim()) {
        const query = state.filters.search.toLowerCase();
        filtered = filtered.filter(activity => {
            // ... การค้นหา
        });
    }
    
    return filtered;
});
```

## การจัดการ UI

### 1. Loading States

```html
<!-- Loading Indicator -->
<div v-if="state.isLoadingActivities" class="flex justify-center">
    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
</div>
```

### 2. Error Handling

```javascript
// store/index.js
const actions = {
    async fetchActivities(page = 1) {
        try {
            // ... การดึงข้อมูล
        } catch (error) {
            console.error('Fetch activities error:', error);
            
            // แสดงข้อความผิดพลาด
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: 'ไม่สามารถโหลดข้อมูลได้ กรุณาลองใหม่อีกครั้ง',
            });
        }
    }
};
```

### 3. Responsive Design

```html
<!-- Mobile menu button -->
<div class="md:hidden">
    <button @click="toggleMobileMenu" class="p-2 rounded-md text-gray-600">
        <!-- ... -->
    </button>
</div>

<!-- Desktop navigation -->
<nav class="hidden md:flex space-x-8">
    <!-- ... -->
</nav>
```

## การปรับปรุงประสิทธิภาพ

### 1. Lazy Loading

```javascript
// router/index.js
const routes = [
    {
        path: '/newsfeed-v2',
        name: 'newsfeed-v2',
        component: () => import('../Pages/NewsfeedV2.vue'),
    },
    // ...
];
```

### 2. Caching

```javascript
// store/index.js
const actions = {
    async fetchActivities(page = 1) {
        const cacheKey = `activities_page_${page}`;
        
        // ตรวจสอบว่ามีข้อมูลใน cache หรือไม่
        const cachedData = localStorage.getItem(cacheKey);
        if (cachedData) {
            state.activities = JSON.parse(cachedData);
        }
        
        // ดึงข้อมูลใหม่
        // ...
        
        // เก็บข้อมูลไว้ใน cache
        localStorage.setItem(cacheKey, JSON.stringify(response.data.activities));
    }
};
```

## การทดสอบ

### 1. Unit Testing

```javascript
// tests/store.test.js
import { describe, it, expect } from 'vitest';
import store from '../store/index.js';

describe('Store', () => {
    it('should initialize with empty activities', () => {
        expect(store.state.activities).toEqual([]);
    });
    
    it('should fetch activities', async () => {
        await store.actions.fetchActivities();
        expect(store.state.activities.length).toBeGreaterThan(0);
    });
});
```

### 2. E2E Testing

```javascript
// tests/e2e/newsfeed.test.js
import { test, expect } from '@playwright/test';

test('should display newsfeed', async ({ page }) => {
    await page.goto('/newsfeed-v2');
    
    // ตรวจสอบว่าแสดง activities
    await expect(page.locator('.activity-card')).toHaveCount(5);
    
    // ตรวจสอบการทำงานของ infinite scroll
    await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
    await expect(page.locator('.activity-card')).toHaveCount(10);
});
```

## การ Deploy

### 1. Build

```bash
npm run build-spa
```

### 2. การตั้งค่า Server

```nginx
# nginx.conf
location / {
    try_files $uri $uri/ /index.html;
}
```

## ข้อดีและข้อเสียงของ SPA

### ข้อดี

1. **ประสบการณ์การใช้งานที่รวดเร็ว** - ไม่ต้องรีเฟรชหน้าเว็บ
2. **การใช้ทรัพยากรที่มีประสิทธิภาพ** - โหลดข้อมูลเฉพาะที่จำเป็น
3. **การพัฒนาที่ง่ายขึ้น** - แยกส่วน frontend และ backend ชัดเจน
4. **การทำงานแบบ offline** - สามารถใช้ Service Worker ได้

### ข้อเสียง

1. **SEO** - ต้องใช้ Server-side rendering หรือ Static site generation
2. **การโหลดครั้งแรก** - อาจใช้เวลานานขึ้นถ้ามีการโหลดข้อมูลมาก
3. **ความซับซ้อน** - ต้องมีการจัดการ state และ routing ที่ซับซ้อนขึ้น
4. **การใช้งานบนอุปกรณ์เก่า** - อาจทำงานได้ช้าลงบนบางอุปกรณ์

## ขั้นตอนถัดไป

1. **เพิ่มการทำงานแบบ offline** ด้วย Service Worker
2. **ปรับปรุง SEO** ด้วย Server-side rendering
3. **เพิ่มการทดสอบ** ให้ครอบคลุมมากขึ้น
4. **ปรับปรุงประสิทธิภาพ** ด้วยการใช้ Web Workers