# AssignmentsScoresViewer Component - คู่มือการใช้งาน

## ภาพรวม

Component `AssignmentsScoresViewer` ถูกออกแบบมาเพื่อแสดงผลคะแนนของสมาชิกจากการทำภาระงานและแบบทดสอบในแต่ละรายวิชา โดยมีการประมวลผลข้อมูลที่ Controller ก่อนส่งมาแสดงผล

## สถาปัตยกรรมของระบบ

### 1. การประมวลผลข้อมูลใน Controller

Controller มีหน้าที่สำคัญในการตรวจสอบและปรับปรุงความถูกต้องของคะแนนก่อนส่งข้อมูลมายัง Frontend:

#### ขั้นตอนการทำงานของ Controller:

1. **ดึงข้อมูลคำตอบจากฐานข้อมูล**

    - ดึงข้อมูลจากตาราง `user_answer_questions`
    - ตรวจสอบคำตอบของสมาชิกในแต่ละข้อของแบบทดสอบ

2. **จัดการข้อมูลคำตอบซ้ำ**

    - ตรวจหาคำตอบที่ซ้ำกันของคำถามเดียวกัน
    - ลบคำตอบที่ไม่ถูกต้อง (incorrect answers) ออก
    - หากมีคำตอบที่ถูกต้องหลายรายการ เลือกคำตอบที่ตอบล่าสุด (latest correct answer)

3. **คำนวณคะแนนแบบทดสอบ**

    - นับจำนวนคำตอบที่ถูกต้องทั้งหมด
    - บันทึกหรืออัปเดตผลคะแนนในตาราง `course_quiz_results`

4. **อัปเดตคะแนนรวมของสมาชิก**

    - รวมคะแนนจากแบบทดสอบทุกครั้ง
    - อัปเดตข้อมูลในตาราง `course_members` ให้เป็นปัจจุบัน

5. **เตรียมข้อมูลส่งกลับ Frontend**
    - คะแนนภาระงานแต่ละครั้ง
    - คะแนนแบบทดสอบแต่ละครั้ง
    - คะแนนรวมทั้งหมด
    - คะแนนโบนัส (ถ้ามี)

### 2. โครงสร้างข้อมูลที่ส่งมาจาก Controller

```php
// ตัวอย่างข้อมูลที่ Controller ส่งมา
[
    'member_info' => [
        'id' => 1,
        'user_id' => 123,
        'member_code' => '6401001',
        'member_name' => 'สมชาย ใจดี',
        'achieved_score' => 75,
        'bonus_points' => 5
    ],
    'course' => [
        'id' => 1,
        'name' => 'วิชาคอมพิวเตอร์เบื้องต้น',
        'total_score' => 100
    ],
    'assignments' => [
        [
            'id' => 1,
            'title' => 'ภาระงานที่ 1',
            'points' => 20,
            'answers' => [
                [
                    'id' => 1,
                    'student' => ['id' => 123],
                    'points' => 18
                ]
            ]
        ]
    ],
    'quizzes' => [
        [
            'id' => 1,
            'title' => 'แบบทดสอบที่ 1',
            'total_score' => 30,
            'course_members_score' => [
                [
                    'user_id' => 123,
                    'score' => 25,
                    'efficiency' => 83.3
                ]
            ]
        ]
    ]
]
```

## คุณสมบัติของ Component

### Props

| Prop          | Type    | Required | Default | Description                                        |
| ------------- | ------- | -------- | ------- | -------------------------------------------------- |
| `member_info` | Object  | Yes      | -       | ข้อมูลสมาชิก (รหัส, ชื่อ, คะแนนที่ได้, คะแนนโบนัส) |
| `course`      | Object  | Yes      | -       | ข้อมูลรายวิชา (ชื่อวิชา, คะแนนเต็ม)                |
| `assignments` | Array   | No       | []      | รายการภาระงานและคะแนน                              |
| `quizzes`     | Array   | No       | []      | รายการแบบทดสอบและคะแนน                             |
| `canRefresh`  | Boolean | No       | true    | อนุญาตให้ปรับปรุงคะแนนได้หรือไม่                   |
| `refreshUrl`  | String  | No       | null    | URL สำหรับเรียก API ปรับปรุงคะแนน                  |

### คุณสมบัติหลัก

1. **แสดงคะแนนภาระงาน**

    - แสดงคะแนนแต่ละภาระงาน
    - Progress bar แสดงความคืบหน้า
    - สีแสดงระดับคะแนน (เขียว/เหลือง/ส้ม/แดง)
    - สรุปคะแนนรวมภาระงาน

2. **แสดงคะแนนแบบทดสอบ**

    - แสดงคะแนนแต่ละแบบทดสอบ
    - แสดงประสิทธิภาพ (efficiency)
    - Progress bar แสดงความคืบหน้า
    - สรุปคะแนนรวมแบบทดสอบ

3. **สรุปคะแนนรวม**

    - คะแนนภาระงานรวม
    - คะแนนแบบทดสอบรวม
    - คะแนนโบนัส
    - คะแนนรวมทั้งหมด
    - เปอร์เซ็นต์ความคืบหน้ารวม

