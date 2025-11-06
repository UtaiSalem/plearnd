# 📚 Pinia Store Architecture - โครงสร้างสำหรับอนาคต

> เอกสารนี้อธิบายโครงสร้าง Pinia Store ที่ออกแบบมาเพื่อรองรับการใช้งาน API และการขยายระบบในอนาคต

## 🎯 หลักการออกแบบ

### 1. **Separation of Concerns** (แยกหน้าที่ชัดเจน)

```
stores/
├── modules/           # Store แยกตามฟีเจอร์
│   ├── course/       # จัดการข้อมูลรายวิชา
│   ├── member/       # จัดการสมาชิก
│   ├── attendance/   # จัดการการเข้าเรียน
│   └── lesson/       # จัดการบทเรียน
├── composables/      # Logic ที่ใช้ซ้ำ
│   ├── useApi.js     # API utilities
│   ├── useCache.js   # Cache management
│   └── useLoading.js # Loading states
└── services/         # API services
    └── courseService.js
```

---

## 🏗️ โครงสร้าง Store แบบ Modular

### **ตัวอย่าง: Course Store (Refactored)**

```javascript
// stores/modules/course/courseStore.js
import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { courseService } from "@/services/courseService";
import { useCache } from "@/stores/composables/useCache";
import { useLoading } from "@/stores/composables/useLoading";

export const useCourseStore = defineStore("course", () => {
    // ============= STATE =============
    const courses = ref(new Map()); // ใช้ Map แทน Object เพื่อ performance
    const currentCourseId = ref(null);

    // ============= COMPOSABLES =============
    const { cache, setCache, getCache, isCacheValid, clearCache } =
        useCache("course");

    const { loadingStates, setLoading, isLoading, clearLoading } = useLoading();

    // ============= GETTERS =============
    const currentCourse = computed(() => {
        return currentCourseId.value
            ? courses.value.get(currentCourseId.value)
            : null;
    });

    const getCourseById = computed(() => (courseId) => {
        return courses.value.get(courseId);
    });

    const coursesArray = computed(() => {
        return Array.from(courses.value.values());
    });

    // ============= ACTIONS =============

    // Fetch single course
    const fetchCourse = async (courseId, options = {}) => {
        const { forceRefresh = false } = options;
        const cacheKey = `course_${courseId}`;

        // ตรวจสอบ cache
        if (!forceRefresh && isCacheValid(cacheKey)) {
            return getCache(cacheKey);
        }

        setLoading(cacheKey, true);

        try {
            const data = await courseService.getCourse(courseId);

            // บันทึกลง Map
            courses.value.set(courseId, data);

            // บันทึก cache
            setCache(cacheKey, data);

            return data;
        } catch (error) {
            console.error("Failed to fetch course:", error);
            throw error;
        } finally {
            setLoading(cacheKey, false);
        }
    };

    // Fetch multiple courses
    const fetchCourses = async (params = {}) => {
        const cacheKey = `courses_${JSON.stringify(params)}`;

        if (isCacheValid(cacheKey)) {
            return getCache(cacheKey);
        }

        setLoading("courses_list", true);

        try {
            const data = await courseService.getCourses(params);

            // บันทึกแต่ละ course ลง Map
            data.data.forEach((course) => {
                courses.value.set(course.id, course);
            });

            setCache(cacheKey, data);

            return data;
        } catch (error) {
            console.error("Failed to fetch courses:", error);
            throw error;
        } finally {
            setLoading("courses_list", false);
        }
    };

    // Update course
    const updateCourse = async (courseId, updates) => {
        const cacheKey = `update_course_${courseId}`;

        setLoading(cacheKey, true);

        try {
            const data = await courseService.updateCourse(courseId, updates);

            // อัพเดท local state
            const existingCourse = courses.value.get(courseId);
            if (existingCourse) {
                courses.value.set(courseId, { ...existingCourse, ...data });
            }

            // Clear cache ที่เกี่ยวข้อง
            clearCache(`course_${courseId}`);

            return data;
        } catch (error) {
            console.error("Failed to update course:", error);
            throw error;
        } finally {
            setLoading(cacheKey, false);
        }
    };

    // Optimistic update (สำหรับ UX ที่ดี)
    const optimisticUpdate = (courseId, updates) => {
        const existingCourse = courses.value.get(courseId);
        if (existingCourse) {
            courses.value.set(courseId, { ...existingCourse, ...updates });
        }
    };

    // Revert optimistic update
    const revertUpdate = (courseId, previousData) => {
        courses.value.set(courseId, previousData);
    };

    // Set current course
    const setCurrentCourse = (courseId) => {
        currentCourseId.value = courseId;
    };

    // Clear course
    const clearCourse = (courseId) => {
        courses.value.delete(courseId);
        clearCache(`course_${courseId}`);
    };

    // Clear all
    const clearAll = () => {
        courses.value.clear();
        currentCourseId.value = null;
        clearCache();
        clearLoading();
    };

    return {
        // State
        courses,
        currentCourseId,

        // Getters
        currentCourse,
        getCourseById,
        coursesArray,

        // Loading states
        isLoading,

        // Actions
        fetchCourse,
        fetchCourses,
        updateCourse,
        optimisticUpdate,
        revertUpdate,
        setCurrentCourse,
        clearCourse,
        clearAll,
    };
});
```

