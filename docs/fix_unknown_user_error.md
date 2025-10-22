# แก้ไขปัญหา "Unknown User" ใน NewsfeedV2

## ปัญหา

เมื่อดึงข้อมูล activities จากฐานข้อมูลครั้งแรก พบว่ามีบางรายการที่แสดงผลว่า "Unknown User" แทนที่จะแสดงชื่อผู้ใช้จริง

## สาเหตุ

ปัญหานี้เกิดจาก:

1. **ข้อมูล user ถูกลบไปแล้ว** - มี activities ที่อ้างอิงถึง user_id ที่ถูกลบไปแล้ว
2. **การดึงข้อมูลไม่ครบถ้วน** - การดึงข้อมูล user ไม่ได้รวมถึง user ที่ถูกลบไปแล้ว
3. **การจัดการข้อมูล null ไม่ถูกต้อง** - ไม่มีการตรวจสอบว่าข้อมูล user เป็น null หรือไม่

## วิธีแก้ไข

ฉันได้แก้ไขปัญหานี้โดยการปรับปรุง [`ActivityResource.php`](app/Http/Resources/ActivityResource.php:29):

### ก่อนแก้ไข:
```php
'action_by' => $this->whenLoaded('user', fn() => new UserResource($this->user)),
```

### หลังแก้ไข:
```php
'action_by' => $this->whenLoaded('user', fn() => $this->user ? new UserResource($this->user) : [
    'id' => 0,
    'name' => 'Unknown User',
    'avatar' => '/storage/images/plearnd-logo.png'
]),
```

## การแก้ไขเพิ่มเติม

หากยังคงมีปัญหานี้หลังจากแก้ไขข้างต้นแล้ว สามารถลองวิธีเหล่านี้:

### 1. ล้าง cache
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

### 2. ตรวจสอบข้อมูลในฐานข้อมูล
```sql
-- ตรวจสอบ activities ที่ไม่มี user ที่เกี่ยวข้อง
SELECT a.id, a.user_id, a.activity_type FROM activities a 
LEFT JOIN users u ON a.user_id = u.id 
WHERE u.id IS NULL;

-- ตรวจสอบว่ามี activities ที่อ้างอิงถึง user ที่ถูกลบไปแล้วหรือไม่
SELECT COUNT(*) FROM activities WHERE user_id NOT IN (SELECT id FROM users);
```

### 3. แก้ไขข้อมูลในฐานข้อมูล (ถ้าจำเป็น)
```sql
-- สร้าง user พิเศษสำหรับ activities ที่ไม่มี user
INSERT INTO users (id, name, email, created_at, updated_at) 
VALUES (0, 'Unknown User', 'unknown@example.com', NOW(), NOW());

-- อัปเดต activities ที่ไม่มี user ให้ชี้ไปที่ user พิเศษ
UPDATE activities SET user_id = 0 WHERE user_id NOT IN (SELECT id FROM users WHERE id > 0);
```

### 4. ปรับปรุงการดึงข้อมูลใน Controller
หากต้องการให้แน่ใจว่าไม่มีปัญหานี้อีก สามารถปรับปรุงการดึงข้อมูลใน [`NewsfeedController.php`](app/Http/Controllers/Play/NewsfeedController.php:147):

```php
// แก้ไขจาก
$query = Activity::with(['user', 'activityable'])

// เป็น
$query = Activity::with(['user', 'activityable'])
    ->leftJoin('users', 'activities.user_id', '=', 'users.id')
    ->select('activities.*', 'users.name as user_name', 'users.profile_photo_url as user_avatar')
```

## การตรวจสอบผลลัพธ์

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

## การป้องกันปัญหาในอนาคต

เพื่อป้องกันปัญหานี้ในอนาคต:

1. **เพิ่ม foreign key constraint** ในตาราง activities:
   ```sql
   ALTER TABLE activities ADD CONSTRAINT fk_activities_user_id 
   FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;
   ```

2. **ใช้ Soft Deletes** สำหรับ user model เพื่อไม่ให้ข้อมูลถูกลบจริง

3. **เพิ่มการตรวจสอบ** ใน Controller ก่อนสร้าง activities:
   ```php
   if (!auth()->check()) {
       return redirect()->route('login');
   }
   ```

## ข้อมูลเพิ่มเติม

### การแสดงผลใน Vue Component

หากต้องการเพิ่มการตรวจสอบใน Vue component สามารถใช้:

```html
<div v-if="activity.action_by && activity.action_by.name">
    {{ activity.action_by.name }}
</div>
<div v-else>
    Unknown User
</div>