4. **ปุ่มปรับปรุงคะแนน**
    - ตรวจสอบและแก้ไขข้อมูลคะแนนโดยอัตโนมัติ
    - แสดงสถานะการโหลด
    - แจ้งเตือนผลสำเร็จ/ข้อผิดพลาด

## การใช้งาน

### ตัวอย่างการใช้งานใน Vue Component

```vue
<template>
    <div>
        <AssignmentsScoresViewer
            :member_info="memberData"
            :course="courseData"
            :assignments="assignmentsData"
            :quizzes="quizzesData"
            :canRefresh="true"
            :refreshUrl="'/courses/' + courseId + '/refresh-scores'"
        />
    </div>
</template>

<script setup>
import { ref } from "vue";
import AssignmentsScoresViewer from "@/PlearndComponents/learn/courses/progress/AssignmentsScoresViewer.vue";

const memberData = ref({
    id: 1,
    user_id: 123,
    member_code: "6401001",
    member_name: "สมชาย ใจดี",
    achieved_score: 75,
    bonus_points: 5,
});

const courseData = ref({
    id: 1,
    name: "วิชาคอมพิวเตอร์เบื้องต้น",
    total_score: 100,
});

const assignmentsData = ref([
    // ข้อมูลภาระงาน
]);

const quizzesData = ref([
    // ข้อมูลแบบทดสอบ
]);
</script>
```