---

## 🔧 Composables (ใช้ซ้ำได้)

### **1. useCache.js**

```javascript
// stores/composables/useCache.js
import { ref, computed } from "vue";

export function useCache(namespace, options = {}) {
    const {
        ttl = 5 * 60 * 1000, // 5 minutes
        maxSize = 100,
    } = options;

    const cache = ref(new Map());
    const timestamps = ref(new Map());

    const setCache = (key, data) => {
        const fullKey = `${namespace}:${key}`;

        // ลบข้อมูลเก่าถ้าเกิน maxSize
        if (cache.value.size >= maxSize) {
            const oldestKey = Array.from(timestamps.value.entries()).sort(
                ([, a], [, b]) => a - b
            )[0][0];
            cache.value.delete(oldestKey);
            timestamps.value.delete(oldestKey);
        }

        cache.value.set(fullKey, data);
        timestamps.value.set(fullKey, Date.now());
    };

    const getCache = (key) => {
        const fullKey = `${namespace}:${key}`;
        return cache.value.get(fullKey);
    };

    const isCacheValid = (key) => {
        const fullKey = `${namespace}:${key}`;
        const timestamp = timestamps.value.get(fullKey);

        if (!timestamp) return false;
        return Date.now() - timestamp < ttl;
    };

    const clearCache = (key = null) => {
        if (key) {
            const fullKey = `${namespace}:${key}`;
            cache.value.delete(fullKey);
            timestamps.value.delete(fullKey);
        } else {
            // Clear all cache for this namespace
            Array.from(cache.value.keys())
                .filter((k) => k.startsWith(`${namespace}:`))
                .forEach((k) => {
                    cache.value.delete(k);
                    timestamps.value.delete(k);
                });
        }
    };

    const invalidateCache = () => {
        clearCache();
    };

    return {
        cache,
        setCache,
        getCache,
        isCacheValid,
        clearCache,
        invalidateCache,
    };
}
```

### **2. useLoading.js**

```javascript
// stores/composables/useLoading.js
import { ref, computed } from "vue";

export function useLoading() {
    const loadingStates = ref(new Map());

    const setLoading = (key, status) => {
        loadingStates.value.set(key, status);
    };

    const isLoading = computed(() => (key) => {
        return loadingStates.value.get(key) || false;
    });

    const isAnyLoading = computed(() => {
        return Array.from(loadingStates.value.values()).some((v) => v === true);
    });

    const clearLoading = (key = null) => {
        if (key) {
            loadingStates.value.delete(key);
        } else {
            loadingStates.value.clear();
        }
    };

    return {
        loadingStates,
        setLoading,
        isLoading,
        isAnyLoading,
        clearLoading,
    };
}
```

### **3. useApi.js**

```javascript
// stores/composables/useApi.js
import axios from "axios";
import { ref } from "vue";

export function useApi() {
    const error = ref(null);
    const isLoading = ref(false);

    const request = async (config, options = {}) => {
        const {
            showLoading = true,
            throwError = true,
            onSuccess = null,
            onError = null,
        } = options;

        if (showLoading) isLoading.value = true;
        error.value = null;

        try {
            const response = await axios(config);

            if (onSuccess) {
                onSuccess(response.data);
            }

            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || err.message;

            if (onError) {
                onError(err);
            }

            if (throwError) {
                throw err;
            }

            return null;
        } finally {
            if (showLoading) isLoading.value = false;
        }
    };

    const get = (url, params = {}, options = {}) => {
        return request({ method: "GET", url, params }, options);
    };

    const post = (url, data = {}, options = {}) => {
        return request({ method: "POST", url, data }, options);
    };

    const put = (url, data = {}, options = {}) => {
        return request({ method: "PUT", url, data }, options);
    };

    const patch = (url, data = {}, options = {}) => {
        return request({ method: "PATCH", url, data }, options);
    };

    const del = (url, options = {}) => {
        return request({ method: "DELETE", url }, options);
    };

    return {
        error,
        isLoading,
        request,
        get,
        post,
        put,
        patch,
        del,
    };
}
```

