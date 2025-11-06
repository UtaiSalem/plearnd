-- Script to update course enrollment counts based on actual course_members data
-- This script fixes the discrepancy between courses.enrolled_students and actual member counts

-- First, let's see the current state (for verification)
SELECT 
    c.id,
    c.name,
    c.enrolled_students as current_enrollment,
    COUNT(cm.id) as actual_members,
    (COUNT(cm.id) - c.enrolled_students) as discrepancy
FROM courses c
LEFT JOIN course_members cm ON c.id = cm.course_id
GROUP BY c.id, c.name, c.enrolled_students
HAVING COUNT(cm.id) != c.enrolled_students
ORDER BY c.id;

-- Now update the enrollment counts
UPDATE courses c
SET enrolled_students = (
    SELECT COUNT(*)
    FROM course_members cm
    WHERE cm.course_id = c.id
);

-- Verify the updates
SELECT 
    c.id,
    c.name,
    c.enrolled_students as updated_enrollment,
    COUNT(cm.id) as actual_members,
    (COUNT(cm.id) - c.enrolled_students) as discrepancy_after_update
FROM courses c
LEFT JOIN course_members cm ON c.id = cm.course_id
GROUP BY c.id, c.name, c.enrolled_students
HAVING COUNT(cm.id) != c.enrolled_students
ORDER BY c.id;

-- Summary report
SELECT 
    'Summary' as report_type,
    COUNT(*) as total_courses,
    SUM(CASE WHEN c.enrolled_students = (SELECT COUNT(*) FROM course_members cm WHERE cm.course_id = c.id) THEN 1 ELSE 0 END) as courses_with_correct_enrollment,
    SUM(CASE WHEN c.enrolled_students != (SELECT COUNT(*) FROM course_members cm WHERE cm.course_id = c.id) THEN 1 ELSE 0 END) as courses_with_incorrect_enrollment
FROM courses c;