### ตัวอย่าง Controller (Laravel)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseScoresController extends Controller
{
    public function showMemberScores($courseId, $memberId)
    {
        // ดึงข้อมูลสมาชิก
        $member = CourseMember::with('user')->findOrFail($memberId);

        // ดึงข้อมูลรายวิชา
        $course = Course::findOrFail($courseId);

        // ดึงภาระงานพร้อมคำตอบ
        $assignments = CourseAssignment::where('course_id', $courseId)
            ->with(['answers' => function($query) use ($member) {
                $query->where('student_id', $member->user_id);
            }])
            ->get();

        // ดึงแบบทดสอบพร้อมคะแนน
        $quizzes = CourseQuiz::where('course_id', $courseId)
            ->with(['course_members_score' => function($query) use ($member) {
                $query->where('user_id', $member->user_id);
            }])
            ->get();

        // จัดเตรียมข้อมูลสมาชิก
        $memberInfo = [
            'id' => $member->id,
            'user_id' => $member->user_id,
            'member_code' => $member->member_code,
            'member_name' => $member->user->name,
            'achieved_score' => $member->achieved_score,
            'bonus_points' => $member->bonus_points
        ];

        return Inertia::render('Courses/MemberScores', [
            'member_info' => $memberInfo,
            'course' => $course,
            'assignments' => $assignments,
            'quizzes' => $quizzes
        ]);
    }

    public function refreshScores(Request $request, $courseId, $memberId)
    {
        try {
            $member = CourseMember::findOrFail($memberId);

            // 1. ดึงคำตอบจากแบบทดสอบทั้งหมด
            $quizzes = CourseQuiz::where('course_id', $courseId)->get();

            foreach ($quizzes as $quiz) {
                // 2. ตรวจสอบและลบคำตอบซ้ำ
                $this->cleanDuplicateAnswers($quiz->id, $member->user_id);

                // 3. คำนวณคะแนนใหม่
                $correctAnswers = $this->countCorrectAnswers($quiz->id, $member->user_id);

                // 4. อัปเดตตาราง course_quiz_results
                CourseQuizResult::updateOrCreate(
                    [
                        'quiz_id' => $quiz->id,
                        'user_id' => $member->user_id
                    ],
                    [
                        'score' => $correctAnswers,
                        'total_questions' => $quiz->questions()->count()
                    ]
                );
            }

            // 5. คำนวณและอัปเดตคะแนนรวมใน course_members
            $totalQuizScore = CourseQuizResult::where('user_id', $member->user_id)
                ->whereHas('quiz', function($query) use ($courseId) {
                    $query->where('course_id', $courseId);
                })
                ->sum('score');

            $member->update([
                'achieved_score' => $totalQuizScore
            ]);

            return back()->with('success', 'ปรับปรุงคะแนนสำเร็จ');

        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    private function cleanDuplicateAnswers($quizId, $userId)
    {
        // ลบคำตอบที่ไม่ถูกต้อง
        UserAnswerQuestion::where('quiz_id', $quizId)
            ->where('user_id', $userId)
            ->where('is_correct', false)
            ->delete();

        // จัดการคำตอบที่ถูกต้องซ้ำ - เก็บเฉพาะล่าสุด
        $questions = CourseQuizQuestion::where('quiz_id', $quizId)->get();

        foreach ($questions as $question) {
            $correctAnswers = UserAnswerQuestion::where('quiz_id', $quizId)
                ->where('user_id', $userId)
                ->where('question_id', $question->id)
                ->where('is_correct', true)
                ->orderBy('created_at', 'desc')
                ->get();

            if ($correctAnswers->count() > 1) {
                // ลบคำตอบเก่าออก เหลือแค่ล่าสุด
                $correctAnswers->skip(1)->each(function($answer) {
                    $answer->delete();
                });
            }
        }
    }

    private function countCorrectAnswers($quizId, $userId)
    {
        return UserAnswerQuestion::where('quiz_id', $quizId)
            ->where('user_id', $userId)
            ->where('is_correct', true)
            ->distinct('question_id')
            ->count('question_id');
    }
}
```

## ระบบการแสดงผลคะแนน

### เกณฑ์การแสดงสี

Component ใช้เกณฑ์ต่อไปนี้ในการแสดงสีตามเปอร์เซ็นต์คะแนน:

| เปอร์เซ็นต์ | สี     | ความหมาย     |
| ----------- | ------ | ------------ |
| 80% ขึ้นไป  | เขียว  | ดีมาก        |
| 60-79%      | เหลือง | ดี           |
| 40-59%      | ส้ม    | ปานกลาง      |
| ต่ำกว่า 40% | แดง    | ต้องปรับปรุง |

### Progress Bar

-   แสดงความคืบหน้าเป็นเปอร์เซ็นต์
-   มี animation การเคลื่อนไหว
-   สีเปลี่ยนตามเกณฑ์คะแนน
-   Responsive สำหรับทุกขนาดหน้าจอ

## การจัดการข้อผิดพลาด

Component มีระบบจัดการข้อผิดพลาดที่ครอบคลุม:

1. **แสดงข้อความแจ้งเตือน**

    - ข้อความสำเร็จ (สีเขียว)
    - ข้อความข้อผิดพลาด (สีแดง)

2. **ป้องกันการคลิกซ้ำ**

    - ปุ่มปรับปรุงคะแนนจะถูก disable ขณะกำลังประมวลผล

3. **แสดงสถานะการโหลด**
    - Spinner แสดงขณะกำลังประมวลผล
    - ข้อความเปลี่ยนตามสถานะ

## Best Practices

### 1. การจัดการข้อมูล

-   ตรวจสอบข้อมูลใน Controller ก่อนส่งมา Frontend
-   ใช้ Eager Loading เพื่อลด N+1 Query Problem
-   แคชข้อมูลที่ไม่เปลี่ยนแปลงบ่อย

### 2. Performance

-   ใช้ `computed` properties สำหรับการคำนวณ
-   หลีกเลี่ยงการคำนวณซ้ำ
-   Lazy load รูปภาพและข้อมูลขนาดใหญ่

### 3. User Experience

-   แสดง Loading state ขณะกำลังโหลดข้อมูล
-   ให้ Feedback ทันทีเมื่อมีการดำเนินการ
-   ใช้สีและ icon ที่สื่อความหมายชัดเจน

## การทดสอบ

### Unit Testing

```javascript
import { mount } from "@vue/test-utils";
import AssignmentsScoresViewer from "@/PlearndComponents/learn/courses/progress/AssignmentsScoresViewer.vue";

describe("AssignmentsScoresViewer", () => {
    it("displays member information correctly", () => {
        const wrapper = mount(AssignmentsScoresViewer, {
            props: {
                member_info: {
                    member_code: "6401001",
                    member_name: "สมชาย ใจดี",
                    achieved_score: 75,
                    bonus_points: 5,
                },
                course: {
                    name: "วิชาคอมพิวเตอร์เบื้องต้น",
                    total_score: 100,
                },
                assignments: [],
                quizzes: [],
            },
        });

        expect(wrapper.text()).toContain("6401001");
        expect(wrapper.text()).toContain("สมชาย ใจดี");
    });

    it("calculates total scores correctly", () => {
        const wrapper = mount(AssignmentsScoresViewer, {
            props: {
                member_info: {
                    user_id: 123,
                    bonus_points: 5,
                },
                course: {},
                assignments: [
                    {
                        id: 1,
                        title: "Test",
                        points: 20,
                        answers: [{ student: { id: 123 }, points: 15 }],
                    },
                ],
                quizzes: [],
            },
        });

        // Test total calculation
        expect(wrapper.vm.totalScore).toBe(20); // 15 + 5 bonus
    });
});
```

## สรุป

Component `AssignmentsScoresViewer` ทำงานร่วมกับ Controller เพื่อ:

1. ✅ แสดงผลคะแนนอย่างถูกต้องและเป็นปัจจุบัน
2. ✅ จัดการข้อมูลคำตอบซ้ำโดยอัตโนมัติ
3. ✅ ให้ข้อมูลภาพรวมที่ชัดเจนและครอบคลุม
4. ✅ รองรับการปรับปรุงคะแนนด้วยปุ่มกด
5. ✅ แสดงผลแบบ Responsive และใช้งานง่าย

การออกแบบนี้ช่วยให้ระบบมีความน่าเชื่อถือสูง และป้องกันปัญหาข้อมูลไม่สอดคล้องกันในฐานข้อมูล
