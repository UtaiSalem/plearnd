<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = 'demo123';
        $faculty = 'คณะวิทยาศาสตร์และเทคโนโลยี';

        $admin = User::create([
            'username' => 'admin',
            'name' => 'ผู้ดูแลระบบ',
            'email' => 'admin@skru.ac.th',
            'password' => $password,
            'role' => 'admin',
            'faculty' => $faculty,
            'department' => 'งานบริหารกลาง',
            'phone' => '074-336-933',
        ]);

        $chemHead = User::create([
            'username' => 'chem_head',
            'name' => 'อ.ดร.อภิชาติ สังข์ทอง',
            'email' => 'chem.head@skru.ac.th',
            'password' => $password,
            'role' => 'staff',
            'faculty' => $faculty,
            'department' => 'หลักสูตรเคมี',
            'phone' => '074-336-934',
        ]);

        $bioHead = User::create([
            'username' => 'bio_head',
            'name' => 'ผศ.ดร.สมพร เพชรรัตน์',
            'email' => 'bio.head@skru.ac.th',
            'password' => $password,
            'role' => 'staff',
            'faculty' => $faculty,
            'department' => 'หลักสูตรชีววิทยา',
            'phone' => '074-336-935',
        ]);

        $compHead = User::create([
            'username' => 'comp_head',
            'name' => 'อ.พงษ์ศักดิ์ บุญทอง',
            'email' => 'comp.head@skru.ac.th',
            'password' => $password,
            'role' => 'staff',
            'faculty' => $faculty,
            'department' => 'หลักสูตรวิทยาการคอมพิวเตอร์',
            'phone' => '074-336-936',
        ]);

        $student = User::create([
            'username' => 'student',
            'name' => 'นางสาวกชกร แก้วใส',
            'email' => 'student@parichat.skru.ac.th',
            'password' => $password,
            'role' => 'requester',
            'user_type' => 'student',
            'student_id' => '6612345678',
            'faculty' => $faculty,
            'department' => 'หลักสูตรเคมี',
            'phone' => '081-234-5678',
            'default_advisor' => 'อ.ดร.อภิชาติ สังข์ทอง',
        ]);

        $employee = User::create([
            'username' => 'employee',
            'name' => 'นายธีระศักดิ์ ทองอ่อน',
            'email' => 'employee@skru.ac.th',
            'password' => $password,
            'role' => 'requester',
            'user_type' => 'employee',
            'employee_id' => 'EMP-0421',
            'faculty' => $faculty,
            'department' => 'หลักสูตรชีววิทยา',
            'phone' => '089-876-5432',
        ]);

        $chem = Room::create([
            'code' => 'SCI-A301',
            'name' => 'ห้องปฏิบัติการเคมีวิเคราะห์',
            'building' => 'อาคารวิทยาศาสตร์ 1 (อาคาร 50)',
            'floor' => '3',
            'category' => 'ห้องปฏิบัติการเคมี',
            'capacity' => 24,
            'manager_user_id' => $chemHead->id,
            'manager_name' => $chemHead->name,
            'contact' => $chemHead->phone,
            'status' => 'available',
            'open_hours' => '08:30-16:30',
            'summary' => 'ห้องปฏิบัติการสำหรับการวิเคราะห์เชิงปริมาณ การไทเทรต และการเตรียมตัวอย่าง',
            'equipment' => 'ตู้ดูดควัน, เครื่องชั่งวิเคราะห์ 4 ตำแหน่ง, pH meter, เครื่อง spectrophotometer',
        ]);

        $bio = Room::create([
            'code' => 'SCI-B202',
            'name' => 'ห้องปฏิบัติการจุลชีววิทยา',
            'building' => 'อาคารวิทยาศาสตร์ 2',
            'floor' => '2',
            'category' => 'ห้องปฏิบัติการชีววิทยา',
            'capacity' => 20,
            'manager_user_id' => $bioHead->id,
            'manager_name' => $bioHead->name,
            'contact' => $bioHead->phone,
            'status' => 'available',
            'open_hours' => '09:00-16:00',
            'summary' => 'ห้องเพาะเลี้ยงและตรวจสอบจุลินทรีย์ มีระบบควบคุมความปลอดภัยทางชีวภาพ',
            'equipment' => 'Autoclave, ตู้ปลอดเชื้อ (Laminar flow), Incubator, กล้องจุลทรรศน์',
        ]);

        $inst = Room::create([
            'code' => 'SCI-C101',
            'name' => 'ห้องเครื่องมือวิเคราะห์ขั้นสูง',
            'building' => 'อาคารวิทยาศาสตร์ 3',
            'floor' => '1',
            'category' => 'ห้องเครื่องมือ',
            'capacity' => 8,
            'manager_user_id' => $chemHead->id,
            'manager_name' => $chemHead->name,
            'contact' => $chemHead->phone,
            'status' => 'limited',
            'open_hours' => '10:00-16:00',
            'summary' => 'ห้องเครื่องมือสำหรับงานวิจัย ต้องนัดล่วงหน้าและมีอาจารย์ควบคุม',
            'equipment' => 'GC-MS, UV-Vis spectrophotometer, HPLC, FTIR',
        ]);

        $comp = Room::create([
            'code' => 'SCI-D405',
            'name' => 'ห้องปฏิบัติการคอมพิวเตอร์',
            'building' => 'อาคารวิทยาศาสตร์ 4',
            'floor' => '4',
            'category' => 'ห้องคอมพิวเตอร์',
            'capacity' => 40,
            'manager_user_id' => $compHead->id,
            'manager_name' => $compHead->name,
            'contact' => $compHead->phone,
            'status' => 'available',
            'open_hours' => '08:00-20:00',
            'summary' => 'ห้องคอมพิวเตอร์สำหรับการเรียนการสอนและฝึกปฏิบัติด้านวิทยาการข้อมูล',
            'equipment' => 'PC 40 เครื่อง (Intel i7, RAM 16GB), โปรเจกเตอร์, Smart board',
        ]);

        $physics = Room::create([
            'code' => 'SCI-A105',
            'name' => 'ห้องปฏิบัติการฟิสิกส์ทั่วไป',
            'building' => 'อาคารวิทยาศาสตร์ 1 (อาคาร 50)',
            'floor' => '1',
            'category' => 'ห้องปฏิบัติการฟิสิกส์',
            'capacity' => 30,
            'manager_user_id' => null,
            'manager_name' => 'ภาควิชาฟิสิกส์',
            'contact' => '074-336-937',
            'status' => 'maintenance',
            'open_hours' => '08:30-16:30',
            'summary' => 'ห้องปฏิบัติการพื้นฐานสำหรับวิชาฟิสิกส์ทั่วไป (ปิดปรับปรุงระบบไฟฟ้า)',
            'equipment' => 'อุปกรณ์การทดลองกลศาสตร์, อุปกรณ์ไฟฟ้า-แม่เหล็ก, oscilloscope',
        ]);

        Booking::create([
            'room_id' => $chem->id,
            'user_id' => $student->id,
            'requester_name' => $student->name,
            'requester_type' => 'student',
            'requester_identifier' => $student->student_id,
            'faculty' => $student->faculty,
            'department' => $student->department,
            'phone' => $student->phone,
            'advisor_name' => $student->default_advisor,
            'start_at' => Carbon::tomorrow()->setTime(9, 0),
            'end_at' => Carbon::tomorrow()->setTime(11, 0),
            'attendees' => 4,
            'purpose' => 'วิเคราะห์ตัวอย่างน้ำตามโครงงานวิทยาศาสตร์ เรื่องคุณภาพน้ำในชุมชน',
            'requirements' => 'ขอใช้ตู้ดูดควันและเครื่องชั่ง 4 ตำแหน่ง',
            'status' => 'approved',
            'staff_status' => 'scheduled',
            'reviewed_by' => $chemHead->id,
            'reviewed_at' => now()->subDay(),
        ]);

        Booking::create([
            'room_id' => $bio->id,
            'user_id' => $employee->id,
            'requester_name' => $employee->name,
            'requester_type' => 'employee',
            'requester_identifier' => $employee->employee_id,
            'faculty' => $employee->faculty,
            'department' => $employee->department,
            'phone' => $employee->phone,
            'advisor_name' => null,
            'start_at' => Carbon::tomorrow()->addDay()->setTime(13, 0),
            'end_at' => Carbon::tomorrow()->addDay()->setTime(16, 0),
            'attendees' => 6,
            'purpose' => 'ฝึกปฏิบัติเทคนิคการเพาะเลี้ยงเชื้อแบคทีเรียให้กับนักศึกษาชั้นปีที่ 2',
            'requirements' => 'ขอใช้ตู้ Laminar flow 2 ตู้ และ incubator',
            'status' => 'pending',
            'staff_status' => 'scheduled',
        ]);

        Booking::create([
            'room_id' => $inst->id,
            'user_id' => $student->id,
            'requester_name' => $student->name,
            'requester_type' => 'student',
            'requester_identifier' => $student->student_id,
            'faculty' => $student->faculty,
            'department' => $student->department,
            'phone' => $student->phone,
            'advisor_name' => $student->default_advisor,
            'start_at' => Carbon::tomorrow()->addDays(2)->setTime(10, 0),
            'end_at' => Carbon::tomorrow()->addDays(2)->setTime(12, 0),
            'attendees' => 2,
            'purpose' => 'ใช้ HPLC วิเคราะห์ปริมาณสาร phenolic ในตัวอย่างพืช',
            'requirements' => 'ต้องการอาจารย์ควบคุมระหว่างใช้เครื่อง',
            'status' => 'pending',
            'staff_status' => 'scheduled',
        ]);

        // Generate additional test data
        User::factory(15)->student()->create();
        User::factory(5)->employee()->create();
        User::factory(3)->staff()->create();
        
        $rooms = Room::all();
        $users = User::where('role', 'requester')->get();
        
        foreach ($rooms as $room) {
            Booking::factory(5)->create([
                'room_id' => $room->id,
                'user_id' => $users->random()->id,
            ]);
        }
    }
}
