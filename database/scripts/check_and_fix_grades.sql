-- =====================================================
-- Script: ตรวจสอบและแก้ไขเกรดในตาราง course_members
-- วันที่: 2025-11-06
-- วัตถุประสงค์: ตรวจสอบและแก้ไขเกรดที่คำนวณไม่ถูกต้องตามเกณฑ์การศึกษาไทย
-- =====================================================

-- ฟังก์ชันคำนวณเกรดตามเกณฑ์การศึกษาไทย
DELIMITER $$

DROP FUNCTION IF EXISTS calculate_thai_grade$$
CREATE FUNCTION calculate_thai_grade(percentage DECIMAL(5,2))
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE grade INT;
    
    IF percentage IS NULL THEN
        RETURN NULL;
    ELSEIF percentage >= 80 THEN
        SET grade = 4;  -- A (80-100)
    ELSEIF percentage >= 75 THEN
        SET grade = 3;  -- B+ (75-79)
    ELSEIF percentage >= 70 THEN
        SET grade = 3;  -- B (70-74)
    ELSEIF percentage >= 65 THEN
        SET grade = 2;  -- C+ (65-69)
    ELSEIF percentage >= 60 THEN
        SET grade = 2;  -- C (60-64)
    ELSEIF percentage >= 55 THEN
        SET grade = 1;  -- D+ (55-59)
    ELSEIF percentage >= 50 THEN
        SET grade = 1;  -- D (50-54)
    ELSE
        SET grade = 0;  -- F (0-49)
    END IF;
    
    RETURN grade;
END$$

DELIMITER ;

-- =====================================================
-- ส่วนที่ 1: ตรวจสอบเรคคอร์ดที่มีปัญหา
-- =====================================================

SELECT 
    'รายการที่ต้องแก้ไข' AS report_type,
    cm.id,
    cm.user_id,
    u.name AS student_name,
    cm.course_id,
    c.title AS course_title,
    cm.achieved_score,
    c.total_score,
    ROUND((cm.achieved_score / NULLIF(c.total_score, 0)) * 100, 2) AS percentage_calculated,
    calculate_thai_grade(ROUND((cm.achieved_score / NULLIF(c.total_score, 0)) * 100, 2)) AS correct_grade,
    cm.grade_progress AS current_grade,
    CASE 
        WHEN calculate_thai_grade(ROUND((cm.achieved_score / NULLIF(c.total_score, 0)) * 100, 2)) != cm.grade_progress 
        THEN 'ไม่ถูกต้อง'
        ELSE 'ถูกต้อง'
    END AS status
FROM course_members cm
INNER JOIN courses c ON cm.course_id = c.id
INNER JOIN users u ON cm.user_id = u.id
WHERE 
    c.total_score > 0 
    AND cm.achieved_score IS NOT NULL
    AND calculate_thai_grade(ROUND((cm.achieved_score / NULLIF(c.total_score, 0)) * 100, 2)) != cm.grade_progress
ORDER BY cm.id;

-- =====================================================
-- ส่วนที่ 2: สรุปจำนวนเรคคอร์ดที่มีปัญหา
-- =====================================================

SELECT 
    'สรุปภาพรวม' AS report_type,
    COUNT(*) AS total_incorrect_records,
    COUNT(DISTINCT cm.course_id) AS affected_courses,
    COUNT(DISTINCT cm.user_id) AS affected_students
FROM course_members cm
INNER JOIN courses c ON cm.course_id = c.id
WHERE 
    c.total_score > 0 
    AND cm.achieved_score IS NOT NULL
    AND calculate_thai_grade(ROUND((cm.achieved_score / NULLIF(c.total_score, 0)) * 100, 2)) != cm.grade_progress;

-- =====================================================
-- ส่วนที่ 3: แสดงรายละเอียดการแก้ไขที่จะทำ (Preview)
-- =====================================================

SELECT 
    'ตัวอย่างการแก้ไข - ก่อนและหลัง' AS report_type,
    cm.id,
    u.name AS student_name,
    c.title AS course_title,
    cm.achieved_score,
    c.total_score,
    ROUND((cm.achieved_score / NULLIF(c.total_score, 0)) * 100, 2) AS percentage,
    cm.grade_progress AS grade_before,
    calculate_thai_grade(ROUND((cm.achieved_score / NULLIF(c.total_score, 0)) * 100, 2)) AS grade_after,
    CONCAT(
        'เกรดเดิม: ', cm.grade_progress, 
        ' → เกรดใหม่: ', 
        calculate_thai_grade(ROUND((cm.achieved_score / NULLIF(c.total_score, 0)) * 100, 2))
    ) AS change_description
FROM course_members cm
INNER JOIN courses c ON cm.course_id = c.id
INNER JOIN users u ON cm.user_id = u.id
WHERE 
    c.total_score > 0 
    AND cm.achieved_score IS NOT NULL
    AND calculate_thai_grade(ROUND((cm.achieved_score / NULLIF(c.total_score, 0)) * 100, 2)) != cm.grade_progress
ORDER BY cm.id
LIMIT 20;

-- =====================================================
-- หมายเหตุ:
-- เกณฑ์เกรดที่ใช้ในการคำนวณ (ตามระบบการศึกษาไทย):
-- - 4 = A (80-100%)
-- - 3 = B+/B (70-79%)
-- - 2 = C+/C (60-69%)
-- - 1 = D+/D (50-59%)
-- - 0 = F (0-49%)
-- =====================================================
