# Implementation Guide - AssignmentsScoresViewer

## Quick Start Guide

### 1. ติดตั้ง Component

Component อยู่ที่: `resources/js/PlearndComponents/learn/courses/progress/AssignmentsScoresViewer.vue`

### 2. สร้าง Route

```php
// routes/web.php หรือ routes/learn.php

use App\Http\Controllers\Learn\CourseScoresController;

Route::middleware(['auth'])->group(function () {
    // แสดงหน้าคะแนนของสมาชิก
    Route::get('/courses/{course}/members/{member}/scores',
        [CourseScoresController::class, 'show'])
        ->name('courses.members.scores');

    // API สำหรับปรับปรุงคะแนน
    Route::post('/courses/{course}/members/{member}/refresh-scores',
        [CourseScoresController::class, 'refreshScores'])
        ->name('courses.members.refresh-scores');
});
```

### 3. สร้าง Controller

```php
<?php

namespace App\Http\Controllers\Learn;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMember;
use App\Models\CourseAssignment;
use App\Models\CourseQuiz;
use App\Models\CourseQuizResult;
use App\Models\CourseQuizQuestion;
use App\Models\UserAnswerQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CourseScoresController extends Controller
{
    /**
     * แสดงหน้าคะแนนของสมาชิก
     */
    public function show(Course $course, CourseMember $member)
    {
        // ตรวจสอบสิทธิ์การเข้าถึง
        $this->authorize('view', [$member, $course]);

        // ดึงข้อมูลภาระงานพร้อมคำตอบของสมาชิก
        $assignments = CourseAssignment::where('course_id', $course->id)
            ->with(['answers' => function($query) use ($member) {
                $query->where('student_id', $member->user_id);
            }])
            ->orderBy('created_at')
            ->get();

        // ดึงข้อมูลแบบทดสอบพร้อมคะแนนของสมาชิก
        $quizzes = CourseQuiz::where('course_id', $course->id)
            ->with(['course_members_score' => function($query) use ($member) {
                $query->where('user_id', $member->user_id);
            }])
            ->orderBy('created_at')
            ->get();

        // จัดเตรียมข้อมูลสมาชิก
        $memberInfo = [
            'id' => $member->id,
            'user_id' => $member->user_id,
            'member_code' => $member->member_code,
            'member_name' => $member->user->name,
            'achieved_score' => $member->achieved_score,
            'bonus_points' => $member->bonus_points ?? 0
        ];

        return Inertia::render('Learn/Courses/MemberScores', [
            'member_info' => $memberInfo,
            'course' => [
                'id' => $course->id,
                'name' => $course->name,
                'total_score' => $course->total_score
            ],
            'assignments' => $assignments,
            'quizzes' => $quizzes,
            'refreshUrl' => route('courses.members.refresh-scores', [
                'course' => $course->id,
                'member' => $member->id
            ])
        ]);
    }

    /**
     * ปรับปรุงคะแนนของสมาชิก
     */
    public function refreshScores(Course $course, CourseMember $member)
    {
        // ตรวจสอบสิทธิ์
        $this->authorize('update', [$member, $course]);

        DB::beginTransaction();

        try {
            // ดึงแบบทดสอบทั้งหมดในรายวิชา
            $quizzes = CourseQuiz::where('course_id', $course->id)->get();

            foreach ($quizzes as $quiz) {
                // 1. ทำความสะอาดข้อมูลซ้ำ
                $this->cleanDuplicateAnswers($quiz->id, $member->user_id);

                // 2. คำนวณคะแนนใหม่
                $result = $this->calculateQuizScore($quiz->id, $member->user_id);

                // 3. บันทึกผลคะแนน
                CourseQuizResult::updateOrCreate(
                    [
                        'quiz_id' => $quiz->id,
                        'user_id' => $member->user_id
                    ],
                    [
                        'score' => $result['score'],
                        'total_questions' => $result['total'],
                        'efficiency' => $result['efficiency']
                    ]
                );
            }

            // 4. คำนวณคะแนนรวมทั้งหมด
            $totalQuizScore = CourseQuizResult::where('user_id', $member->user_id)
                ->whereHas('quiz', function($query) use ($course) {
                    $query->where('course_id', $course->id);
                })
                ->sum('score');

            // 5. อัปเดตคะแนนรวมในตาราง course_members
            $member->update([
                'achieved_score' => $totalQuizScore
            ]);

            DB::commit();

            return back()->with('success', 'ปรับปรุงคะแนนสำเร็จ! ข้อมูลคะแนนได้รับการตรวจสอบและอัปเดตแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error refreshing scores', [
                'course_id' => $course->id,
                'member_id' => $member->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'เกิดข้อผิดพลาดในการปรับปรุงคะแนน: ' . $e->getMessage());
        }
    }

    /**
     * ทำความสะอาดคำตอบซ้ำ
     */
    private function cleanDuplicateAnswers($quizId, $userId)
    {
        // ลบคำตอบที่ไม่ถูกต้องทั้งหมด
        UserAnswerQuestion::where('quiz_id', $quizId)
            ->where('user_id', $userId)
            ->where('is_correct', false)
            ->delete();

        // จัดการคำตอบที่ถูกต้องซ้ำ
        $questions = CourseQuizQuestion::where('quiz_id', $quizId)->get();

        foreach ($questions as $question) {
            // หาคำตอบที่ถูกต้องทั้งหมด (เรียงจากใหม่สุดไปเก่าสุด)
            $correctAnswers = UserAnswerQuestion::where('quiz_id', $quizId)
                ->where('user_id', $userId)
                ->where('question_id', $question->id)
                ->where('is_correct', true)
                ->orderBy('created_at', 'desc')
                ->get();

            // ถ้ามีมากกว่า 1 รายการ
            if ($correctAnswers->count() > 1) {
                // เก็บรายการแรก (ใหม่สุด) ไว้ ลบที่เหลือ
                $correctAnswers->skip(1)->each(function($answer) {
                    $answer->delete();
                });
            }
        }
    }

    /**
     * คำนวณคะแนนแบบทดสอบ
     */
    private function calculateQuizScore($quizId, $userId)
    {
        // นับจำนวนคำตอบที่ถูกต้อง (แต่ละข้อนับครั้งเดียว)
        $correctCount = UserAnswerQuestion::where('quiz_id', $quizId)
            ->where('user_id', $userId)
            ->where('is_correct', true)
            ->distinct('question_id')
            ->count('question_id');

        // จำนวนข้อทั้งหมด
        $totalQuestions = CourseQuizQuestion::where('quiz_id', $quizId)->count();

        // คำนวณประสิทธิภาพ
        $efficiency = ($totalQuestions > 0)
            ? round(($correctCount / $totalQuestions) * 100, 2)
            : 0;

        return [
            'score' => $correctCount,
            'total' => $totalQuestions,
            'efficiency' => $efficiency
        ];
    }
}
```

