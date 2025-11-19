# ระบบจัดการโซนการเยี่ยมบ้าน (Home Visit Zones)

## สรุปการเปลี่ยนแปลง

เพิ่มฟีเจอร์การจัดการโซนสำหรับระบบการเยี่ยมบ้านนักเรียน ช่วยให้สามารถแบ่งพื้นที่การเยี่ยมบ้านออกเป็นโซนต่างๆ และครูสามารถเลือกโซนเมื่อสร้างบันทึกการเยี่ยมบ้าน

## ไฟล์ที่สร้างใหม่

### 1. Migration Files

-   `database/migrations/2025_11_19_102158_create_home_visit_zones_table.php`
    -   สร้างตาราง `home_visit_zones` เก็บข้อมูลโซน
-   `database/migrations/2025_11_19_102224_add_zone_id_to_student_home_visits_table.php`
    -   เพิ่มฟิลด์ `zone_id` ในตาราง `student_home_visits`

### 2. Model

-   `app/Models/HomeVisitZone.php`
    -   Model สำหรับจัดการข้อมูลโซน
    -   มี scope: `active()`, `ordered()`
    -   มี relationship กับ `StudentHomeVisit`

### 3. Controller

-   `app/Http/Controllers/Learn/Student/HomeVisit/ZoneController.php`
    -   จัดการ CRUD operations สำหรับโซน
    -   รองรับการเรียงลำดับโซน
    -   สิทธิ์เข้าถึง: Admin สำหรับการจัดการ, Teacher สามารถดูรายการโซนได้

### 4. Seeder

-   `database/seeders/HomeVisitZoneSeeder.php`
    -   ข้อมูลโซนตัวอย่าง 5 โซน: เหนือ, ใต้, ตะวันออก, ตะวันตก, กลาง

## โครงสร้างตาราง home_visit_zones

```sql
- id (bigint, primary key)
- zone_name (string) - ชื่อโซน
- zone_code (string, unique) - รหัสโซน
- description (text, nullable) - รายละเอียดโซน
- color (string, nullable) - สีประจำโซน (hex color)
- is_active (boolean, default: true) - สถานะใช้งาน
- display_order (integer, default: 0) - ลำดับการแสดง
- created_at (timestamp)
- updated_at (timestamp)
```

## API Endpoints

### สำหรับ Admin (ต้อง login เป็น Admin)

#### 1. ดูรายการโซนแบบมี pagination

```
GET /home-visit/admin/zones
Query Parameters:
  - search: ค้นหาจากชื่อ/รหัส/รายละเอียด
  - is_active: กรองตามสถานะ (true/false)
  - per_page: จำนวนรายการต่อหน้า (default: 15)
```

#### 2. ดูข้อมูลโซนเดียว

```
GET /home-visit/admin/zones/{id}
```

#### 3. สร้างโซนใหม่

```
POST /home-visit/admin/zones
Body:
{
  "zone_name": "โซนเหนือ",
  "zone_code": "NORTH",
  "description": "พื้นที่ทางตอนเหนือ",
  "color": "#3B82F6",
  "is_active": true,
  "display_order": 1
}
```

#### 4. แก้ไขโซน

```
PUT /home-visit/admin/zones/{id}
Body: เหมือนกับการสร้าง
```

#### 5. ลบโซน

```
DELETE /home-visit/admin/zones/{id}
หมายเหตุ: ไม่สามารถลบโซนที่มีการเยี่ยมบ้านอยู่
```

#### 6. เปิด/ปิดใช้งานโซน

```
PUT /home-visit/admin/zones/{id}/toggle-status
```

#### 7. เรียงลำดับโซน

```
POST /home-visit/admin/zones/reorder
Body:
{
  "zones": [
    { "id": 1, "display_order": 1 },
    { "id": 2, "display_order": 2 }
  ]
}
```

### สำหรับ Teacher

#### ดูรายการโซนที่ active (สำหรับ dropdown)

```
GET /home-visit/zones?active_only=true
```

## การอัพเดทในส่วนอื่นๆ

### 1. StudentHomeVisit Model

-   เพิ่ม `zone_id` ใน fillable
-   เพิ่ม relationship `zone()` กับ HomeVisitZone

### 2. TeacherController

