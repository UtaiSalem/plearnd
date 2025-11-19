# API Testing Guide - Home Visit Zones

## การทดสอบ API ด้วย Postman หรือ cURL

### Base URL

```
http://localhost/home-visit
```

---

## 1. ทดสอบดูรายการโซน (สำหรับ Teacher)

### Request

```http
GET /home-visit/zones?active_only=true
```

### cURL Command

```bash
curl -X GET "http://localhost/home-visit/zones?active_only=true" \
  -H "Accept: application/json"
```

### Expected Response (200 OK)

```json
{
    "success": true,
    "zones": [
        {
            "id": 1,
            "zone_name": "โซนเหนือ",
            "zone_code": "NORTH",
            "description": "พื้นที่ทางตอนเหนือของเขตพื้นที่บริการ",
            "color": "#3B82F6",
            "is_active": true,
            "display_order": 1,
            "created_at": "2025-11-19T10:30:00.000000Z",
            "updated_at": "2025-11-19T10:30:00.000000Z"
        },
        {
            "id": 2,
            "zone_name": "โซนใต้",
            "zone_code": "SOUTH",
            "description": "พื้นที่ทางตอนใต้ของเขตพื้นที่บริการ",
            "color": "#EF4444",
            "is_active": true,
            "display_order": 2,
            "created_at": "2025-11-19T10:30:00.000000Z",
            "updated_at": "2025-11-19T10:30:00.000000Z"
        }
    ]
}
```

---

## 2. ทดสอบดูรายการโซนแบบมี Pagination (Admin)

### Request

```http
GET /home-visit/admin/zones?per_page=10&page=1&search=เหนือ&is_active=1
```

### cURL Command

```bash
curl -X GET "http://localhost/home-visit/admin/zones?per_page=10&page=1" \
  -H "Accept: application/json" \
  -H "Cookie: your-session-cookie"
```

### Query Parameters

-   `per_page` (optional): จำนวนรายการต่อหน้า (default: 15)
-   `page` (optional): หมายเลขหน้า (default: 1)
-   `search` (optional): ค้นหาจากชื่อ/รหัส/รายละเอียด
-   `is_active` (optional): กรองตามสถานะ (0 หรือ 1)

### Expected Response (200 OK)

```json
{
  "success": true,
  "zones": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "zone_name": "โซนเหนือ",
        "zone_code": "NORTH",
        "description": "พื้นที่ทางตอนเหนือของเขตพื้นที่บริการ",
        "color": "#3B82F6",
        "is_active": true,
        "display_order": 1,
        "home_visits_count": 5,
        "created_at": "2025-11-19T10:30:00.000000Z",
        "updated_at": "2025-11-19T10:30:00.000000Z"
      }
    ],
    "first_page_url": "http://localhost/home-visit/admin/zones?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost/home-visit/admin/zones?page=1",
    "links": [...],
    "next_page_url": null,
    "path": "http://localhost/home-visit/admin/zones",
    "per_page": 15,
    "prev_page_url": null,
    "to": 5,
    "total": 5
  }
}
```

---

## 3. ทดสอบดูข้อมูลโซนเดียว (Admin)

### Request

```http
GET /home-visit/admin/zones/{id}
```

### cURL Command

```bash
curl -X GET "http://localhost/home-visit/admin/zones/1" \
  -H "Accept: application/json" \
  -H "Cookie: your-session-cookie"
```

### Expected Response (200 OK)

```json
{
    "success": true,
    "zone": {
        "id": 1,
        "zone_name": "โซนเหนือ",
        "zone_code": "NORTH",
        "description": "พื้นที่ทางตอนเหนือของเขตพื้นที่บริการ",
        "color": "#3B82F6",
        "is_active": true,
        "display_order": 1,
        "home_visits_count": 5,
        "created_at": "2025-11-19T10:30:00.000000Z",
        "updated_at": "2025-11-19T10:30:00.000000Z"
    }
}
```

---

## 4. ทดสอบสร้างโซนใหม่ (Admin)

### Request

```http
POST /home-visit/admin/zones
Content-Type: application/json
```

### cURL Command

```bash
curl -X POST "http://localhost/home-visit/admin/zones" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Cookie: your-session-cookie" \
  -d '{
    "zone_name": "โซนทดสอบ",
    "zone_code": "TEST",
    "description": "โซนสำหรับทดสอบระบบ",
    "color": "#9333EA",
    "is_active": true,
    "display_order": 99
  }'
```

### Request Body

```json
{
    "zone_name": "โซนทดสอบ",
    "zone_code": "TEST",
    "description": "โซนสำหรับทดสอบระบบ",
    "color": "#9333EA",
    "is_active": true,
    "display_order": 99
}
```

### Expected Response (201 Created)

