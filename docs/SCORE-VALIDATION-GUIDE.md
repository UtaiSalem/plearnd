# คู่มือการตรวจสอบคะแนน (Score Validation)

## ภาพรวม

ระบบตรวจสอบความถูกต้องของคะแนนที่บันทึกในระบบ เพื่อป้องกันกรณีที่คะแนนเกิน 100% หรือมีค่าผิดปกติ

---

## Backend (PHP Laravel)

### API Endpoint

```
POST /courses/{course}/members/{member}/process-grades
```

### Controller: `CourseMemberGradeProgressController`

#### ฟังก์ชันตรวจสอบคะแนน

```php
private function validateScore($achievedScore, $totalScore, $memberId, $courseId)
{
    // ตรวจสอบ:
    // 1. ค่าติดลบ -> severity: 'critical'
    // 2. คะแนนเกิน > 150% -> severity: 'critical'
    // 3. คะแนนเกิน 100-150% -> severity: 'error'
    // 4. ปกติ -> severity: 'none'
}
```

### Response Format

#### กรณีปกติ (คะแนนถูกต้อง)

```json
{
    "success": true,
    "message": "Grades processed successfully",
    "total_score": 85,
    "course_total_score": 100,
    "quizzes_processed": 5,
    "validation": {
        "is_valid": true,
        "error_message": "",
        "has_error": false,
        "severity": "none",
        "percentage": 85.0
    }
}
```

#### กรณีพบข้อผิดพลาด (คะแนนเกิน 100%)

```json
{
    "success": true,
    "warning": true,
    "message": "Grades processed with validation warnings",
    "total_score": 120,
    "course_total_score": 100,
    "quizzes_processed": 5,
    "validation": {
        "is_valid": false,
        "error_message": "คะแนนที่บันทึก (120) สูงกว่าคะแนนรวมสูงสุด (100) คิดเป็น 120%",
        "has_error": true,
        "severity": "error",
        "percentage": 120.0
    }
}
```

#### กรณีร้ายแรง (คะแนนเกิน 150% หรือติดลบ)

```json
{
    "success": true,
    "warning": true,
    "message": "Grades processed with validation warnings",
    "total_score": 180,
    "course_total_score": 100,
    "quizzes_processed": 5,
    "validation": {
        "is_valid": false,
        "error_message": "คะแนนที่บันทึก (180) สูงกว่าคะแนนรวมสูงสุด (100) คิดเป็น 180%",
        "has_error": true,
        "severity": "critical",
        "percentage": 180.0
    }
}
```

---

## Frontend (Vue.js)

### Composable: `useMemberProgress`

#### การใช้งาน

```javascript
import { useMemberProgress } from "@/composables/useMemberProgress";

const { percentage, progressStatus, scoreValidation, validateScore } =
    useMemberProgress(member, totalScore);

// ตรวจสอบสถานะ
if (scoreValidation.value.hasError) {
    console.error(scoreValidation.value.errorMessage);
    console.log("Severity:", scoreValidation.value.severity);
}
```

#### Validation Object Structure

```javascript
{
  isValid: boolean,
  errorMessage: string,
  hasError: boolean,
  severity: 'none' | 'warning' | 'error' | 'critical'
}
```

#### ระดับความรุนแรง (Severity Levels)

| Level      | เงื่อนไข               | การแสดงผล                     |
| ---------- | ---------------------- | ----------------------------- |
| `none`     | คะแนนปกติ (0-100%)     | ไม่แสดง error                 |
| `warning`  | -                      | แจ้งเตือนเบาๆ                 |
| `error`    | คะแนน 100-150%         | แสดง error แดง                |
| `critical` | คะแนน > 150% หรือติดลบ | แสดง error แดงสด + ไอคอนเตือน |

---

## ตัวอย่างการใช้งานใน Component

### การเรียก API และตรวจสอบผล

