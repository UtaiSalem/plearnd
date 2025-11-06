# 📘 คู่มือการใช้งาน Composables และ Services

> เอกสารนี้อธิบายวิธีการใช้งาน composables และ services ที่เพิ่งสร้างขึ้น

## 📦 สิ่งที่สร้างเสร็จแล้ว

### ✅ Composables

-   `useCache.js` - จัดการ cache พร้อม TTL
-   `useLoading.js` - จัดการ loading states
-   `useApi.js` - Wrapper สำหรับ API calls

### ✅ Services

-   `courseService.js` - API endpoints สำหรับ Course

---

## 🔧 วิธีการใช้งาน

### **1. useCache - จัดการ Cache**

```javascript
import { useCache } from "@/stores/composables";

// สร้าง cache instance
const { setCache, getCache, isCacheValid, clearCache } = useCache("course", {
    ttl: 5 * 60 * 1000, // 5 นาที
    maxSize: 100, // จำนวนสูงสุด 100 items
});

// บันทึกข้อมูล
setCache("course_1", { id: 1, name: "Math 101" });

// ดึงข้อมูล
const data = getCache("course_1");

// ตรวจสอบความ valid
if (isCacheValid("course_1")) {
    // ใช้ข้อมูลจาก cache
} else {
    // ดึงข้อมูลใหม่
}

// ลบ cache
clearCache("course_1"); // ลบ specific key
clearCache(); // ลบทั้งหมด
```

---

### **2. useLoading - จัดการ Loading States**

```javascript
import { useLoading } from "@/stores/composables";

const { setLoading, isLoading, isAnyLoading, withLoading } = useLoading();

// ตั้งค่า loading
setLoading("fetch_course", true);
// ... ทำงาน
setLoading("fetch_course", false);

// ตรวจสอบสถานะ
if (isLoading.value("fetch_course")) {
    console.log("กำลังโหลด...");
}

// ตรวจสอบว่ามี loading ใดๆ
if (isAnyLoading.value) {
    console.log("มีบางอย่างกำลังโหลด");
}

// ใช้ withLoading wrapper (แนะนำ)
const data = await withLoading("fetch_course", async () => {
    return await fetchData();
});
```

---

### **3. useApi - API Calls**

```javascript
import { useApi } from "@/stores/composables";

const { get, post, patch, del, upload, error, isLoading } = useApi();

// GET request
const courses = await get("/api/courses", { page: 1 });

// POST request
const newCourse = await post("/api/courses", {
    name: "Math 101",
    code: "MTH101",
});

// PATCH request
const updated = await patch(`/api/courses/${id}`, {
    name: "New Name",
});

// DELETE request
await del(`/api/courses/${id}`);

// Upload file
const formData = new FormData();
formData.append("cover", file);
const result = await upload(`/api/courses/${id}/cover`, formData);

// จัดการ error
if (error.value) {
    console.error(error.value);
}

// Custom options
const data = await get(
    "/api/courses",
    {},
    {
        showLoading: true,
        throwError: false,
        retries: 3,
        retryDelay: 1000,
        onSuccess: (data) => {
            console.log("Success!", data);
        },
        onError: (err) => {
            console.error("Error!", err);
        },
    }
);
```

---

### **4. courseService - Course API**

```javascript
import { courseService } from "@/services";

// ดึงข้อมูลรายวิชา
const course = await courseService.getCourse(courseId);

// ดึงรายการรายวิชา
const courses = await courseService.getCourses({
    page: 1,
    perPage: 10,
});

// อัพเดทรูปปก
await courseService.updateCourseCover(courseId, file);

// อัพเดทโลโก้
await courseService.updateCourseLogo(courseId, file);

// อัพเดทชื่อ
await courseService.updateCourseHeader(courseId, "New Name");

// อัพเดทรหัสวิชา
await courseService.updateCourseSubheader(courseId, "MTH101");

// ขอเป็นสมาชิก
await courseService.requestMembership(courseId, groupId);

// ยกเลิกสมาชิก
await courseService.cancelMembership(courseId, memberId);

// ดึงสมาชิก
const members = await courseService.getCourseMembers(courseId);

// ดึงกลุ่ม
const groups = await courseService.getCourseGroups(courseId);
```

---

## 🎯 ตัวอย่างการใช้งานจริง

### **ตัวอย่าง 1: Fetch Course with Cache**

```javascript
import { useCache, useLoading } from "@/stores/composables";
import { courseService } from "@/services";

const { setCache, getCache, isCacheValid } = useCache("course");
const { withLoading, isLoading } = useLoading();

async function fetchCourse(courseId) {
    const cacheKey = `course_${courseId}`;

    // ตรวจสอบ cache
    if (isCacheValid(cacheKey)) {
        return getCache(cacheKey);
    }

    // Fetch ข้อมูลใหม่
    const data = await withLoading("fetch_course", async () => {
        return await courseService.getCourse(courseId);
    });

    // บันทึก cache
    setCache(cacheKey, data);

    return data;
}
```