-   อัพเดทเมธอด `dashboard()` เพื่อส่งข้อมูล zones ไปยัง frontend
-   อัพเดทเมธอด `manageStudent()` เพื่อส่งข้อมูล zones
-   อัพเดทเมธอด `createHomeVisitWithImages()` รองรับ `zone_id`
-   อัพเดทเมธอด `updateHomeVisitWithImages()` รองรับ `zone_id`
-   เพิ่ม import `HomeVisitZone` model

### 3. Routes

-   เพิ่ม import `ZoneController`
-   เพิ่ม routes สำหรับจัดการโซน (ภายใ่ admin prefix)
-   เพิ่ม route สำหรับดูรายการโซน (สำหรับ teacher)

## การใช้งาน

### 1. รัน Migration

```bash
php artisan migrate
```

### 2. เพิ่มข้อมูลโซนตัวอย่าง

```bash
php artisan db:seed --class=HomeVisitZoneSeeder
```

### 3. การจัดการโซนสำหรับ Admin

-   เข้าสู่ระบบในฐานะ Admin
-   ไปที่หน้าจัดการโซน
-   สามารถเพิ่ม/แก้ไข/ลบ/เปิด-ปิดใช้งานโซนได้

### 4. การเลือกโซนสำหรับ Teacher

-   เมื่อสร้างบันทึกการเยี่ยมบ้าน
-   จะมี dropdown ให้เลือกโซน
-   แสดงเฉพาะโซนที่ active เท่านั้น
-   สามารถไม่เลือกโซนก็ได้ (optional)

## ตัวอย่างข้อมูล Response

### Zone Object

```json
{
    "id": 1,
    "zone_name": "โซนเหนือ",
    "zone_code": "NORTH",
    "description": "พื้นที่ทางตอนเหนือของเขตพื้นที่บริการ",
    "color": "#3B82F6",
    "is_active": true,
    "display_order": 1,
    "created_at": "2025-11-19T10:30:00.000000Z",
    "updated_at": "2025-11-19T10:30:00.000000Z",
    "home_visits_count": 5
}
```

### Home Visit with Zone

```json
{
    "id": 1,
    "student_id": 123,
    "zone_id": 1,
    "visit_date": "2025-11-19",
    "zone": {
        "id": 1,
        "zone_name": "โซนเหนือ",
        "zone_code": "NORTH",
        "color": "#3B82F6"
    }
}
```

## Validation Rules

### สำหรับสร้าง/แก้ไขโซน

-   `zone_name`: required, string, max:255
-   `zone_code`: required, string, max:255, unique
-   `description`: nullable, string
-   `color`: nullable, string, max:50
-   `is_active`: boolean
-   `display_order`: integer, min:0

### สำหรับสร้าง/แก้ไขการเยี่ยมบ้าน

-   `zone_id`: nullable, exists:home_visit_zones,id

## หมายเหตุ

1. โซนไม่สามารถลบได้หากมีการเยี่ยมบ้านที่เชื่อมโยงอยู่
2. การปิดใช้งานโซนจะไม่ส่งผลกับข้อมูลการเยี่ยมบ้านเดิม
3. สีของโซนควรเป็น hex color code (เช่น #3B82F6)
4. zone_code ควรเป็น unique และใช้ตัวพิมพ์ใหญ่
5. การเลือกโซนเป็น optional ครูสามารถไม่เลือกก็ได้

## การอัพเดทในอนาคต

1. เพิ่มฟีเจอร์กรองการเยี่ยมบ้านตามโซน
2. เพิ่มรายงานสถิติแยกตามโซน
3. เพิ่มการกำหนดโซนให้กับนักเรียนโดยตรง (ถ้าต้องการ)
4. เพิ่ม map integration แสดงโซนบนแผนที่

## การทดสอบ

### ทดสอบ API สำหรับ Admin

```bash
# ดูรายการโซน
curl -X GET http://localhost/home-visit/admin/zones

# สร้างโซนใหม่
curl -X POST http://localhost/home-visit/admin/zones \
  -H "Content-Type: application/json" \
  -d '{
    "zone_name": "โซนทดสอบ",
    "zone_code": "TEST",
    "description": "โซนสำหรับทดสอบ",
    "color": "#000000",
    "is_active": true,
    "display_order": 99
  }'
```

### ทดสอบ API สำหรับ Teacher

```bash
# ดูรายการโซนที่ active
curl -X GET http://localhost/home-visit/zones?active_only=true
```

---

**วันที่สร้าง**: 19 พฤศจิกายน 2568  
**ผู้สร้าง**: System Administrator  
**เวอร์ชัน**: 1.0.0