### 4. สร้าง Policy (Authorization)

```php
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseMember;
use Illuminate\Auth\Access\HandlesAuthorization;

class CourseMemberPolicy
{
    use HandlesAuthorization;

    /**
     * ตรวจสอบว่าสามารถดูคะแนนของสมาชิกได้หรือไม่
     */
    public function view(User $user, CourseMember $member, Course $course)
    {
        // เจ้าของรายวิชา (อาจารย์) ดูได้
        if ($course->user_id === $user->id) {
            return true;
        }

        // สมาชิกดูคะแนนของตัวเองได้
        if ($member->user_id === $user->id) {
            return true;
        }

        // Admin ดูได้
        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }

    /**
     * ตรวจสอบว่าสามารถปรับปรุงคะแนนได้หรือไม่
     */
    public function update(User $user, CourseMember $member, Course $course)
    {
        // เฉพาะเจ้าของรายวิชาหรือ Admin
        return $course->user_id === $user->id || $user->hasRole('admin');
    }
}
```

### 5. สร้าง Inertia Page

```vue
<!-- resources/js/Pages/Learn/Courses/MemberScores.vue -->
<template>
    <AuthenticatedLayout>
        <Head title="คะแนนสมาชิก" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Breadcrumb -->
                <nav class="mb-6 text-sm">
                    <ol class="flex items-center space-x-2">
                        <li>
                            <Link
                                :href="route('courses.index')"
                                class="text-blue-600 hover:text-blue-800"
                            >
                                รายวิชา
                            </Link>
                        </li>
                        <li class="text-gray-500">/</li>
                        <li>
                            <Link
                                :href="route('courses.show', course.id)"
                                class="text-blue-600 hover:text-blue-800"
                            >
                                {{ course.name }}
                            </Link>
                        </li>
                        <li class="text-gray-500">/</li>
                        <li class="text-gray-700">คะแนนสมาชิก</li>
                    </ol>
                </nav>

                <!-- Component -->
                <AssignmentsScoresViewer
                    :member_info="member_info"
                    :course="course"
                    :assignments="assignments"
                    :quizzes="quizzes"
                    :canRefresh="$page.props.auth.user.id === course.user_id"
                    :refreshUrl="refreshUrl"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AssignmentsScoresViewer from "@/PlearndComponents/learn/courses/progress/AssignmentsScoresViewer.vue";

defineProps({
    member_info: Object,
    course: Object,
    assignments: Array,
    quizzes: Array,
    refreshUrl: String,
});
</script>
```