---

### **ตัวอย่าง 2: Update with Optimistic UI**

```javascript
import { courseService } from "@/services";
import { useLoading } from "@/stores/composables";

const { withLoading, isLoading } = useLoading();

async function updateCourseName(courseId, newName) {
    // Optimistic update - อัพเดท UI ทันที
    const oldName = course.value.name;
    course.value.name = newName;

    try {
        await withLoading("update_course", async () => {
            return await courseService.updateCourseHeader(courseId, newName);
        });
    } catch (error) {
        // Revert ถ้า error
        course.value.name = oldName;
        console.error("Failed to update:", error);
    }
}
```

---

### **ตัวอย่าง 3: Multiple Loading States**

```javascript
import { useLoading } from '@/stores/composables';

const { isLoading } = useLoading();

// ใน component
const isUpdatingCover = computed(() => isLoading.value('update_cover'));
const isUpdatingLogo = computed(() => isLoading.value('update_logo'));
const isUpdatingName = computed(() => isLoading.value('update_name'));

// ใน template
<button :disabled="isUpdatingCover">
    <Icon v-if="isUpdatingCover" icon="loading" class="animate-spin" />
    Update Cover
</button>
```

---

## 📝 Best Practices

### ✅ **DO's**

1. **ใช้ withLoading สำหรับ async operations**

    ```javascript
    await withLoading("key", async () => {
        return await doSomething();
    });
    ```

2. **ตรวจสอบ cache ก่อน fetch**

    ```javascript
    if (!isCacheValid(key)) {
        await fetchData();
    }
    ```

3. **จัดการ error ให้ดี**

    ```javascript
    try {
        await api.post("/endpoint", data);
    } catch (error) {
        // แสดง notification หรือ alert
    }
    ```

4. **ใช้ Service Layer**

    ```javascript
    // ✅ ดี
    await courseService.getCourse(id);

    // ❌ ไม่ดี - เรียก axios โดยตรง
    await axios.get(`/courses/${id}`);
    ```

### ❌ **DON'Ts**

1. **อย่าเก็บข้อมูลซ้ำซ้อน**

    ```javascript
    // ❌ ไม่ดี
    const courses = ref([]);
    const coursesCache = ref({});

    // ✅ ดี - ใช้ cache เดียว
    const { getCache } = useCache("course");
    ```

2. **อย่าลืม clear loading state**

    ```javascript
    // ❌ ไม่ดี
    setLoading("key", true);
    await doSomething();
    // ลืม setLoading('key', false);

    // ✅ ดี - ใช้ withLoading
    await withLoading("key", doSomething);
    ```

3. **อย่าเก็บ sensitive data ใน cache**

    ```javascript
    // ❌ ไม่ดี
    setCache("password", userPassword);

    // ✅ ดี - เก็บเฉพาะข้อมูลที่จำเป็น
    setCache("user", { id, name, email });
    ```

---

## 🚀 ขั้นตอนถัดไป

### **Phase 2: Refactor Store**

1. แยก `courseProfileStore` เป็น modular stores
2. ใช้ composables ที่สร้างขึ้น
3. ย้าย API calls ไปยัง Service Layer

### **Phase 3: Update Components**

1. อัพเดท `CourseProfileCover.vue` ใช้ Store ใหม่
2. ทดสอบ functionality
3. ลบ code เก่า

---

## 📊 โครงสร้างไฟล์ปัจจุบัน

```
resources/js/
├── stores/
│   ├── composables/
│   │   ├── index.js          ✅
│   │   ├── useCache.js       ✅
│   │   ├── useLoading.js     ✅
│   │   └── useApi.js         ✅
│   └── courseProfile.js      (เดิม - รอ refactor)
├── services/
│   ├── index.js              ✅
│   └── courseService.js      ✅
└── PlearndComponents/
    └── learn/
        └── courses/
            └── CourseProfileCover.vue (ใช้ Store เดิมอยู่)
```

---

## 💡 Tips

1. **Import แบบ destructure**

    ```javascript
    import { useCache, useLoading } from "@/stores/composables";
    ```

2. **ใช้ computed สำหรับ reactive values**

    ```javascript
    const isLoading = computed(() => store.isLoading("key"));
    ```

3. **ทดสอบใน Console ก่อน**
    ```javascript
    // เปิด DevTools Console
    import { useCache } from "@/stores/composables";
    const cache = useCache("test");
    cache.setCache("key", "value");
    console.log(cache.getCache("key"));
    ```

---

## 🎉 สรุป

คุณได้สร้าง:

-   ✅ 3 Composables (Cache, Loading, API)
-   ✅ 1 Service Layer (Course)
-   ✅ Index files สำหรับ export

**พร้อมใช้งานแล้ว!** 🚀

ขั้นตอนต่อไป: Refactor `courseProfileStore` ให้ใช้ composables เหล่านี้