```javascript
async function processGrades(courseId, memberId) {
    try {
        const response = await axios.post(
            `/courses/${courseId}/members/${memberId}/process-grades`
        );

        const { validation, warning } = response.data;

        // ตรวจสอบผลการ validate
        if (warning && validation.has_error) {
            // แสดง notification หรือ modal แจ้งเตือน
            showValidationWarning(validation);
        } else {
            // ประมวลผลสำเร็จ
            showSuccessMessage();
        }

        // Refresh ข้อมูลคะแนน
        await fetchMemberProgress();
    } catch (error) {
        console.error("Error processing grades:", error);
    }
}

function showValidationWarning(validation) {
    const severityClass = {
        error: "bg-red-100 border-red-500 text-red-900",
        critical: "bg-red-200 border-red-700 text-red-950 font-bold",
    };

    // แสดง Toast หรือ Alert
    toast.error(validation.error_message, {
        class: severityClass[validation.severity],
        duration: 10000, // แสดงนานกว่าปกติ
    });
}
```

### การแสดงผล Error ใน Template

```vue
<template>
    <div class="member-progress">
        <!-- Progress Bar -->
        <div class="progress-bar">
            <div
                class="progress-fill"
                :style="progressBarStyle"
                :class="{ 'bg-red-500': scoreValidation.hasError }"
            ></div>
        </div>

        <!-- Error Alert -->
        <div
            v-if="scoreValidation.hasError"
            :class="[
                'alert',
                scoreValidation.severity === 'critical'
                    ? 'alert-critical'
                    : 'alert-error',
            ]"
        >
            <Icon :name="getErrorIcon(scoreValidation.severity)" />
            <span>{{ scoreValidation.errorMessage }}</span>
        </div>

        <!-- คะแนน -->
        <div class="score-display">
            <span>{{ member.achieved_score }} / {{ totalScore }}</span>
            <span>({{ percentage }}%)</span>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { useMemberProgress } from "@/composables/useMemberProgress";

const props = defineProps({
    member: Object,
    totalScore: Number,
});

const { percentage, progressBarStyle, scoreValidation } = useMemberProgress(
    computed(() => props.member),
    computed(() => props.totalScore)
);

function getErrorIcon(severity) {
    return severity === "critical"
        ? "heroicons:exclamation-triangle-solid"
        : "heroicons:exclamation-circle-solid";
}
</script>
```

---

## Log Files

### Laravel Log

```
storage/logs/laravel.log
```

### ตัวอย่าง Log Entry

```
[2025-11-05 10:30:45] local.ERROR: Score Validation Error (Score Exceeds Total) {
  "member_id": 123,
  "course_id": 456,
  "achieved_score": 120,
  "total_score": 100,
  "percentage": 120,
  "exceed_by": 20,
  "severity": "error",
  "timestamp": "2025-11-05 10:30:45"
}
```

---

## การทดสอบ

### Test Case 1: คะแนนปกติ

-   Input: `achieved_score = 85`, `total_score = 100`
-   Expected: `percentage = 85%`, `severity = none`

### Test Case 2: คะแนนเกิน 100%

-   Input: `achieved_score = 120`, `total_score = 100`
-   Expected: `percentage = 120%`, `severity = error`, `has_error = true`

### Test Case 3: คะแนนเกิน 150%

-   Input: `achieved_score = 180`, `total_score = 100`
-   Expected: `percentage = 180%`, `severity = critical`, `has_error = true`

### Test Case 4: คะแนนติดลบ

-   Input: `achieved_score = -10`, `total_score = 100`
-   Expected: `severity = critical`, `has_error = true`

---

## สรุป

✅ **Backend**: ตรวจสอบคะแนนก่อนบันทึก และส่ง validation result กลับ  
✅ **Frontend**: รับ validation result และแสดงผลเตือนผู้ใช้  
✅ **Logging**: บันทึก error log เพื่อติดตามและแก้ไขปัญหา  
✅ **Prevention**: ป้องกันการแสดงผลคะแนนที่ผิดพลาด (แสดง 0% แทน)
