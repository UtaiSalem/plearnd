# สถาปัตยกรรมระบบแสดงผลคะแนนสมาชิก

## ภาพรวมของระบบ

ระบบแสดงผลคะแนนถูกออกแบบให้มีการประมวลผลข้อมูลที่ Controller ก่อนส่งมาแสดงผลที่ Frontend เพื่อรับประกันความถูกต้องและความสอดคล้องของข้อมูลคะแนน

## การไหลของข้อมูล (Data Flow)

```
┌──────────────────────────────────────────────────────────────────┐
│                      1. User Request                              │
│  ผู้ใช้เปิดหน้าแสดงผลคะแนน หรือคลิกปุ่ม "ปรับปรุงคะแนน"          │
└──────────────────────┬───────────────────────────────────────────┘
                       │
                       ▼
┌──────────────────────────────────────────────────────────────────┐
│                   2. Controller Processing                        │
│                                                                    │
│  ┌────────────────────────────────────────────────────────────┐  │
│  │ A. ดึงข้อมูลคำตอบจาก user_answer_questions                 │  │
│  │    - ดึงคำตอบของนักเรียนในแต่ละข้อของแบบทดสอบ             │  │
│  │    - ตรวจสอบว่ามีคำตอบซ้ำหรือไม่                           │  │
│  └────────────────────────────────────────────────────────────┘  │
│                       │                                            │
│                       ▼                                            │
│  ┌────────────────────────────────────────────────────────────┐  │
│  │ B. จัดการข้อมูลซ้ำ                                        │  │
│  │    ▸ ลบคำตอบที่ไม่ถูกต้องออก (is_correct = false)        │  │
│  │    ▸ หากมีคำตอบที่ถูกต้องซ้ำ                              │  │
│  │      → เลือกคำตอบที่ตอบหลังสุด (ORDER BY created_at DESC) │  │
│  │      → ลบคำตอบเก่าที่เหลือออก                             │  │
│  └────────────────────────────────────────────────────────────┘  │
│                       │                                            │
│                       ▼                                            │
│  ┌────────────────────────────────────────────────────────────┐  │
│  │ C. คำนวณคะแนน                                              │  │
│  │    - นับจำนวนคำตอบที่ถูกต้อง (DISTINCT question_id)       │  │
│  │    - คำนวณเปอร์เซ็นต์                                      │  │
│  │    - คำนวณประสิทธิภาพ (efficiency)                        │  │
│  └────────────────────────────────────────────────────────────┘  │
│                       │                                            │
│                       ▼                                            │
│  ┌────────────────────────────────────────────────────────────┐  │
│  │ D. บันทึกผลลงฐานข้อมูล                                     │  │
│  │    ▸ อัปเดตตาราง course_quiz_results                      │  │
│  │      (คะแนนแต่ละแบบทดสอบ)                                 │  │
│  │    ▸ อัปเดตตาราง course_members                           │  │
│  │      (คะแนนรวมทั้งหมด)                                     │  │
│  └────────────────────────────────────────────────────────────┘  │
│                       │                                            │
│                       ▼                                            │
│  ┌────────────────────────────────────────────────────────────┐  │
│  │ E. เตรียมข้อมูลส่งกลับ                                     │  │
│  │    - member_info (ข้อมูลสมาชิก)                           │  │
│  │    - assignments (ภาระงานพร้อมคะแนน)                      │  │
│  │    - quizzes (แบบทดสอบพร้อมคะแนน)                        │  │
│  │    - course (ข้อมูลรายวิชา)                               │  │
│  └────────────────────────────────────────────────────────────┘  │
│                                                                    │
└──────────────────────┬───────────────────────────────────────────┘
                       │
                       ▼
┌──────────────────────────────────────────────────────────────────┐
│                3. Frontend Rendering (Vue Component)              │
│                                                                    │
│  ┌────────────────────────────────────────────────────────────┐  │
│  │ AssignmentsScoresViewer Component                          │  │
│  │                                                             │  │
│  │  ┌──────────────────────────────────────────────────────┐  │  │
│  │  │ Computed Properties ประมวลผลข้อมูล                  │  │  │
│  │  │  • assignmentsData - จัดรูปแบบข้อมูลภาระงาน         │  │  │
│  │  │  • quizzesData - จัดรูปแบบข้อมูลแบบทดสอบ           │  │  │
│  │  │  • totalScore - คำนวณคะแนนรวม                      │  │  │
│  │  │  • overallPercentage - คำนวณเปอร์เซ็นต์รวม         │  │  │
│  │  └──────────────────────────────────────────────────────┘  │  │
│  │                       │                                      │  │
│  │                       ▼                                      │  │
│  │  ┌──────────────────────────────────────────────────────┐  │  │
│  │  │ Template แสดงผล                                     │  │  │
│  │  │  ▸ ส่วนหัว - ข้อมูลสมาชิกและปุ่มปรับปรุงคะแนน      │  │  │
│  │  │  ▸ คะแนนภาระงาน - แสดงเป็น Card พร้อม Progress Bar │  │  │
│  │  │  ▸ คะแนนแบบทดสอบ - แสดงเป็น Card พร้อมประสิทธิภาพ  │  │  │
│  │  │  ▸ สรุปคะแนนรวม - แสดงแบบ Dashboard                │  │  │
│  │  └──────────────────────────────────────────────────────┘  │  │
│  └─────────────────────────────────────────────────────────────┘  │
│                                                                    │
└────────────────────────────────────────────────────────────────────┘
```

