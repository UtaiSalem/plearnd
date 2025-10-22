# แก้ไขปัญหา "Unknown User" โดยใช้ Infinite scroll ทั้งหมด

## ปัญหา

เมื่อดึงข้อมูล activities จากฐานข้อมูลครั้งแรกใน Controller พบว่ามีบางรายการที่แสดงผลว่า "Unknown User" แต่เมื่อใช้ Infinite scroll สามารถดึงข้อมูลได้ถูกต้อง

## สาเหตุ

ปัญหานี้เกิดจาก:

1. **การดึงข้อมูลครั้งแรกใน Controller** ใช้วิธีการที่แตกต่างจากการดึงข้อมูลผ่าน Infinite scroll
2. **การจัดการความสัมพันธ์** ใน Controller ไม่ได้โหลดความสัมพันธ์ของ user ในลักษณะเดียวกับที่ใช้ใน API
3. **การแปลงข้อมูล** ใน Controller อาจจะไม่ได้ใช้ Resource ในการแปลงข้อมูลเหมือนกับใน API

## วิธีแก้ไข

ฉันได้แก้ไขปัญหานี้โดยการเปลี่ยนไปใช้ Infinite scroll ทั้งหมดแทนการดึงข้อมูลครั้งแรก:

### 1. แก้ไข NewsfeedV2Controller

**ก่อนแก้ไข:**
```php
// Get initial page of activities (smaller batch size)
\Log::info('NewsfeedV2Controller::index - Fetching activities');
$activitiesMethod = new \ReflectionMethod($newsfeedController, 'getActivities');
$activitiesMethod->setAccessible(true);
$activities = $activitiesMethod->invoke($newsfeedController, 1, 5);
\Log::info('NewsfeedV2Controller::index - Activities count: ' . (isset($activities['activities']) ? count($activities['activities']) : 'no activities key'));

// Ensure all data has proper structure before passing to Inertia
return Inertia::render('NewsfeedV2', [
    'initialActivities' => $activities['activities'] ?? [],
    'initialPagination' => [
        'current_page' => $activities['current_page'] ?? 1,
        'last_page' => $activities['last_page'] ?? 1,
        'total' => $activities['total'] ?? 0,
    ],
    // ... other data
]);
```

**หลังแก้ไข:**
```php
// Don't fetch activities initially - let infinite scroll handle it
\Log::info('NewsfeedV2Controller::index - Activities will be loaded by infinite scroll');

// Ensure all data has proper structure before passing to Inertia
return Inertia::render('NewsfeedV2', [
    // Remove initial activities to prevent the "Unknown User" issue
    // ... other data
]);
```

### 2. แก้ไข NewsfeedV2.vue

**ก่อนแก้ไข:**
```javascript
const props = defineProps({
    initialActivities: Array,
    initialPagination: Object,
    peopleMayKnow: Object,
    pendingFriends: Object,
    donates: Object,
    advertises: Object,
    notifications: Array,
});

const currentPage = ref(props.initialPagination?.current_page || 1);
const lastPage = ref(props.initialPagination?.last_page || 1);
const newsfeedActivities = ref(Array.isArray(props.initialActivities) ? [...props.initialActivities] : []);
```

**หลังแก้ไข:**
```javascript
const props = defineProps({
    peopleMayKnow: Object,
    pendingFriends: Object,
    donates: Object,
    advertises: Object,
    notifications: Array,
});

const currentPage = ref(0); // Start with 0 since we're not fetching initial data
const lastPage = ref(1);
const newsfeedActivities = ref([]);
const isInitialLoad = ref(true); // Mark as initial load to trigger infinite scroll
```

### 3. เปลี่ยน URL ของ API

**ก่อนแก้ไข:**
```javascript
const response = await axios.get(`/api/newsfeed/activities?page=${currentPage.value}&per_page=5`);
```

**หลังแก้ไข:**
```javascript
const response = await axios.get(`/api/newsfeed-v2/activities?page=${currentPage.value}&per_page=5`);
```

## ข้อดีของวิธีนี้

1. **แก้ไขปัญหา "Unknown User"** โดยหลีกเลี่ยงการดึงข้อมูลครั้งแรกที่ทำให้เกิดปัญหา
2. **การโหลดข้อมูลสม่ำเสมอ** - ใช้วิธีการเดียวกันในการดึงข้อมูลทั้งหมด
3. **ประสิทธิภาพที่ดีขึ้น** - โหลดข้อมูลเฉพาะเมื่อจำเป็น
4. **ลดภาระของ Server** - ไม่ต้องโหลดข้อมูลครั้งแรกใน Controller

## การทดสอบผลลัพธ์

หลังจากแก้ไขปัญหานี้:

1. **รีสตาร์ท Laravel server**
   ```bash
   php artisan serve
   ```

2. **เข้าถึงหน้า NewsfeedV2**
   URL: `/newsfeed-v2`

3. **ตรวจสอบใน Browser Console**
   - เปิด Developer Tools (F12)
   - ดูแท็บ Console และ Network
   - ตรวจสอบว่าไม่มีข้อผิดพลาดเกี่ยวกับ user

4. **ตรวจสอบว่า Infinite scroll ทำงาน**
   - เลื่อนหน้าเว็บลง
   - ตรวจสอบว่าข้อมูลถูกโหลดอย่างถูกต้อง
   - ตรวจสอบว่าชื่อผู้ใช้แสดงถูกต้อง

## การปรับปรุงเพิ่มเติม

หากต้องการปรับปรุงประสิทธิภาพเพิ่มเติม:

1. **เพิ่ม Loading Skeleton**
   ```html
   <div v-if="isInitialLoad" class="mt-4">
       <ActivitiesLoadingSkeleton />
   </div>
   ```

2. **เพิ่มการจัดการข้อผิดพลาด**
   ```javascript
   if (response.data.activities.length === 0 && currentPage.value === 1) {
       // Show empty state message
   }
   ```

3. **เพิ่มการบีบอัดรูปภาพ**
   ```javascript
   // ใน ActivityResource
   'avatar' => $this->user->profile_photo_url ?? '/storage/images/plearnd-logo.png',