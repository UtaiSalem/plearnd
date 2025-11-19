# การเพิ่ม Dropdown เลือกโซนในแบบฟอร์มสร้างการเยี่ยมบ้านของครู

## วันที่: 19 พฤศจิกายน 2025

## สรุปการทำงาน

เพิ่มฟังก์ชันการเลือกโซนในแบบฟอร์มสร้างและแก้ไขการเยี่ยมบ้านสำหรับครู โดยให้ครูสามารถเลือกโซนที่นักเรียนอยู่เพื่อจัดการข้อมูลการเยี่ยมบ้านตามพื้นที่

## ไฟล์ที่แก้ไข

### 1. Dashboard.vue (หน้าหลักของครู)

**ไฟล์:** `resources/js/Pages/Learn/Student/HomeVisit/Teacher/Dashboard.vue`

#### การเปลี่ยนแปลง:

1. **เพิ่ม zones ใน Props:**

```javascript
const props = defineProps({
    stats: Object,
    students: Object,
    classrooms: Array,
    zones: {
        type: Array,
        default: () => [],
    },
    filters: Object,
});
```

2. **เพิ่ม zone_id ใน visitForm:**

```javascript
const visitForm = useForm({
    visit_date: "",
    visit_time: "",
    notes: "",
    zone_id: "", // ← เพิ่มฟิลด์นี้
    images: [],
});
```

3. **เพิ่ม Dropdown เลือกโซนใน Modal:**

-   เพิ่มฟิลด์ dropdown ระหว่างฟิลด์ "วันที่เยี่ยม" และ "หัวข้อการเยี่ยม"
-   มีตัวเลือก "ไม่ระบุโซน" เป็นค่าเริ่มต้น
-   แสดงรายชื่อโซนที่ active จากฐานข้อมูล

4. **อัปเดต createNewHomeVisit function:**

```javascript
const createNewHomeVisit = () => {
    // ... existing code ...

    // Add zone_id if selected
    if (visitForm.zone_id) {
        formData.append("zone_id", visitForm.zone_id);
    }

    // ... rest of code ...
};
```

5. **ส่ง zones prop ไปยัง HomeVisitCard:**

```vue
<HomeVisitCard
    :home-visits="homeVisits"
    :visit-form="visitForm"
    :zones="zones"
    :create-new-home-visit="createNewHomeVisit"
    :handle-image-upload="handleImageUpload"
    :remove-image="removeImage"
    @visit-updated="onVisitUpdated"
/>
```

### 2. HomeVisitCard.vue (Component แสดงและจัดการการเยี่ยมบ้าน)

**ไฟล์:** `resources/js/Pages/Learn/Student/HomeVisit/Teacher/Components/HomeVisitCard.vue`

#### การเปลี่ยนแปลง:

1. **เพิ่ม zones ใน Props:**

```javascript
const props = defineProps({
    homeVisits: Array,
    visitForm: Object,
    zones: {
        type: Array,
        default: () => [],
    },
    createNewHomeVisit: Function,
    handleImageUpload: Function,
    removeImage: Function,
});
```

2. **เพิ่ม Dropdown เลือกโซนในฟอร์มสร้างการเยี่ยมบ้านใหม่:**

-   เพิ่มฟิลด์ dropdown หลังฟิลด์วันที่และเวลา
-   มี icon แผนที่ (`fa-map-marker-alt`) และข้อความ "(ไม่บังคับ)"
-   ผูกกับ `visitForm.zone_id`

3. **เพิ่ม Dropdown เลือกโซนในฟอร์มแก้ไขการเยี่ยมบ้าน:**

-   เพิ่มฟิลด์ dropdown ในส่วนแก้ไขข้อมูลการเยี่ยมบ้านเดิม
-   แสดงโซนปัจจุบันที่เลือกไว้ (ถ้ามี)
-   ผูกกับ `visit.zone_id`

4. **อัปเดต saveVisit function:**

```javascript
async function saveVisit(visit) {
    // ... existing code ...

    // Add zone_id if selected
    if (visit.zone_id) {
        formData.append("zone_id", visit.zone_id);
    }

    // ... rest of code ...
}
```

## Backend Support

### TeacherController.php

Backend มีการรองรับการทำงานกับ zone_id แล้วตั้งแต่การแก้ไขครั้งก่อน:

1. **Dashboard Method:**

```php
public function dashboard()
{
    // ...
    return Inertia::render('Learn/Student/HomeVisit/Teacher/Dashboard', [
        'students' => (object)['data' => []],
        'classrooms' => $classrooms,
        'zones' => HomeVisitZone::active()->ordered()->get(),  // ส่งโซนไปหน้าบ้าน
        'stats' => $stats,
        'filters' => [],
    ]);
}
```

2. **searchStudents Method:**

```php
public function searchStudents(Request $request)
{
    // ...
    $zones = HomeVisitZone::active()->ordered()->get();

    return Inertia::render('Learn/Student/HomeVisit/Teacher/Dashboard', [
        'students' => $students,
        'classrooms' => $classrooms,
        'zones' => $zones,  // ส่งโซนไปหน้าบ้าน
        'stats' => $stats,
        'filters' => $request->only(['search', 'classroom']),
    ]);
}
```

3. **createHomeVisitWithImages Method:**

