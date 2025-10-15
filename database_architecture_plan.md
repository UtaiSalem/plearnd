# แผนการจัดองค์ประกอบฐานข้อมูลสำหรับระบบข้อมูลนักเรียน
# Database Architecture Plan for Student Information System

## 📊 วิเคราะห์ปัญหาปัจจุบัน (Current Issues Analysis)

### ปัญหาหลักของ student_info table:
1. **Single Monolithic Table**: รวมข้อมูลทุกอย่างไว้ในตารางเดียว (70+ columns)
2. **Data Redundancy**: ข้อมูลซ้ำซ้อน เช่น ข้อมูลที่อยู่, ข้อมูลผู้ปกครอง
3. **Poor Normalization**: ไม่ได้ปฏิบัติตาม Database Normalization principles
4. **Scalability Issues**: ยากต่อการขยายระบบและเพิ่มฟีเจอร์ใหม่
5. **Performance Problems**: Query ช้าเนื่องจากตารางใหญ่
6. **Data Integrity**: ยากต่อการควบคุมความถูกต้องของข้อมูล

## 🎯 เป้าหมายการปรับปรุง (Improvement Goals)

1. **Normalization**: แยกข้อมูลตาม Entity ที่เกี่ยวข้อง
2. **Performance**: เพิ่มความเร็วในการ Query และ Update
3. **Scalability**: สามารถขยายระบบได้ง่าย
4. **Data Integrity**: ควบคุมความถูกต้องของข้อมูลได้ดีกว่า
5. **Maintainability**: ง่ายต่อการดูแลรักษา

## 🏗️ โครงสร้างฐานข้อมูลที่เสนอ (Proposed Database Structure)

### 1. **students** (Core Student Table) - Primary Entity
```sql
CREATE TABLE students (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    student_id VARCHAR(20) UNIQUE NOT NULL COMMENT 'รหัสนักเรียน',
    citizen_id VARCHAR(13) UNIQUE COMMENT 'เลขประจำตัวประชาชน',
    
    -- ชื่อภาษาไทย
    title_prefix_th VARCHAR(20) COMMENT 'คำนำหน้าชื่อ',
    first_name_th VARCHAR(100) NOT NULL COMMENT 'ชื่อ',
    last_name_th VARCHAR(100) NOT NULL COMMENT 'นามสกุล',
    middle_name_th VARCHAR(100) COMMENT 'ชื่อกลาง',
    
    -- ชื่อภาษาอังกฤษ
    title_prefix_en VARCHAR(20) COMMENT 'Title (EN)',
    first_name_en VARCHAR(100) COMMENT 'First Name (EN)',
    last_name_en VARCHAR(100) COMMENT 'Last Name (EN)',
    middle_name_en VARCHAR(100) COMMENT 'Middle Name (EN)',
    
    -- ข้อมูลพื้นฐาน
    date_of_birth DATE COMMENT 'วันเดือนปีเกิด',
    gender ENUM('male', 'female', 'other') COMMENT 'เพศ',
    nationality VARCHAR(50) DEFAULT 'ไทย' COMMENT 'สัญชาติ',
    religion VARCHAR(50) COMMENT 'ศาสนา',
    
    -- สถานะ
    status ENUM('active', 'inactive', 'graduated', 'transferred') DEFAULT 'active',
    enrollment_date DATE COMMENT 'วันที่เข้าเรียน',
    
    -- Metadata
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_student_id (student_id),
    INDEX idx_citizen_id (citizen_id),
    INDEX idx_name_th (first_name_th, last_name_th),
    INDEX idx_status (status)
);
```