### 6. Register Policy

```php
// app/Providers/AuthServiceProvider.php

namespace App\Providers;

use App\Models\CourseMember;
use App\Policies\CourseMemberPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        CourseMember::class => CourseMemberPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
```

### 7. เพิ่ม Models Relations (ถ้ายังไม่มี)

```php
// app/Models/CourseAssignment.php
class CourseAssignment extends Model
{
    public function answers()
    {
        return $this->hasMany(CourseAssignmentAnswer::class, 'assignment_id');
    }
}

// app/Models/CourseQuiz.php
class CourseQuiz extends Model
{
    public function course_members_score()
    {
        return $this->hasMany(CourseQuizResult::class, 'quiz_id');
    }

    public function questions()
    {
        return $this->hasMany(CourseQuizQuestion::class, 'quiz_id');
    }
}

// app/Models/CourseMember.php
class CourseMember extends Model
{
    protected $fillable = [
        'course_id',
        'user_id',
        'member_code',
        'achieved_score',
        'bonus_points'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
```

## Testing

### การทดสอบด้วย Browser

1. เข้าสู่ระบบด้วยบัญชีอาจารย์
2. เปิดรายวิชา
3. คลิกดูคะแนนของนักเรียน
4. ตรวจสอบว่าคะแนนแสดงถูกต้อง
5. คลิกปุ่ม "ปรับปรุงคะแนน"
6. ตรวจสอบว่าข้อมูลอัปเดตสำเร็จ

### การทดสอบด้วย PHPUnit

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseMember;
use App\Models\CourseQuiz;
use App\Models\UserAnswerQuestion;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseScoresTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_view_member_scores()
    {
        $teacher = User::factory()->create();
        $course = Course::factory()->create(['user_id' => $teacher->id]);
        $member = CourseMember::factory()->create(['course_id' => $course->id]);

        $response = $this->actingAs($teacher)
            ->get(route('courses.members.scores', [
                'course' => $course->id,
                'member' => $member->id
            ]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) =>
            $page->component('Learn/Courses/MemberScores')
                ->has('member_info')
                ->has('course')
        );
    }

    public function test_refresh_scores_removes_duplicate_answers()
    {
        $teacher = User::factory()->create();
        $course = Course::factory()->create(['user_id' => $teacher->id]);
        $member = CourseMember::factory()->create(['course_id' => $course->id]);
        $quiz = CourseQuiz::factory()->create(['course_id' => $course->id]);

        // สร้างคำตอบซ้ำ
        UserAnswerQuestion::factory()->count(2)->create([
            'quiz_id' => $quiz->id,
            'user_id' => $member->user_id,
            'question_id' => 1,
            'is_correct' => true
        ]);

        $response = $this->actingAs($teacher)
            ->post(route('courses.members.refresh-scores', [
                'course' => $course->id,
                'member' => $member->id
            ]));

        $response->assertRedirect();

        // ตรวจสอบว่าเหลือคำตอบเพียง 1 รายการ
        $this->assertEquals(1, UserAnswerQuestion::where('question_id', 1)->count());
    }
}
```

## Troubleshooting

### ปัญหา: คะแนนไม่แสดงผล

**แก้ไข:** ตรวจสอบว่า relations ใน Model ถูกต้อง และมีการ eager load ข้อมูลครบ

### ปัญหา: ปุ่มปรับปรุงคะแนนไม่ทำงาน

**แก้ไข:** ตรวจสอบ `refreshUrl` และ permissions ของผู้ใช้

### ปัญหา: ข้อมูลซ้ำยังไม่หาย

**แก้ไข:** ตรวจสอบ function `cleanDuplicateAnswers` และลอง debug ด้วย `dd()` หรือ `Log`

## สรุป

การใช้งาน Component นี้ต้องมี:

1. ✅ Route สำหรับแสดงผลและ refresh
2. ✅ Controller ที่ประมวลผลข้อมูล
3. ✅ Policy สำหรับตรวจสอบสิทธิ์
4. ✅ Inertia Page สำหรับแสดงผล
5. ✅ Models และ Relations ที่ถูกต้อง

ทำตามขั้นตอนข้างต้น Component จะทำงานได้อย่างสมบูรณ์!
