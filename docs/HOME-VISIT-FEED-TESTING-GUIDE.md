# Visit Feed Timeline - Testing Guide

# คู่มือการทดสอบฟีดไทม์ไลน์การเยี่ยมบ้าน

## Overview | ภาพรวม

ฟีดไทม์ไลน์การเยี่ยมบ้านเป็นฟีเจอร์ใหม่ที่แสดงรายละเอียดการเยี่ยมบ้านนักเรียนในรูปแบบ Social Media Timeline คล้ายกับโพสต์บน Facebook เพื่อให้ Admin เห็นความคืบหน้าได้อย่างชัดเจน

## Files Created | ไฟล์ที่สร้างขึ้น

1. **VisitFeed.vue** (`resources/js/Pages/Learn/Student/HomeVisit/Admin/Components/VisitFeed.vue`)

    - Component หลักสำหรับแสดงไทม์ไลน์
    - มีระบบกรองข้อมูล (status, zone, date range)
    - แสดงรูปภาพ, สรุปเนื้อหา, metadata
    - รองรับการขยายเนื้อหา (expand/collapse)
    - มี pagination แบ่งหน้า

2. **visitFeedData.js** (`resources/js/Pages/Learn/Student/HomeVisit/Admin/MockData/visitFeedData.js`)

    - ข้อมูลจำลองสำหรับทดสอบ
    - สร้างข้อมูล 30 รายการพร้อมรูปภาพ
    - มีฟังก์ชันสร้างข้อมูลแบบ dynamic

3. **Dashboard.vue** (อัปเดต)

    - เพิ่มแท็บใหม่ "ฟีดการเยี่ยมบ้าน"
    - เชื่อมต่อกับ VisitFeed component
    - รองรับทั้ง desktop และ mobile

4. **AdminController.php** (อัปเดต)
    - เพิ่มเมธอด `dashboardMock()` สำหรับทดสอบ
    - สร้างข้อมูลจำลองฝั่ง backend
    - Route: `/home-visit/admin/dashboard/mock`

## Features | ฟีเจอร์

### 1. Timeline Display | การแสดงผลแบบไทม์ไลน์

-   แสดงโพสต์เรียงตามวันที่ล่าสุด
-   มีเส้นแนวตั้งเชื่อมต่อระหว่างโพสต์
-   ไอคอนสถานะแบบสี (completed=เขียว, pending=เทา, in-progress=เหลือง, cancelled=แดง)

### 2. Post Card | การ์ดโพสต์

**Header:**

-   ชื่อนักเรียน + ชั้นเรียน
-   ครูผู้เยี่ยม
-   วันที่และเวลา
-   สถานะ (badge สี)

**Content:**

-   สรุปการเยี่ยมบ้าน (summary/notes)
-   แกลเลอรีรูปภาพ (แสดงสูงสุด 3 ภาพ, มี +N ถ้ามีเพิ่ม)

**Meta Information:**

-   โซน
-   ผู้เยี่ยม
-   ระยะเวลา (นาที)
-   วันนัดครั้งต่อไป

**Expandable Section:** (กดขยายดู)

-   ประเด็นที่พบ (risks)
-   ข้อเสนอแนะ (recommendations)
-   การติดตามผล (follow_up_actions)

**Actions:**

-   ปุ่ม "ดูรายละเอียด" → เปิด Modal แสดงข้อมูลเต็ม
-   ปุ่ม "ขยาย/ย่อ" → แสดง/ซ่อนข้อมูลเพิ่มเติม

### 3. Filters | ตัวกรอง

-   **สถานะ:** ทั้งหมด, รอดำเนินการ, กำลังดำเนินการ, เสร็จสิ้น, ยกเลิก
-   **โซน:** เลือกจากโซนที่มี
-   **ช่วงวันที่:** วันที่เริ่ม - วันที่สิ้นสุด
-   ปุ่ม "ล้างตัวกรอง"
-   แสดงจำนวนรายการที่พบ

### 4. Pagination | การแบ่งหน้า

-   แสดง 10 รายการต่อหน้า
-   ปุ่มก่อนหน้า/ถัดไป
-   แสดงหน้าปัจจุบัน/ทั้งหมด

## Testing Instructions | วิธีทดสอบ

### Option 1: ทดสอบด้วยข้อมูลจำลอง (Mock Data) - แนะนำ

1. **เข้าสู่ระบบ Admin**

    ```
    URL: http://localhost/home-visit/
    เลือก: Admin Login
    ```