### 2. **student_academic_info** - ข้อมูลการศึกษา
```sql
CREATE TABLE student_academic_info (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    student_id BIGINT UNSIGNED NOT NULL,
    
    current_grade VARCHAR(10) COMMENT 'ชั้นปีปัจจุบัน',
    current_class VARCHAR(10) COMMENT 'ห้องเรียนปัจจุบัน',
    classroom_full VARCHAR(20) COMMENT 'ชั้น/ห้อง เต็ม',
    
    -- โรงเรียนเดิม
    previous_school_name VARCHAR(200) COMMENT 'ชื่อโรงเรียนเดิม',
    previous_school_province VARCHAR(100) COMMENT 'จังหวัดโรงเรียนเดิม',
    previous_grade_level VARCHAR(20) COMMENT 'ชั้นเรียนเดิม',
    
    -- ความพิการ/ความต้องการพิเศษ
    disability_type VARCHAR(100) COMMENT 'ประเภทความพิการ',
    special_needs TEXT COMMENT 'ความต้องการพิเศษ',
    
    academic_year VARCHAR(10) COMMENT 'ปีการศึกษา',
    semester TINYINT COMMENT 'ภาคเรียน',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    INDEX idx_academic_year (academic_year),
    INDEX idx_grade_class (current_grade, current_class)
);
```

### 3. **student_addresses** - ที่อยู่ (ปรับปรุงจากที่มีอยู่)
```sql
CREATE TABLE student_addresses (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    student_id BIGINT UNSIGNED NOT NULL,
    address_type ENUM('current', 'permanent', 'contact') DEFAULT 'current',
    
    house_number VARCHAR(50) COMMENT 'บ้านเลขที่',
    village_number VARCHAR(10) COMMENT 'หมู่',
    village_name VARCHAR(100) COMMENT 'ชื่อหมู่บ้าน',
    alley VARCHAR(100) COMMENT 'ซอย',
    road VARCHAR(100) COMMENT 'ถนน',
    subdistrict VARCHAR(100) COMMENT 'ตำบล/แขวง',
    district VARCHAR(100) COMMENT 'อำเภอ/เขต',
    province VARCHAR(100) COMMENT 'จังหวัด',
    postal_code VARCHAR(10) COMMENT 'รหัสไปรษณีย์',
    country VARCHAR(50) DEFAULT 'ประเทศไทย',
    
    is_primary BOOLEAN DEFAULT TRUE COMMENT 'ที่อยู่หลัก',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    INDEX idx_address_type (address_type),
    INDEX idx_location (province, district)
);
```

### 4. **student_contacts** - ข้อมูลการติดต่อ
```sql
CREATE TABLE student_contacts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    student_id BIGINT UNSIGNED NOT NULL,
    contact_type ENUM('phone', 'mobile', 'email', 'line', 'facebook') NOT NULL,
    contact_value VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    is_verified BOOLEAN DEFAULT FALSE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    INDEX idx_contact_type (contact_type),
    INDEX idx_primary (is_primary)
);
```

### 5. **student_guardians** - ข้อมูลผู้ปกครอง
```sql
CREATE TABLE student_guardians (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    student_id BIGINT UNSIGNED NOT NULL,
    guardian_type ENUM('father', 'mother', 'guardian', 'other') NOT NULL,
    
    citizen_id VARCHAR(13) COMMENT 'เลขประจำตัวประชาชน',
    title_prefix VARCHAR(20) COMMENT 'คำนำหน้าชื่อ',
    first_name VARCHAR(100) NOT NULL COMMENT 'ชื่อ',
    last_name VARCHAR(100) NOT NULL COMMENT 'นามสกุล',
    
    occupation VARCHAR(100) COMMENT 'อาชีพ',
    workplace VARCHAR(200) COMMENT 'สถานที่ทำงาน',
    monthly_income DECIMAL(10,2) COMMENT 'รายได้ต่อเดือน',
    
    relationship VARCHAR(50) COMMENT 'ความสัมพันธ์',
    status ENUM('alive', 'deceased', 'unknown') DEFAULT 'alive',
    nationality VARCHAR(50) DEFAULT 'ไทย',
    
    is_primary_contact BOOLEAN DEFAULT FALSE,
    is_emergency_contact BOOLEAN DEFAULT FALSE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    INDEX idx_guardian_type (guardian_type),
    INDEX idx_primary_contact (is_primary_contact)
);
```

### 6. **guardian_contacts** - ข้อมูลการติดต่อผู้ปกครอง
```sql
CREATE TABLE guardian_contacts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    guardian_id BIGINT UNSIGNED NOT NULL,
    contact_type ENUM('phone', 'mobile', 'email', 'line', 'facebook') NOT NULL,
    contact_value VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    is_verified BOOLEAN DEFAULT FALSE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (guardian_id) REFERENCES student_guardians(id) ON DELETE CASCADE,
    INDEX idx_contact_type (contact_type)
);
```

