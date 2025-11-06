# Checkpoint: ตารางแสดงผลสถานะการเข้าเรียนของนักเรียนแยกตามกลุ่ม

## วันที่สร้าง Checkpoint
1 พฤศจิกายน 2025 (UTC+7)

## รายละเอียด Checkpoint
นี่คือจุดมาร์คสำหรับฟีเจอร์ "ตารางแสดงผลสถานะการเข้าเรียนของนักเรียนแยกตามกลุ่ม" สร้างขึ้นเพื่อใช้เป็นจุดอ้างอิงในการย้อนกลับหากเกิดข้อผิดพลาดหลังจากการปรับปรุงในอนาคต

## ไฟล์ที่เกี่ยวข้อง
- `resources/js/PlearndComponents/learn/courses/attendances/MembersAttendanceDetails.vue` - คอมโพเนนต์หลักสำหรับแสดงรายละเอียดการเข้าเรียน
- `resources/js/PlearndComponents/learn/courses/attendances/CourseMemberAttendanceCard.vue` - การ์ดแสดงสถานะการเข้าเรียนของสมาชิก
- `resources/js/PlearndComponents/learn/courses/attendances/CourseAttendance.vue` - คอมโพเนนต์การเข้าเรียนของคอร์ส
- `resources/js/PlearndComponents/learn/courses/attendances/AttendanceStatus.vue` - คอมโพเนนต์สถานะการเข้าเรียน
- `resources/js/stores/attendance.js` - Store สำหรับจัดการข้อมูลการเข้าเรียน
- `resources/js/Pages/Learn/Course/Attendance/Attendances.vue` - หน้าแสดงการเข้าเรียน
- `app/Http/Controllers/Learn/Course/attendances/AttendanceDetailController.php` - Controller สำหรับจัดการรายละเอียดการเข้าเรียน
- `routes/learn/course.php` - เส้นทางสำหรับฟีเจอร์การเข้าเรียน

## ฟีเจอร์หลักที่มีอยู่ใน Checkpoint นี้
1. การแสดงตารางสถานะการเข้าเรียนของนักเรียน
2. การจัดกลุ่มนักเรียนตามเงื่อนไขต่างๆ
3. การแสดงสถานะการเข้าเรียน (เข้าเรียน/ขาด/ลา/สาย)
4. การกรองและค้นหาข้อมูลการเข้าเรียน
5. การแสดงสถิติการเข้าเรียนแบบสรุป

## วิธีการย้อนกลับสู่ Checkpoint นี้
หากต้องการย้อนกลับสู่ checkpoint นี้:
1. ตรวจสอบไฟล์ทั้งหมดในรายการด้านบน
2. คืนค่าไฟล์เหล่านี้จาก backup หรือ version control
3. ตรวจสอบการทำงานของฟีเจอร์ทั้งหมด

## หมายเหตุ
- Checkpoint นี้สร้างขึ้นก่อนการปรับปรุงครั้งต่อไป
- ควรบันทึกการเปลี่ยนแปลงทั้งหมดหลังจาก checkpoint นี้
- หากมีการเปลี่ยนแปลงโครงสร้างฐานข้อมูล ควรสำรองข้อมูลไว้ด้วย