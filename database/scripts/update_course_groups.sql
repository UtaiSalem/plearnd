-- Script to update course group counts based on actual course_groups data
-- This script fixes the discrepancy between courses.groups and actual group counts

-- First, let's see the current state (for verification)
SELECT 
    c.id,
    c.name,
    c.groups as current_groups,
    COUNT(cg.id) as actual_groups,
    (COUNT(cg.id) - c.groups) as discrepancy
FROM courses c
LEFT JOIN course_groups cg ON c.id = cg.course_id
GROUP BY c.id, c.name, c.groups
HAVING COUNT(cg.id) != c.groups
ORDER BY c.id;

-- Now update the group counts
UPDATE courses c
SET groups = (
    SELECT COUNT(*)
    FROM course_groups cg
    WHERE cg.course_id = c.id
);

-- Verify the updates
SELECT 
    c.id,
    c.name,
    c.groups as updated_groups,
    COUNT(cg.id) as actual_groups,
    (COUNT(cg.id) - c.groups) as discrepancy_after_update
FROM courses c
LEFT JOIN course_groups cg ON c.id = cg.course_id
GROUP BY c.id, c.name, c.groups
HAVING COUNT(cg.id) != c.groups
ORDER BY c.id;

-- Summary report
SELECT 
    'Summary' as report_type,
    COUNT(*) as total_courses,
    SUM(CASE WHEN c.groups = (SELECT COUNT(*) FROM course_groups cg WHERE cg.course_id = c.id) THEN 1 ELSE 0 END) as courses_with_correct_groups,
    SUM(CASE WHEN c.groups != (SELECT COUNT(*) FROM course_groups cg WHERE cg.course_id = c.id) THEN 1 ELSE 0 END) as courses_with_incorrect_groups
FROM courses c;