2. **เข้าหน้าทดสอบด้วยข้อมูลจำลอง**

    ```
    URL: http://localhost/home-visit/admin/dashboard/mock
    ```

    หรือแก้ไข URL จาก `/dashboard` เป็น `/dashboard/mock`

3. **คลิกแท็บ "ฟีดการเยี่ยมบ้าน"**
    - จะเห็นไทม์ไลน์ 30 รายการ
    - ข้อมูลสร้างอัตโนมัติพร้อมรูปภาพจาก Unsplash

### Option 2: ทดสอบด้วยข้อมูลจริง

1. **เข้าหน้า Dashboard ปกติ**

    ```
    URL: http://localhost/home-visit/admin/dashboard
    ```

2. **คลิกแท็บ "ฟีดการเยี่ยมบ้าน"**
    - จะใช้ข้อมูลจริงจากฐานข้อมูล
    - ถ้ายังไม่มีข้อมูล จะแสดงว่า "ไม่พบข้อมูล"

## Test Cases | กรณีทดสอบ

### 1. Filter Testing | ทดสอบการกรอง

**Test 1.1: Filter by Status**

```
1. เลือกสถานะ "เสร็จสิ้น"
2. ตรวจสอบว่าแสดงเฉพาะโพสต์ที่เสร็จสิ้น
3. Badge สีเขียว
4. จำนวนรายการลดลง
```

**Test 1.2: Filter by Zone**

```
1. เลือก "โซนกลาง"
2. ตรวจสอบว่าทุกโพสต์แสดงโซนกลาง
3. Meta info แสดง "โซน: โซนกลาง"
```

**Test 1.3: Filter by Date Range**

```
1. เลือกวันที่เริ่ม: 2025-10-01
2. เลือกวันที่สิ้นสุด: 2025-10-31
3. ตรวจสอบว่าแสดงเฉพาะเดือนตุลาคม
```

**Test 1.4: Multiple Filters**

```
1. เลือกสถานะ "เสร็จสิ้น" + โซน "โซนเหนือ"
2. ตรวจสอบว่าผลลัพธ์ตรงทั้ง 2 เงื่อนไข
3. กดปุ่ม "ล้างตัวกรอง"
4. ตรวจสอบว่ากลับมาแสดงทั้งหมด
```

### 2. Post Card Interaction | ทดสอบการโต้ตอบ

**Test 2.1: Expand/Collapse**

```
1. คลิกปุ่ม "ขยาย" บนโพสต์
2. ตรวจสอบว่าแสดงส่วน risks, recommendations, follow_up_actions
3. คลิก "ย่อ"
4. ตรวจสอบว่าซ่อนส่วนเพิ่มเติม
```

**Test 2.2: View Details Modal**

```
1. คลิกปุ่ม "ดูรายละเอียด"
2. Modal เปิดขึ้นแสดงข้อมูลเต็ม
3. ตรวจสอบว่ามีข้อมูลครบถ้วน
4. คลิกปิด Modal
```

**Test 2.3: Image Gallery**

```
1. คลิกที่รูปภาพในโพสต์
2. Modal เปิดแสดงแกลเลอรีเต็ม
3. ดูภาพทั้งหมด
```

### 3. Pagination | ทดสอบการแบ่งหน้า

**Test 3.1: Navigate Pages**

```
1. ตรวจสอบว่าแสดง "หน้า 1 / X"
2. คลิก "ถัดไป"
3. ตรวจสอบว่าเปลี่ยนเป็น "หน้า 2 / X"
4. คลิก "ก่อนหน้า"
5. กลับหน้า 1
```

**Test 3.2: Disabled States**

```
1. หน้า 1: ปุ่ม "ก่อนหน้า" ควร disabled
2. หน้าสุดท้าย: ปุ่ม "ถัดไป" ควร disabled
```

### 4. Responsive Design | ทดสอบการตอบสนอง

**Test 4.1: Desktop View (> 1024px)**

```
1. แสดงทั้ง 3 แท็บ: แดชบอร์ด, ฟีดการเยี่ยมบ้าน, ตั้งค่าระบบ
2. ตัวกรอง 4 คอลัมน์
3. Meta info 4 คอลัมน์
```

**Test 4.2: Mobile View (< 640px)**

```
1. Navigation เป็น select dropdown
2. ตัวกรอง stack 1 คอลัมน์
3. รูปภาพ grid 3 คอลัมน์ยังคงอยู่
```

### 5. Performance | ทดสอบประสิทธิภาพ

**Test 5.1: Large Dataset**