### 7. **student_health_info** - ข้อมูลสุขภาพ
```sql
CREATE TABLE student_health_info (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    student_id BIGINT UNSIGNED NOT NULL,
    
    height_cm DECIMAL(5,2) COMMENT 'ส่วนสูง (ซม.)',
    weight_kg DECIMAL(5,2) COMMENT 'น้ำหนัก (กก.)',
    blood_type ENUM('A', 'B', 'AB', 'O') COMMENT 'หมู่เลือด',
    rh_factor ENUM('+', '-') COMMENT 'RH',
    
    allergies TEXT COMMENT 'อาการแพ้',
    chronic_diseases TEXT COMMENT 'โรคประจำตัว',
    medications TEXT COMMENT 'ยาที่ต้องรับประทานสม่ำเสมอ',
    
    last_checkup_date DATE COMMENT 'วันที่ตรวจสุขภาพล่าสุด',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY unique_student_health (student_id)
);
```

### 8. **student_documents** - เอกสารประกอบ
```sql
CREATE TABLE student_documents (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    student_id BIGINT UNSIGNED NOT NULL,
    document_type ENUM('profile_image', 'birth_certificate', 'house_registration', 'parent_id', 'transcript', 'medical_certificate', 'other') NOT NULL,
    
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT UNSIGNED,
    mime_type VARCHAR(100),
    
    description TEXT COMMENT 'คำอธิบาย',
    uploaded_by BIGINT UNSIGNED COMMENT 'ผู้อัปโหลด',
    is_verified BOOLEAN DEFAULT FALSE,
    verified_by BIGINT UNSIGNED COMMENT 'ผู้ตรวจสอบ',
    verified_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    INDEX idx_document_type (document_type),
    INDEX idx_verification (is_verified)
);
```

## 🔗 ความสัมพันธ์ระหว่างตาราง (Table Relationships)

```
students (1) ←→ (1) student_academic_info
students (1) ←→ (N) student_addresses
students (1) ←→ (N) student_contacts
students (1) ←→ (N) student_guardians
students (1) ←→ (1) student_health_info
students (1) ←→ (N) student_documents

student_guardians (1) ←→ (N) guardian_contacts
```

## 📈 ข้อดีของโครงสร้างใหม่ (Benefits)

### 1. **Performance Improvements**
- แยกข้อมูลตาม Entity ทำให้ Query เฉพาะส่วนที่ต้องการ
- Index ที่เหมาะสมสำหรับแต่ละตาราง
- Reduced I/O operations

### 2. **Scalability**
- ง่ายต่อการเพิ่มฟีเจอร์ใหม่
- สามารถ Partition ตารางได้
- รองรับ Microservices Architecture

### 3. **Data Integrity**
- Foreign Key Constraints
- Proper Data Types และ Validations
- Normalized Structure

### 4. **Flexibility**
- รองรับข้อมูลหลายที่อยู่ต่อนักเรียน
- รองรับข้อมูลการติดต่อหลายช่องทาง
- รองรับผู้ปกครองหลายคน

### 5. **Maintainability**
- แยกความรับผิดชอบของแต่ละตาราง
- ง่ายต่อการ Debug และ Optimize
- Clear Business Logic

## 🚀 แผนการ Migration (Migration Plan)

### Phase 1: เตรียมโครงสร้างใหม่
1. สร้างตารางใหม่ทั้งหมด
2. ตั้งค่า Indexes และ Constraints
3. สร้าง Migration Scripts

### Phase 2: ย้ายข้อมูล
1. ย้ายข้อมูลหลักจาก student_info → students
2. แยกข้อมูลการศึกษา → student_academic_info
3. ย้ายข้อมูลที่อยู่ → student_addresses (ปรับปรุงจากที่มีอยู่)
4. แยกข้อมูลผู้ปกครอง → student_guardians + guardian_contacts
5. แยกข้อมูลสุขภาพ → student_health_info

### Phase 3: อัปเดต Application Code
1. สร้าง Models ใหม่ทั้งหมด
2. อัปเดต Controllers และ Services
3. ปรับ API Endpoints
4. อัปเดต Frontend Components

