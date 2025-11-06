-- Update courses table with counts from related tables
-- This script updates the groups, quizzes, and enrolled_students columns

-- Update groups count from course_groups table
UPDATE courses c
SET c.groups = (
    SELECT COUNT(*)
    FROM course_groups cg
    WHERE cg.course_id = c.id
);

-- Update quizzes count from course_quizzes table
UPDATE courses c
SET c.quizzes = (
    SELECT COUNT(*)
    FROM course_quizzes cq
    WHERE cq.course_id = c.id
);

-- Update enrolled_students count from course_members table (count only active students)
UPDATE courses c
SET c.enrolled_students = (
    SELECT COUNT(*)
    FROM course_members cm
    WHERE cm.course_id = c.id
    AND cm.course_member_status = 1  -- Only count active members
    AND (cm.role = 1 OR cm.role = 2)  -- Only count students and student leaders
);

-- Optional: If you want to count all members regardless of status or role, use this instead:
-- UPDATE courses c
-- SET c.enrolled_students = (
--     SELECT COUNT(*)
--     FROM course_members cm
--     WHERE cm.course_id = c.id
-- );

-- Verify the updates
SELECT 
    c.id,
    c.name,
    c.groups,
    c.quizzes,
    c.enrolled_students,
    (SELECT COUNT(*) FROM course_groups cg WHERE cg.course_id = c.id) as actual_groups,
    (SELECT COUNT(*) FROM course_quizzes cq WHERE cq.course_id = c.id) as actual_quizzes,
    (SELECT COUNT(*) FROM course_members cm WHERE cm.course_id = c.id AND cm.course_member_status = 1 AND (cm.role = 1 OR cm.role = 2)) as actual_enrolled_students
FROM courses c
ORDER BY c.id;