```php
public function createHomeVisitWithImages(Request $request, $studentId)
{
    $request->validate([
        'visit_date' => 'required|date',
        'visit_time' => 'nullable',
        'zone_id' => 'nullable|exists:home_visit_zones,id',  // มี validation แล้ว
        'observations' => 'nullable|string',
        'notes' => 'nullable|string',
        // ...
    ]);

    // ...

    $homeVisit = StudentHomeVisit::createFromStudent($student, [
        'visit_date' => $request->visit_date,
        'visit_time' => $request->visit_time,
        'zone_id' => $request->zone_id,  // บันทึก zone_id
        // ...
    ]);
}
```

4. **updateHomeVisitWithImages Method:**

```php
public function updateHomeVisitWithImages(Request $request, $homeVisitId)
{
    $request->validate([
        'visit_date' => 'required|date',
        'visit_time' => 'nullable',
        'zone_id' => 'nullable|exists:home_visit_zones,id',  // มี validation แล้ว
        // ...
    ]);

    // ...

    $homeVisit->update([
        'visit_date' => $request->visit_date,
        'visit_time' => $request->visit_time,
        'zone_id' => $request->zone_id,  // อัปเดต zone_id
        // ...
    ]);
}
```

## UI/UX Design

### ฟอร์มสร้างการเยี่ยมบ้านใหม่:

```
┌─────────────────────────────────────────┐
│ วันที่เยี่ยม *                          │
│ [2025-11-19]                            │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│ 📍 โซน (ไม่บังคับ)                     │
│ [▼ ไม่ระบุโซน            ]            │
│   - ไม่ระบุโซน                         │
│   - โซนกลาง                             │
│   - โซนเหนือ                            │
│   - โซนใต้                              │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│ หัวข้อการเยี่ยม *                      │
│ [เช่น ติดตามผลการเรียน]               │
└─────────────────────────────────────────┘
```

### ฟอร์มแก้ไขการเยี่ยมบ้าน:

```
┌─────────────────────────────────────────┐
│ วันที่เยี่ยม *                          │
│ [2025-11-19]                            │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│ 📍 โซน (ไม่บังคับ)                     │
│ [▼ โซนกลาง                ]            │
│ โซนปัจจุบัน: โซนกลาง                   │
└─────────────────────────────────────────┘
```

## คุณสมบัติ

✅ **ฟิลด์ zone_id เป็น optional** - ครูสามารถเลือกหรือไม่เลือกโซนก็ได้
✅ **แสดงเฉพาะโซนที่ active** - ใช้ scope `active()` และ `ordered()` จาก Model
✅ **รองรับทั้งการสร้างและแก้ไข** - มี dropdown ในทั้งสองฟอร์ม
✅ **แสดงโซนปัจจุบัน** - ในฟอร์มแก้ไข จะแสดงโซนที่เลือกไว้ก่อนหน้า
✅ **Backend Validation** - มีการ validate `zone_id` ที่ backend แล้ว
✅ **ไม่มี Error** - ผ่านการตรวจสอบ syntax แล้ว

## การทดสอบ

### ขั้นตอนการทดสอบ:

1. **ทดสอบการสร้างการเยี่ยมบ้านใหม่:**

    - เข้าหน้า Teacher Dashboard
    - ค้นหานักเรียน
    - คลิกปุ่ม "สร้างการเยี่ยมบ้านใหม่"
    - เลือกโซนจาก dropdown
    - กรอกข้อมูลอื่นๆ และบันทึก
    - ตรวจสอบว่า zone_id ถูกบันทึกลงฐานข้อมูล

2. **ทดสอบการแก้ไขการเยี่ยมบ้าน:**

    - เปิดข้อมูลการเยี่ยมบ้านที่มีอยู่แล้ว
    - เปลี่ยนโซนจาก dropdown
    - บันทึกการแก้ไข
    - ตรวจสอบว่า zone_id ถูกอัปเดต

3. **ทดสอบกรณีไม่เลือกโซน:**

    - สร้างการเยี่ยมบ้านโดยเลือก "ไม่ระบุโซน"
    - บันทึกสำเร็จโดย zone_id เป็น null

4. **ทดสอบการแสดงผล:**
    - ตรวจสอบว่า dropdown แสดงเฉพาะโซนที่ is_active = 1
    - ตรวจสอบว่าลำดับการแสดงโซนถูกต้องตาม display_order

## บันทึกเพิ่มเติม

-   ฟิลด์ zone_id เป็น nullable ใน database migration อยู่แล้ว
-   การแสดงข้อมูลโซนในรายการการเยี่ยมบ้านสามารถเพิ่มได้ในอนาคต (เช่น แสดงชื่อโซนเป็น badge)
-   Model StudentHomeVisit มี relationship `zone()` อยู่แล้ว สามารถใช้ eager loading ได้
-   การ validate ที่ backend ใช้ `exists:home_visit_zones,id` เพื่อตรวจสอบว่าโซนมีอยู่จริง

## สรุป

การเพิ่ม dropdown เลือกโซนในแบบฟอร์มการเยี่ยมบ้านของครูเสร็จสมบูรณ์แล้ว โดย:

-   ✅ Frontend: เพิ่ม dropdown ทั้งในฟอร์มสร้างและแก้ไข
-   ✅ Data Binding: ผูกข้อมูลกับ visitForm.zone_id และ visit.zone_id
-   ✅ Backend: รองรับการรับและบันทึก zone_id แล้ว
-   ✅ Validation: มีการ validate ที่ backend แล้ว
-   ✅ UI/UX: ออกแบบให้ชัดเจนและใช้งานง่าย พร้อมระบุว่าเป็น optional