### Phase 4: Testing & Optimization
1. Performance Testing
2. Data Integrity Verification
3. Load Testing
4. Query Optimization

## 📋 Model Classes Structure (Laravel)

```php
// app/Models/Student.php
class Student extends Model
{
    public function academicInfo() { return $this->hasOne(StudentAcademicInfo::class); }
    public function addresses() { return $this->hasMany(StudentAddress::class); }
    public function contacts() { return $this->hasMany(StudentContact::class); }
    public function guardians() { return $this->hasMany(StudentGuardian::class); }
    public function healthInfo() { return $this->hasOne(StudentHealthInfo::class); }
    public function documents() { return $this->hasMany(StudentDocument::class); }
    
    public function primaryAddress() { return $this->hasOne(StudentAddress::class)->where('is_primary', true); }
    public function primaryContact() { return $this->hasOne(StudentContact::class)->where('is_primary', true); }
}

// app/Models/StudentGuardian.php
class StudentGuardian extends Model
{
    public function student() { return $this->belongsTo(Student::class); }
    public function contacts() { return $this->hasMany(GuardianContact::class); }
    
    public function primaryContact() { return $this->hasOne(GuardianContact::class)->where('is_primary', true); }
}
```

## 🔍 Query Examples

```php
// ดึงข้อมูลนักเรียนพร้อมที่อยู่และผู้ปกครอง
$student = Student::with([
    'primaryAddress',
    'guardians.primaryContact',
    'academicInfo'
])->find($id);

// ค้นหานักเรียนตามชื่อ
$students = Student::where('first_name_th', 'LIKE', "%{$name}%")
    ->orWhere('last_name_th', 'LIKE', "%{$name}%")
    ->with('academicInfo')
    ->paginate(20);

// ดึงข้อมูลนักเรียนในห้องเรียน
$classStudents = Student::whereHas('academicInfo', function($q) use ($grade, $room) {
    $q->where('current_grade', $grade)
      ->where('current_class', $room);
})->get();
```

## 💾 Storage Considerations

### 1. **File Storage Strategy**
- รูปประจำตัว: `/storage/students/photos/{student_id}/`
- เอกสารประกอบ: `/storage/students/documents/{student_id}/`
- Backup Strategy สำหรับไฟล์สำคัญ

### 2. **Database Optimization**
- ใช้ Read Replicas สำหรับ Query ที่ไม่ต้อง Real-time
- Implement Caching สำหรับข้อมูลที่เข้าถึงบ่อย
- Database Connection Pooling

## 🔒 Security & Privacy

### 1. **Data Protection**
- Encrypt sensitive fields (citizen_id, phone numbers)
- Access Control ตาม Role-Based Permissions
- Audit Log สำหรับการเปลี่ยนแปลงข้อมูลสำคัญ

### 2. **Compliance**
- PDPA Compliance สำหรับข้อมูลส่วนบุคคล
- Data Retention Policies
- Right to be Forgotten Implementation

## 📊 Performance Monitoring

### 1. **Key Metrics**
- Query Response Time
- Database Connection Usage
- Storage Usage Growth
- API Response Time

### 2. **Optimization Strategies**
- Regular Index Analysis
- Query Performance Monitoring
- Database Statistics Updates
- Connection Pool Tuning

---

## 🎯 สรุป (Summary)

การปรับโครงสร้างฐานข้อมูลตามแผนนี้จะช่วยให้:

1. **ประสิทธิภาพดีขึ้น** - Query เร็วขึ้น, ใช้ Resource น้อยลง
2. **ขยายตัวได้ง่าย** - เพิ่มฟีเจอร์ใหม่ได้ง่าย
3. **ดูแลรักษาง่าย** - โครงสร้างชัดเจน, แยกหน้าที่การทำงาน
4. **ความปลอดภัยสูง** - ควบคุมการเข้าถึงข้อมูลได้ดีกว่า
5. **รองรับอนาคต** - พร้อมสำหรับการพัฒนาต่อยอด

การ Migration ควรทำทีละขั้นตอน พร้อม Testing อย่างครอบคลุมในแต่ละ Phase