```json
{
    "success": true,
    "message": "เพิ่มโซนเรียบร้อยแล้ว",
    "zone": {
        "id": 6,
        "zone_name": "โซนทดสอบ",
        "zone_code": "TEST",
        "description": "โซนสำหรับทดสอบระบบ",
        "color": "#9333EA",
        "is_active": true,
        "display_order": 99,
        "created_at": "2025-11-19T11:00:00.000000Z",
        "updated_at": "2025-11-19T11:00:00.000000Z"
    }
}
```

### Error Response (422 Unprocessable Entity)

```json
{
    "success": false,
    "errors": {
        "zone_code": ["The zone code has already been taken."]
    }
}
```

---

## 5. ทดสอบแก้ไขโซน (Admin)

### Request

```http
PUT /home-visit/admin/zones/{id}
Content-Type: application/json
```

### cURL Command

```bash
curl -X PUT "http://localhost/home-visit/admin/zones/6" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Cookie: your-session-cookie" \
  -d '{
    "zone_name": "โซนทดสอบ (แก้ไข)",
    "zone_code": "TEST",
    "description": "โซนสำหรับทดสอบระบบ - อัพเดทแล้ว",
    "color": "#EC4899",
    "is_active": true,
    "display_order": 6
  }'
```

### Request Body

```json
{
    "zone_name": "โซนทดสอบ (แก้ไข)",
    "zone_code": "TEST",
    "description": "โซนสำหรับทดสอบระบบ - อัพเดทแล้ว",
    "color": "#EC4899",
    "is_active": true,
    "display_order": 6
}
```

### Expected Response (200 OK)

```json
{
    "success": true,
    "message": "อัพเดทโซนเรียบร้อยแล้ว",
    "zone": {
        "id": 6,
        "zone_name": "โซนทดสอบ (แก้ไข)",
        "zone_code": "TEST",
        "description": "โซนสำหรับทดสอบระบบ - อัพเดทแล้ว",
        "color": "#EC4899",
        "is_active": true,
        "display_order": 6,
        "created_at": "2025-11-19T11:00:00.000000Z",
        "updated_at": "2025-11-19T11:15:00.000000Z"
    }
}
```

---

## 6. ทดสอบเปิด/ปิดใช้งานโซน (Admin)

### Request

```http
PUT /home-visit/admin/zones/{id}/toggle-status
```

### cURL Command

```bash
curl -X PUT "http://localhost/home-visit/admin/zones/6/toggle-status" \
  -H "Accept: application/json" \
  -H "Cookie: your-session-cookie"
```

### Expected Response (200 OK)

```json
{
    "success": true,
    "message": "ปิดใช้งานโซนแล้ว",
    "zone": {
        "id": 6,
        "zone_name": "โซนทดสอบ (แก้ไข)",
        "zone_code": "TEST",
        "description": "โซนสำหรับทดสอบระบบ - อัพเดทแล้ว",
        "color": "#EC4899",
        "is_active": false,
        "display_order": 6,
        "created_at": "2025-11-19T11:00:00.000000Z",
        "updated_at": "2025-11-19T11:20:00.000000Z"
    }
}
```

---

## 7. ทดสอบเรียงลำดับโซน (Admin)

### Request

```http
POST /home-visit/admin/zones/reorder
Content-Type: application/json
```

### cURL Command

```bash
curl -X POST "http://localhost/home-visit/admin/zones/reorder" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Cookie: your-session-cookie" \
  -d '{
    "zones": [
      { "id": 1, "display_order": 5 },
      { "id": 2, "display_order": 4 },
      { "id": 3, "display_order": 3 },
      { "id": 4, "display_order": 2 },
      { "id": 5, "display_order": 1 }
    ]
  }'
```

### Request Body

```json
{
    "zones": [
        { "id": 1, "display_order": 5 },
        { "id": 2, "display_order": 4 },
        { "id": 3, "display_order": 3 },
        { "id": 4, "display_order": 2 },
        { "id": 5, "display_order": 1 }
    ]
}
```

### Expected Response (200 OK)

```json
{
    "success": true,
    "message": "จัดเรียงโซนเรียบร้อยแล้ว"
}
```

---

## 8. ทดสอบลบโซน (Admin)

### Request

```http
DELETE /home-visit/admin/zones/{id}
```

### cURL Command

```bash
curl -X DELETE "http://localhost/home-visit/admin/zones/6" \
  -H "Accept: application/json" \
  -H "Cookie: your-session-cookie"
```

### Expected Response (200 OK)

```json
{
    "success": true,
    "message": "ลบโซนเรียบร้อยแล้ว"
}
```

### Error Response - ไม่สามารถลบโซนที่มีการเยี่ยมบ้าน (400 Bad Request)

```json
{
    "success": false,
    "message": "ไม่สามารถลบโซนที่มีการเยี่ยมบ้านอยู่ได้ กรุณาย้ายหรือลบการเยี่ยมบ้านก่อน"
}
```