---

## 🌐 Service Layer

### **courseService.js**

```javascript
// services/courseService.js
import axios from "axios";

export const courseService = {
    // Get single course
    async getCourse(courseId) {
        const response = await axios.get(`/api/courses/${courseId}`);
        return response.data;
    },

    // Get courses list
    async getCourses(params = {}) {
        const response = await axios.get("/api/courses", { params });
        return response.data;
    },

    // Create course
    async createCourse(data) {
        const response = await axios.post("/api/courses", data);
        return response.data;
    },

    // Update course
    async updateCourse(courseId, data) {
        const response = await axios.patch(`/api/courses/${courseId}`, data);
        return response.data;
    },

    // Delete course
    async deleteCourse(courseId) {
        const response = await axios.delete(`/api/courses/${courseId}`);
        return response.data;
    },

    // Update cover
    async updateCourseCover(courseId, file) {
        const formData = new FormData();
        formData.append("cover", file);

        const response = await axios.post(
            `/api/courses/${courseId}/cover`,
            formData,
            { headers: { "Content-Type": "multipart/form-data" } }
        );
        return response.data;
    },

    // Update logo
    async updateCourseLogo(courseId, file) {
        const formData = new FormData();
        formData.append("logo", file);

        const response = await axios.post(
            `/api/courses/${courseId}/logo`,
            formData,
            { headers: { "Content-Type": "multipart/form-data" } }
        );
        return response.data;
    },

    // Member management
    async requestMembership(courseId, groupId = null) {
        const response = await axios.post(`/api/courses/${courseId}/members`, {
            group_id: groupId,
        });
        return response.data;
    },

    async cancelMembership(courseId, memberId) {
        const response = await axios.delete(
            `/api/courses/${courseId}/members/${memberId}`
        );
        return response.data;
    },
};
```

---

## 🎨 Component Usage (ตัวอย่างการใช้งาน)

### **CourseProfileCover.vue (Refactored)**

```vue
<script setup>
import { computed, onMounted } from "vue";
import { useCourseStore } from "@/stores/modules/course/courseStore";
import { useCourseProfileUIStore } from "@/stores/modules/course/courseProfileUIStore";

const props = defineProps({
    courseId: Number,
    // ... other props
});

// ใช้ Store แยกกัน
const courseStore = useCourseStore();
const uiStore = useCourseProfileUIStore();

// Computed
const course = computed(() => courseStore.getCourseById(props.courseId));
const isLoading = computed(() =>
    courseStore.isLoading(`course_${props.courseId}`)
);
const isUpdatingCover = computed(() =>
    courseStore.isLoading(`update_cover_${props.courseId}`)
);

// UI State
const showOptionGroups = computed(() => uiStore.showOptionGroups);
const inputHeaderEditing = computed(() => uiStore.inputHeaderEditing);

// Methods
async function updateCover(file) {
    try {
        // Optimistic update
        const tempUrl = URL.createObjectURL(file);
        courseStore.optimisticUpdate(props.courseId, { cover: tempUrl });

        // API call
        await courseStore.updateCourse(props.courseId, { cover: file });

        // Cleanup
        URL.revokeObjectURL(tempUrl);
    } catch (error) {
        // Revert on error
        courseStore.revertUpdate(props.courseId, course.value);
        console.error("Failed to update cover:", error);
    }
}

// Lifecycle
onMounted(async () => {
    await courseStore.fetchCourse(props.courseId);
});
</script>
```

---

## 📋 Best Practices

### ✅ **DO's**

1. **แยก Store ตาม Domain/Feature**

    - `useCourseStore` - ข้อมูลรายวิชา
    - `useMemberStore` - ข้อมูลสมาชิก
    - `useAttendanceStore` - ข้อมูลการเข้าเรียน

2. **ใช้ Composables สำหรับ Logic ที่ใช้ซ้ำ**

    - `useCache` - Cache management
    - `useLoading` - Loading states
    - `useApi` - API calls

3. **แยก UI State ออกจาก Data State**

    - `useCourseStore` - Data
    - `useCourseUIStore` - UI (modal, dropdown, editing)

4. **ใช้ Service Layer สำหรับ API**

    - แยก API logic ออกจาก Store
    - ง่ายต่อการ mock ในการ test

5. **Implement Optimistic Updates**

    - อัพเดท UI ทันที
    - Revert ถ้า API fail

6. **ใช้ Map แทน Object**
    - Performance ดีกว่า
    - Built-in methods ครบ

### ❌ **DON'Ts**

