<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;

// ดูข้อมูลนักเรียน 5 คนแรกที่มีข้อมูลห้องเรียน
$students = Student::whereNotNull('class_level')
    ->whereNotNull('class_section')
    ->limit(5)
    ->get(['student_id', 'first_name_th', 'last_name_th', 'class_level', 'class_section']);

echo "=== ตัวอย่างข้อมูลนักเรียนที่มีห้องเรียน ===\n";
foreach ($students as $student) {
    echo "{$student->first_name_th} {$student->last_name_th} - ม.{$student->class_level}/{$student->class_section}\n";
}

// สรุปจำนวนนักเรียนแต่ละห้อง
echo "\n=== สรุปจำนวนนักเรียนแต่ละห้อง (5 ห้องแรก) ===\n";
$classSummary = Student::selectRaw('class_level, class_section, COUNT(*) as count')
    ->whereNotNull('class_level')
    ->whereNotNull('class_section')
    ->groupBy('class_level', 'class_section')
    ->orderBy('class_level')
    ->orderBy('class_section')
    ->limit(10)
    ->get();

foreach ($classSummary as $class) {
    echo "ม.{$class->class_level}/{$class->class_section}: {$class->count} คน\n";
}

echo "\n=== สรุปรวม ===\n";
$total = Student::whereNotNull('class_level')->whereNotNull('class_section')->count();
echo "จำนวนนักเรียนทั้งหมดที่มีข้อมูลห้องเรียน: {$total} คน\n";