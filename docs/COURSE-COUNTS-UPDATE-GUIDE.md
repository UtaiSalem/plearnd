# Course Counts Update Guide

This guide explains how to update the `courses` table columns `groups`, `quizzes`, and `enrolled_students` by counting related data from other tables.

## Problem
The `courses` table has columns that should contain counts of related records:
- `groups`: Number of groups in the course (from `course_groups` table)
- `quizzes`: Number of quizzes in the course (from `course_quizzes` table)
- `enrolled_students`: Number of active students enrolled in the course (from `course_members` table)

These columns may have incorrect data and need to be updated.

## Solutions Provided

### 1. SQL Script
**File**: `database/scripts/update_course_counts.sql`

A direct SQL script that can be executed in your database client to update all counts.

```sql
-- Run this script directly in your database
UPDATE courses c
SET c.groups = (
    SELECT COUNT(*) FROM course_groups cg WHERE cg.course_id = c.id
);

UPDATE courses c
SET c.quizzes = (
    SELECT COUNT(*) FROM course_quizzes cq WHERE cq.course_id = c.id
);

UPDATE courses c
SET c.enrolled_students = (
    SELECT COUNT(*) 
    FROM course_members cm 
    WHERE cm.course_id = c.id
    AND cm.course_member_status = 1
    AND (cm.role = 1 OR cm.role = 2)
);
```

### 2. Laravel Migration
**File**: `database/migrations/2025_11_06_071300_update_course_counts_from_related_tables.php`

Run this migration to update all counts immediately:

```bash
php artisan migrate
```

### 3. Artisan Command
**File**: `app/Console/Commands/UpdateCourseCounts.php`

A reusable command that can be run anytime to update counts:

```bash
# Basic update
php artisan courses:update-counts

# Update with verification (shows before/after comparison)
php artisan courses:update-counts --verify
```

### 4. Model Observers (Automatic Maintenance)
**Files**: 
- `app/Observers/CourseGroupObserver.php`
- `app/Observers/CourseQuizObserver.php`
- `app/Observers/CourseMemberObserver.php`

These observers automatically maintain the counts when related records are:
- Created (increments count)
- Deleted (decrements count)
- Updated (checks if status/role changed for CourseMember)

The observers are registered in `app/Providers/AppServiceProvider.php`.

## How to Use

### One-time Fix
If you just need to fix the current data:

1. **Option A**: Run the migration
   ```bash
   php artisan migrate
   ```

2. **Option B**: Run the Artisan command
   ```bash
   php artisan courses:update-counts --verify
   ```

3. **Option C**: Execute the SQL script directly in your database client

### Ongoing Maintenance
The model observers will automatically keep counts accurate when:
- New groups/quizzes/members are added
- Existing records are deleted
- Member status or role changes

## Important Notes

### Enrolled Students Count
The `enrolled_students` count only includes:
- Members with `course_member_status = 1` (active)
- Members with `role = 1` (student) or `role = 2` (student leader)

This excludes teachers, admins, and inactive members.

### Verification
Use the Artisan command with `--verify` flag to see a comparison between the stored counts and the actual counts:

```bash
php artisan courses:update-counts --verify
```

This will show a table with:
- Current stored counts
- Actual counts from related tables
- Easy comparison to verify accuracy

## Troubleshooting

### Counts Still Incorrect After Update
1. Check if there are any soft-deleted records affecting counts
2. Verify the criteria for counting (especially for enrolled_students)
3. Run the verification command to see the actual vs stored counts

### Observers Not Working
1. Ensure the observers are registered in `AppServiceProvider`
2. Check that your models are using the `SoftDeletes` trait if applicable
3. Verify the observer methods are being triggered (add logging if needed)

### Performance Considerations
For very large databases:
- Consider running updates during off-peak hours
- The migration might take time on databases with many records
- The observers add minimal overhead but ensure real-time accuracy