---

## 9. ทดสอบสร้างการเยี่ยมบ้านพร้อมโซน (Teacher)

### Request

```http
POST /home-visit/teacher/create-home-visit/{student_id}
Content-Type: multipart/form-data
```

### cURL Command

```bash
curl -X POST "http://localhost/home-visit/teacher/create-home-visit/123" \
  -H "Cookie: your-session-cookie" \
  -F "visit_date=2025-11-19" \
  -F "visit_time=14:30" \
  -F "zone_id=1" \
  -F "observations=สภาพแวดล้อมดี ครอบครัวเอาใจใส่" \
  -F "notes=ควรติดตามต่อเนื่อง" \
  -F "participants[0][name]=นายสมชาย ใจดี" \
  -F "participants[1][name]=นางสมหญิง ใจดี" \
  -F "images[]=@/path/to/image1.jpg" \
  -F "images[]=@/path/to/image2.jpg"
```

### Form Data

```
visit_date: 2025-11-19
visit_time: 14:30
zone_id: 1
observations: สภาพแวดล้อมดี ครอบครัวเอาใจใส่
notes: ควรติดตามต่อเนื่อง
participants[0][name]: นายสมชาย ใจดี
participants[1][name]: นางสมหญิง ใจดี
images[]: [file]
images[]: [file]
```

### Expected Response (Redirect with success message)

```
Redirect to previous page with session success message
```

---

## Authentication & Authorization

### สำหรับทดสอบ Admin API

```
ต้อง login เป็น Admin ก่อน:
POST /home-visit/admin-login

และใช้ session cookie ที่ได้
```

### สำหรับทดสอบ Teacher API

```
ต้อง login เป็น Teacher ก่อน:
POST /home-visit/teacher-login

และใช้ session cookie ที่ได้
```

---

## Validation Rules Summary

### Zone Creation/Update

| Field         | Type    | Required | Validation      |
| ------------- | ------- | -------- | --------------- |
| zone_name     | string  | ✓        | max:255         |
| zone_code     | string  | ✓        | max:255, unique |
| description   | text    | ✗        | -               |
| color         | string  | ✗        | max:50          |
| is_active     | boolean | ✗        | true/false      |
| display_order | integer | ✗        | min:0           |

### Home Visit with Zone

| Field        | Type    | Required | Validation                 |
| ------------ | ------- | -------- | -------------------------- |
| visit_date   | date    | ✓        | valid date                 |
| visit_time   | time    | ✗        | -                          |
| zone_id      | integer | ✗        | exists in home_visit_zones |
| observations | text    | ✗        | -                          |
| notes        | text    | ✗        | -                          |

---

## Common HTTP Status Codes

-   `200 OK`: Request successful
-   `201 Created`: Resource created successfully
-   `400 Bad Request`: Invalid request (e.g., cannot delete zone with visits)
-   `401 Unauthorized`: Not authenticated
-   `403 Forbidden`: Not authorized (wrong user type)
-   `404 Not Found`: Resource not found
-   `422 Unprocessable Entity`: Validation failed
-   `500 Internal Server Error`: Server error

---

## Testing Checklist

### Admin Zone Management

-   [ ] List zones with pagination
-   [ ] Search zones by name/code/description
-   [ ] Filter zones by status (active/inactive)
-   [ ] View single zone details
-   [ ] Create new zone
-   [ ] Update existing zone
-   [ ] Toggle zone active status
-   [ ] Reorder zones
-   [ ] Delete zone (without visits)
-   [ ] Try to delete zone with visits (should fail)
-   [ ] Create zone with duplicate code (should fail)

### Teacher Zone Usage

-   [ ] View active zones list
-   [ ] Create home visit with zone
-   [ ] Create home visit without zone
-   [ ] Update home visit with zone change
-   [ ] View home visit with zone information

---

## Database Verification

### Check created zones

```sql
SELECT * FROM home_visit_zones ORDER BY display_order;
```

### Check home visits with zones

```sql
SELECT
  hv.id,
  hv.student_id,
  hv.visit_date,
  z.zone_name,
  z.zone_code
FROM student_home_visits hv
LEFT JOIN home_visit_zones z ON hv.zone_id = z.id
ORDER BY hv.visit_date DESC;
```

### Count visits per zone

```sql
SELECT
  z.zone_name,
  z.zone_code,
  COUNT(hv.id) as visit_count
FROM home_visit_zones z
LEFT JOIN student_home_visits hv ON z.id = hv.zone_id
GROUP BY z.id
ORDER BY visit_count DESC;
```

---

**Note**: อย่าลืมแทนที่ `localhost` ด้วย domain ที่ใช้งานจริง และใส่ session cookie ที่ถูกต้องในการทดสอบ