1. **อย่าเก็บทุกอย่างใน Store**

    - Local component state ใช้ `ref` ธรรมดา
    - Store เฉพาะข้อมูลที่ share กัน

2. **อย่า Mutate State โดยตรง**

    - ใช้ Actions เสมอ

3. **อย่าเก็บ Computed Properties ใน State**

    - ใช้ Getters แทน

4. **อย่าเก็บ DOM References**
    - ใช้ในระดับ component เท่านั้น

---

## 🔄 Migration Path (แผนการย้าย)

### **Phase 1: เตรียมโครงสร้าง**

1. สร้าง folder structure ใหม่
2. สร้าง composables พื้นฐาน
3. สร้าง service layer

### **Phase 2: Refactor Store ทีละส่วน**

1. เริ่มจาก `courseStore`
2. แยก UI state ออกมา
3. Implement cache & loading

### **Phase 3: Update Components**

1. อัพเดท components ใช้ Store ใหม่
2. ทดสอบ functionality
3. ลบ code เก่า

### **Phase 4: Optimize**

1. Implement optimistic updates
2. เพิ่ม error handling
3. Performance tuning

---

## 📊 ตัวอย่าง Store Structure (สมบูรณ์)

```
stores/
├── modules/
│   ├── course/
│   │   ├── courseStore.js          # Main course data
│   │   ├── courseUIStore.js        # UI states
│   │   └── courseMemberStore.js    # Course members
│   ├── lesson/
│   │   ├── lessonStore.js
│   │   └── lessonProgressStore.js
│   ├── attendance/
│   │   └── attendanceStore.js
│   └── user/
│       ├── userStore.js
│       └── authStore.js
├── composables/
│   ├── useApi.js
│   ├── useCache.js
│   ├── useLoading.js
│   ├── usePagination.js
│   └── useWebSocket.js
└── services/
    ├── courseService.js
    ├── lessonService.js
    ├── attendanceService.js
    └── userService.js
```

---

## 🚀 Advanced Features

### **1. WebSocket Integration**

```javascript
// composables/useWebSocket.js
import { ref, onUnmounted } from "vue";

export function useWebSocket(url) {
    const ws = ref(null);
    const data = ref(null);
    const isConnected = ref(false);

    const connect = () => {
        ws.value = new WebSocket(url);

        ws.value.onopen = () => {
            isConnected.value = true;
        };

        ws.value.onmessage = (event) => {
            data.value = JSON.parse(event.data);
        };

        ws.value.onclose = () => {
            isConnected.value = false;
        };
    };

    const send = (message) => {
        if (ws.value && isConnected.value) {
            ws.value.send(JSON.stringify(message));
        }
    };

    const close = () => {
        if (ws.value) {
            ws.value.close();
        }
    };

    onUnmounted(() => {
        close();
    });

    return {
        data,
        isConnected,
        connect,
        send,
        close,
    };
}
```

### **2. Pagination Helper**

```javascript
// composables/usePagination.js
import { ref, computed } from "vue";

export function usePagination(options = {}) {
    const { initialPage = 1, initialPerPage = 10 } = options;

    const currentPage = ref(initialPage);
    const perPage = ref(initialPerPage);
    const total = ref(0);
    const data = ref([]);

    const totalPages = computed(() => {
        return Math.ceil(total.value / perPage.value);
    });

    const hasNextPage = computed(() => {
        return currentPage.value < totalPages.value;
    });

    const hasPreviousPage = computed(() => {
        return currentPage.value > 1;
    });

    const setData = (newData, newTotal) => {
        data.value = newData;
        total.value = newTotal;
    };

    const nextPage = () => {
        if (hasNextPage.value) {
            currentPage.value++;
        }
    };

    const previousPage = () => {
        if (hasPreviousPage.value) {
            currentPage.value--;
        }
    };

    const goToPage = (page) => {
        if (page >= 1 && page <= totalPages.value) {
            currentPage.value = page;
        }
    };

    return {
        currentPage,
        perPage,
        total,
        data,
        totalPages,
        hasNextPage,
        hasPreviousPage,
        setData,
        nextPage,
        previousPage,
        goToPage,
    };
}
```

---

## 📝 สรุป

โครงสร้าง Store นี้ออกแบบมาเพื่อ:

✅ **Scalability** - ขยายได้ง่าย  
✅ **Maintainability** - แก้ไขง่าย  
✅ **Testability** - ทดสอบง่าย  
✅ **Reusability** - ใช้ซ้ำได้  
✅ **Performance** - ประสิทธิภาพสูง

เริ่มทีละขั้นตอน ไม่ต้องทำทั้งหมดพร้อมกัน! 🎉