## โครงสร้างฐานข้อมูลที่เกี่ยวข้อง

### 1. ตาราง `user_answer_questions`

เก็บคำตอบของนักเรียนในแต่ละข้อของแบบทดสอบ

```sql
CREATE TABLE user_answer_questions (
    id BIGINT PRIMARY KEY,
    user_id BIGINT,              -- รหัสนักเรียน
    quiz_id BIGINT,              -- รหัสแบบทดสอบ
    question_id BIGINT,          -- รหัสคำถาม
    answer_id BIGINT,            -- รหัสคำตอบที่เลือก
    is_correct BOOLEAN,          -- ถูกหรือผิด
    created_at TIMESTAMP,        -- เวลาที่ตอบ
    updated_at TIMESTAMP
);
```

**ปัญหาที่พบ:** มีการบันทึกคำตอบซ้ำของคำถามเดียวกัน

### 2. ตาราง `course_quiz_results`

เก็บผลคะแนนรวมของแต่ละแบบทดสอบ

```sql
CREATE TABLE course_quiz_results (
    id BIGINT PRIMARY KEY,
    quiz_id BIGINT,              -- รหัสแบบทดสอบ
    user_id BIGINT,              -- รหัสนักเรียน
    score INT,                   -- คะแนนที่ได้
    total_questions INT,         -- จำนวนข้อทั้งหมด
    efficiency DECIMAL(5,2),     -- ประสิทธิภาพ (%)
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY (quiz_id, user_id)
);
```

### 3. ตาราง `course_members`

เก็บข้อมูลสมาชิกและคะแนนรวม

```sql
CREATE TABLE course_members (
    id BIGINT PRIMARY KEY,
    course_id BIGINT,            -- รหัสรายวิชา
    user_id BIGINT,              -- รหัสนักเรียน
    member_code VARCHAR(50),     -- รหัสนักเรียน
    achieved_score DECIMAL(8,2), -- คะแนนรวมที่ได้
    bonus_points INT,            -- คะแนนโบนัส
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY (course_id, user_id)
);
```

## วิธีการจัดการข้อมูลซ้ำ

### Algorithm สำหรับทำความสะอาดข้อมูล

```php
function cleanDuplicateAnswers($quizId, $userId) {
    // ขั้นตอนที่ 1: ลบคำตอบที่ไม่ถูกต้องทั้งหมด
    UserAnswerQuestion::where('quiz_id', $quizId)
        ->where('user_id', $userId)
        ->where('is_correct', false)
        ->delete();

    // ขั้นตอนที่ 2: จัดการคำตอบที่ถูกต้องซ้ำ
    $questions = CourseQuizQuestion::where('quiz_id', $quizId)->get();

    foreach ($questions as $question) {
        // หาคำตอบที่ถูกต้องทั้งหมด เรียงจากใหม่ไปเก่า
        $correctAnswers = UserAnswerQuestion::where('quiz_id', $quizId)
            ->where('user_id', $userId)
            ->where('question_id', $question->id)
            ->where('is_correct', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // ถ้ามีมากกว่า 1 รายการ
        if ($correctAnswers->count() > 1) {
            // เก็บรายการแรก (ล่าสุด) ไว้ ลบที่เหลือ
            $correctAnswers->skip(1)->each(function($answer) {
                $answer->delete();
            });
        }
    }
}
```

### การคำนวณคะแนน

```php
function calculateQuizScore($quizId, $userId) {
    // นับจำนวนคำตอบที่ถูกต้อง (แต่ละข้อนับครั้งเดียว)
    $correctCount = UserAnswerQuestion::where('quiz_id', $quizId)
        ->where('user_id', $userId)
        ->where('is_correct', true)
        ->distinct('question_id')
        ->count('question_id');

    // จำนวนข้อทั้งหมด
    $totalQuestions = CourseQuizQuestion::where('quiz_id', $quizId)->count();

    // คำนวณเปอร์เซ็นต์
    $efficiency = ($totalQuestions > 0)
        ? ($correctCount / $totalQuestions) * 100
        : 0;

    return [
        'score' => $correctCount,
        'total' => $totalQuestions,
        'efficiency' => $efficiency
    ];
}
```

## การทำงานของปุ่ม "ปรับปรุงคะแนน"

### 1. User Action

ผู้ใช้คลิกปุ่ม "ปรับปรุงคะแนน"

### 2. Frontend

```javascript
const refreshScores = () => {
    isRefreshing.value = true;

    router.visit(props.refreshUrl, {
        method: "post",
        preserveScroll: true,
        onSuccess: () => {
            successMessage.value = "ปรับปรุงคะแนนสำเร็จ!";
        },
        onError: (errors) => {
            errorMessage.value = "เกิดข้อผิดพลาด";
        },
        onFinish: () => {
            isRefreshing.value = false;
        },
    });
};
```

