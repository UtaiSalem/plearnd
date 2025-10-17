# Home Visit Participants Design

## การออกแบบระบบผู้เข้าร่วมการเยี่ยมบ้าน

### ปัญหา
การเยี่ยมบ้านนักเรียนแต่ละครั้งอาจมีครูหรือบุคลากรมากกว่า 1 คนเข้าร่วม

### โครงสร้างฐานข้อมูล

#### ตาราง `home_visit_participants`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| home_visit_id | bigint | FK → student_home_visits |
| user_id | bigint | FK → users (nullable) |
| participant_name | varchar | ชื่อผู้เข้าร่วม |
| participant_position | varchar | ตำแหน่ง |
| participant_type | enum | teacher, admin, counselor, external |
| role | enum | primary, observer, assistant |
| notes | text | บันทึกเพิ่มเติมของผู้เข้าร่วม |

#### ประเภทผู้เข้าร่วม (participant_type)
- **teacher** - ครูผู้สอน
- **admin** - ผู้บริหาร
- **counselor** - ครูแนะแนว
- **external** - บุคคลภายนอก (เช่น นักสังคมสงเคราะห์)

#### บทบาท (role)
- **primary** - ครูหลัก/ผู้รับผิดชอบหลัก (1 คนต่อการเยี่ยม)
- **observer** - ผู้สังเกตการณ์ (สามารถมีหลายคน)
- **assistant** - ผู้ช่วย (สามารถมีหลายคน)

### Relationships

```php
// StudentHomeVisit Model
public function participants() // All participants
public function primaryParticipant() // Main teacher
public function observers() // Observers only
public function getParticipantNamesAttribute() // Get all names as string

// HomeVisitParticipant Model
public function homeVisit()
public function user()
public function isPrimary()
```

### การใช้งาน

#### 1. สร้างการเยี่ยมบ้านพร้อมผู้เข้าร่วม

```php
$homeVisit = StudentHomeVisit::createFromStudent($student, [
    'visit_date' => '2025-10-17',
    'notes' => 'เยี่ยมบ้านนักเรียน',
]);

// เพิ่มครูหลัก
HomeVisitParticipant::create([
    'home_visit_id' => $homeVisit->id,
    'user_id' => $teacher->id,
    'participant_name' => $teacher->name,
    'participant_position' => 'ครูประจำชั้น',
    'participant_type' => 'teacher',
    'role' => 'primary',
]);

// เพิ่มผู้สังเกตการณ์
HomeVisitParticipant::create([
    'home_visit_id' => $homeVisit->id,
    'participant_name' => 'ครูแนะแนว สมชาย',
    'participant_type' => 'counselor',
    'role' => 'observer',
]);
```

#### 2. ดึงข้อมูลผู้เข้าร่วม

```php
// Get all participants
$participants = $homeVisit->participants;

// Get primary teacher
$primary = $homeVisit->primaryParticipant;

// Get all names as string
$names = $homeVisit->participant_names; // "ครูสมชาย, ครูแนะแนว"
```

#### 3. Query home visits โดยผู้เข้าร่วม

```php
// หาการเยี่ยมบ้านที่ครูคนนี้เข้าร่วม
$visits = StudentHomeVisit::whereHas('participants', function($query) use ($teacherId) {
    $query->where('user_id', $teacherId);
})->get();

// หาการเยี่ยมบ้านที่เป็นครูหลัก
$primaryVisits = StudentHomeVisit::whereHas('participants', function($query) use ($teacherId) {
    $query->where('user_id', $teacherId)->where('role', 'primary');
})->get();
```

### ข้อดีของการออกแบบนี้

1. ✅ รองรับหลายคนต่อการเยี่ยมบ้าน
2. ✅ แยกบทบาทได้ชัดเจน (หลัก, สังเกตการณ์, ผู้ช่วย)
3. ✅ เก็บบันทึกของแต่ละคนได้แยกกัน
4. ✅ Link กับ User ได้ (ถ้าเป็นผู้ใช้ในระบบ)
5. ✅ รองรับบุคคลภายนอกที่ไม่มี account
6. ✅ Query ง่าย หาได้ว่าครูคนไหนเคยเยี่ยมบ้านใคร

### Migration Commands

```bash
php artisan migrate  # จะสร้างตาราง home_visit_participants
```