```
1. สร้างข้อมูลจำลอง 100 รายการ (แก้ใน visitFeedData.js: generateMockVisits(100))
2. ตรวจสอบว่าโหลดไม่ช้า
3. กรองข้อมูลควรรวดเร็ว
4. Pagination ทำงานปกติ
```

## Mock Data Structure | โครงสร้างข้อมูลจำลอง

### Mock Students

```javascript
{
    id, first_name, last_name, classroom;
}
```

### Mock Zones

```javascript
{
    id, name, zone_name, color;
}
```

### Mock Visit

```javascript
{
  id, student_id, student, zone_id, zone,
  teacher_name, visitor_name,
  visit_date, visit_status,
  summary, notes, duration,
  risks: [], recommendations: [], follow_up_actions: [],
  next_schedule, images: [],
  created_at, updated_at
}
```

## Customization | การปรับแต่ง

### เพิ่มจำนวนข้อมูลจำลอง

แก้ไขใน `AdminController.php`:

```php
// ในเมธอด generateMockData()
for ($i = 1; $i <= 50; $i++) { // เปลี่ยนจาก 30 เป็น 50
```

### เปลี่ยนจำนวนต่อหน้า

แก้ไขใน `VisitFeed.vue`:

```javascript
const perPage = ref(20); // เปลี่ยนจาก 10 เป็น 20
```

### เพิ่มรูปภาพเพิ่มเติม

แก้ไขใน `AdminController.php`:

```php
$imageUrls = [
  'https://images.unsplash.com/photo-xxx?w=400',
  'https://images.unsplash.com/photo-yyy?w=400',
  // เพิ่ม URL ใหม่
];
```

## Troubleshooting | แก้ไขปัญหา

### Problem: ไม่มีข้อมูลแสดง

**Solution:**

1. ตรวจสอบว่าใช้ `/dashboard/mock` (มี mock ต่อท้าย)
2. ล้าง cache: `Ctrl + F5`
3. ตรวจสอบ console ว่ามี error

### Problem: รูปภาพไม่แสดง

**Solution:**

1. ตรวจสอบ internet connection (ดึงจาก Unsplash)
2. ดู console ว่ามี CORS error
3. ลองใช้ URL รูปภาพอื่น

### Problem: Filter ไม่ทำงาน

**Solution:**

1. เปิด Vue DevTools ดู state ของ filters
2. ตรวจสอบว่า filteredVisits computed ทำงาน
3. Console.log ดูว่าข้อมูลถูกกรองถูกต้อง

### Problem: Modal ไม่เปิด

**Solution:**

1. ตรวจสอบว่า event `view-details` emit ถูกต้อง
2. ดู `showDetailModal` และ `selectedVisit` ใน Dashboard
3. ตรวจสอบว่า VisitDetailModal import ถูกต้อง

## API Integration | การเชื่อมต่อ API (อนาคต)

เมื่อต้องการใช้ API จริง:

1. **สร้าง endpoint ใหม่**

```php
// routes/homevisit/homevisit.php
Route::get('/admin/visits/feed', [AdminController::class, 'visitFeed']);
```

2. **เพิ่มเมธอดใน Controller**

```php
public function visitFeed(Request $request) {
    $query = StudentHomeVisit::with(['student', 'zone', 'images']);

    if ($request->status) {
        $query->where('visit_status', $request->status);
    }

    return response()->json($query->paginate(10));
}
```

3. **เรียกใช้ใน Component**

```javascript
// VisitFeed.vue
import axios from "axios";

const fetchVisits = async () => {
    const res = await axios.get("/home-visit/admin/visits/feed", {
        params: filters.value,
    });
    visits.value = res.data.data;
};
```

## Next Steps | ขั้นตอนถัดไป

1. ✅ ทดสอบด้วยข้อมูลจำลอง
2. ⬜ ทดสอบด้วยข้อมูลจริง
3. ⬜ ปรับแต่ง UI ตามความต้องการ
4. ⬜ เพิ่ม infinite scroll (ถ้าต้องการ)
5. ⬜ เพิ่มการ group ตามนักเรียน
6. ⬜ เพิ่มระบบ comment/like
7. ⬜ สร้าง API endpoint จริง

## Support | การสนับสนุน

หากพบปัญหาหรือต้องการความช่วยเหลือ:

-   ตรวจสอบ Browser Console (F12)
-   ดู Network Tab สำหรับ API calls
-   ตรวจสอบ Vue DevTools
-   อ่าน error messages ใน Laravel log

---

**สร้างเมื่อ:** 19 พฤศจิกายน 2025  
**เวอร์ชัน:** 1.0  
**สถานะ:** พร้อมใช้งานทดสอบ