### 3. Backend (Controller)

```php
public function refreshScores($courseId, $memberId) {
    DB::beginTransaction();

    try {
        $member = CourseMember::findOrFail($memberId);
        $quizzes = CourseQuiz::where('course_id', $courseId)->get();

        foreach ($quizzes as $quiz) {
            // ทำความสะอาดข้อมูลซ้ำ
            $this->cleanDuplicateAnswers($quiz->id, $member->user_id);

            // คำนวณคะแนนใหม่
            $result = $this->calculateQuizScore($quiz->id, $member->user_id);

            // บันทึกลงฐานข้อมูล
            CourseQuizResult::updateOrCreate(
                ['quiz_id' => $quiz->id, 'user_id' => $member->user_id],
                [
                    'score' => $result['score'],
                    'total_questions' => $result['total'],
                    'efficiency' => $result['efficiency']
                ]
            );
        }

        // อัปเดตคะแนนรวม
        $totalScore = CourseQuizResult::where('user_id', $member->user_id)
            ->whereHas('quiz', function($q) use ($courseId) {
                $q->where('course_id', $courseId);
            })
            ->sum('score');

        $member->update(['achieved_score' => $totalScore]);

        DB::commit();
        return back()->with('success', 'ปรับปรุงคะแนนสำเร็จ');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'เกิดข้อผิดพลาด');
    }
}
```

## ข้อดีของการออกแบบนี้

### 1. ความถูกต้องของข้อมูล (Data Integrity)

-   ✅ ตรวจสอบและแก้ไขข้อมูลซ้ำทุกครั้งที่เปิดหน้า
-   ✅ คะแนนที่แสดงเป็นข้อมูลล่าสุดเสมอ
-   ✅ ป้องกันปัญหาข้อมูลไม่สอดคล้องกัน

### 2. ประสิทธิภาพ (Performance)

-   ✅ ประมวลผลที่ Backend ก่อนส่งมา Frontend
-   ✅ Frontend แค่แสดงผล ไม่ต้องคำนวณหนัก
-   ✅ ลด API calls โดยส่งข้อมูลครบมาครั้งเดียว

### 3. ความยืดหยุ่น (Flexibility)

-   ✅ สามารถเพิ่มเกณฑ์การคำนวณคะแนนใหม่ได้
-   ✅ แก้ไข Algorithm ที่ Backend โดยไม่กระทบ Frontend
-   ✅ รองรับการปรับปรุงระบบในอนาคต

### 4. การบำรุงรักษา (Maintainability)

-   ✅ แยก Logic การประมวลผลออกจาก UI ชัดเจน
-   ✅ ง่ายต่อการ Debug และแก้ไข
-   ✅ Code มีโครงสร้างที่เข้าใจง่าย

## สถานการณ์การใช้งาน

### สถานการณ์ที่ 1: นักเรียนตอบแบบทดสอบ

1. นักเรียนทำแบบทดสอบเสร็จ
2. ระบบบันทึกคำตอบลงฐานข้อมูล
3. อาจารย์เปิดหน้าดูคะแนนนักเรียน
4. Controller ตรวจสอบและคำนวณคะแนนอัตโนมัติ
5. แสดงผลคะแนนที่ถูกต้อง

### สถานการณ์ที่ 2: พบข้อมูลซ้ำ

1. พบว่ามีคำตอบซ้ำในฐานข้อมูล
2. อาจารย์คลิกปุ่ม "ปรับปรุงคะแนน"
3. ระบบลบข้อมูลซ้ำและคำนวณใหม่
4. แสดงผลคะแนนที่ถูกต้อง
5. แจ้งเตือนว่าปรับปรุงสำเร็จ

### สถานการณ์ที่ 3: อาจารย์ดูภาพรวมห้องเรียน

1. อาจารย์ต้องการดูคะแนนของนักเรียนทั้งห้อง
2. เปิดหน้ารายชื่อนักเรียน
3. คลิกดูคะแนนของนักเรียนแต่ละคน
4. Component แสดงผลคะแนนแยกตามประเภท
5. เห็นภาพรวมและรายละเอียดชัดเจน

## สรุป

ระบบนี้ออกแบบมาเพื่อแก้ปัญหาข้อมูลคะแนนที่ไม่สอดคล้องกันในฐานข้อมูล โดย:

1. **ประมวลผลที่ Controller** - ตรวจสอบและแก้ไขข้อมูลก่อนส่งมาแสดงผล
2. **จัดการข้อมูลซ้ำ** - ลบคำตอบที่ไม่จำเป็นและเลือกคำตอบล่าสุด
3. **แสดงผลที่ Frontend** - Component รับข้อมูลที่ถูกต้องมาแสดงผลอย่างสวยงาม
4. **ปรับปรุงอัตโนมัติ** - มีปุ่มสำหรับตรวจสอบและแก้ไขข้อมูลได้ทุกเมื่อ

การออกแบบนี้ทำให้มั่นใจได้ว่าคะแนนที่แสดงเป็นข้อมูลที่ถูกต้องและเป็นปัจจุบันเสมอ
