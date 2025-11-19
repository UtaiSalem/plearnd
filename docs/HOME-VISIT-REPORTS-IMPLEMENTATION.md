# Home Visit Reports System - Quick Implementation Guide

## 📁 ไฟล์ที่สร้างใหม่

### Frontend Components

1. **VisitReports.vue** - `resources/js/Pages/Learn/Student/HomeVisit/Admin/Components/VisitReports.vue`

    - หน้ารายงานหลัก
    - ฟีเจอร์: ตัวกรอง, ค้นหา, ตาราง, pagination, export

2. **VisitDetailModal.vue** - `resources/js/Pages/Learn/Student/HomeVisit/Admin/Components/VisitDetailModal.vue`
    - Modal แสดงรายละเอียดแบบเต็ม
    - ฟีเจอร์: แสดงข้อมูลครบถ้วน, gallery รูปภาพ, print, download

### Backend Updates

3. **AdminController.php** - เพิ่ม methods ใหม่:

    - `getAllVisitsForReports()` - โหลดข้อมูลให้ Dashboard
    - `getAllVisits()` - API สำหรับ filter/search
    - `downloadReport()` - ดาวน์โหลด PDF รายบุคคล
    - `exportToExcel()` - ส่งออก Excel
    - `exportToPDF()` - ส่งออก PDF สรุป

4. **API Routes** - `routes/api.php`
    ```php
    GET  /api/home-visit/admin/visits
    GET  /api/home-visit/admin/visits/{id}/report
    POST /api/home-visit/admin/visits/export/excel
    POST /api/home-visit/admin/visits/export/pdf
    ```

### Documentation

5. **HOME-VISIT-REPORTS-ADMIN-GUIDE.md** - คู่มือการใช้งานแบบละเอียด
6. **HOME-VISIT-REPORTS-IMPLEMENTATION.md** - คู่มือนี้

## ✅ สิ่งที่ทำเสร็จแล้ว

-   ✅ UI/UX Design ครบถ้วน
-   ✅ ระบบตัวกรองและค้นหา
-   ✅ ตารางแสดงข้อมูล พร้อม pagination
-   ✅ Modal รายละเอียดแบบเต็ม
-   ✅ Image Gallery with Lightbox
-   ✅ สถิติแบบเรียลไทม์
-   ✅ Responsive Design
-   ✅ Backend API structure
-   ✅ Integration with Dashboard

## ⏳ สิ่งที่ต้องทำต่อ

### 1. ติดตั้ง Dependencies (Required)

```bash
# Laravel Excel for exporting
composer require maatwebsite/excel

# PDF Generator (เลือก 1 อย่าง)
composer require barryvdh/laravel-dompdf
# หรือ
composer require tecnickcom/tcpdf
```

### 2. สร้าง Excel Export Class

```bash
php artisan make:export HomeVisitsExport --model=StudentHomeVisit
```

**ไฟล์:** `app/Exports/HomeVisitsExport.php`

```php
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HomeVisitsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $visits;

    public function __construct($visits)
    {
        $this->visits = $visits;
    }

    public function collection()
    {
        return $this->visits;
    }

    public function headings(): array
    {
        return [
            'รหัส',
            'วันที่เยี่ยม',
            'เวลา',
            'ชื่อนักเรียน',
            'รหัสนักเรียน',
            'โซน',
            'ครูผู้เยี่ยม',
            'สถานะ',
            'ผลการสังเกต',
            'ข้อเสนอแนะ',
            'จำนวนรูปภาพ',
        ];
    }

    public function map($visit): array
    {
        return [
            $visit->id,
            $visit->visit_date->format('d/m/Y'),
            $visit->visit_time ? date('H:i', strtotime($visit->visit_time)) : '-',
            $visit->student ? "{$visit->student->first_name_th} {$visit->student->last_name_th}" : '-',
            $visit->student?->student_id ?? '-',
            $visit->zone?->zone_name ?? '-',
            $visit->visitor_name ?? $visit->participants->pluck('participant_name')->join(', '),
            $this->getStatusText($visit->visit_status),
            $visit->observations ?? '-',
            $visit->recommendations ?? '-',
            $visit->images_count ?? 0,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    private function getStatusText($status)
    {
        $map = [
            'pending' => 'รอดำเนินการ',
            'in-progress' => 'กำลังดำเนินการ',
            'completed' => 'เสร็จสิ้น',
            'cancelled' => 'ยกเลิก'
        ];
        return $map[$status] ?? $status;
    }
}
```

### 3. สร้าง PDF Views

**ไฟล์:** `resources/views/reports/home-visit-detail.blade.php`

```blade
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายงานการเยี่ยมบ้าน #{{ $visit->id }}</title>
    <style>
        body { font-family: 'Sarabun', sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 8px; border: 1px solid #ddd; }
        .label { font-weight: bold; background: #f5f5f5; }
        .images { display: flex; flex-wrap: wrap; gap: 10px; }
        .images img { width: 200px; height: 200px; object-fit: cover; }
    </style>
</head>
<body>
    <div class="header">
        <h1>รายงานการเยี่ยมบ้านนักเรียน</h1>
        <p>รหัส: #{{ $visit->id }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">วันที่เยี่ยม</td>
            <td>{{ $visit->visit_date->format('d/m/Y') }}</td>
            <td class="label">เวลา</td>
            <td>{{ $visit->visit_time ? date('H:i', strtotime($visit->visit_time)) : '-' }}</td>
        </tr>
        <tr>
            <td class="label">นักเรียน</td>
            <td colspan="3">
                {{ $visit->student ? "{$visit->student->first_name_th} {$visit->student->last_name_th}" : '-' }}
                @if($visit->student?->student_id)
                    (รหัส: {{ $visit->student->student_id }})
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">โซน</td>
            <td>{{ $visit->zone?->zone_name ?? '-' }}</td>
            <td class="label">สถานะ</td>
            <td>{{ $visit->visit_status }}</td>
        </tr>
        <tr>
            <td class="label">ครูผู้เยี่ยม</td>
            <td colspan="3">
                @if($visit->participants->count() > 0)
                    {{ $visit->participants->pluck('participant_name')->join(', ') }}
                @else
                    {{ $visit->visitor_name ?? '-' }}
                @endif
            </td>
        </tr>
    </table>

    @if($visit->observations)
        <h3>ผลการสังเกต</h3>
        <p>{{ $visit->observations }}</p>
    @endif

    @if($visit->notes)
        <h3>บันทึกเพิ่มเติม</h3>
        <p>{{ $visit->notes }}</p>
    @endif

    @if($visit->recommendations)
        <h3>ข้อเสนอแนะ</h3>
        <p>{{ $visit->recommendations }}</p>
    @endif

    @if($visit->images->count() > 0)
        <h3>รูปภาพประกอบ ({{ $visit->images->count() }} รูป)</h3>
        <div class="images">
            @foreach($visit->images as $image)
                <img src="{{ public_path('storage/' . $image->image_path) }}" alt="รูปภาพ">
            @endforeach
        </div>
    @endif
</body>
</html>
```

**ไฟล์:** `resources/views/reports/home-visits-summary.blade.php`

```blade
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายงานสรุปการเยี่ยมบ้าน</title>
    <style>
        body { font-family: 'Sarabun', sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        th { background: #f5f5f5; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>รายงานสรุปการเยี่ยมบ้านนักเรียน</h1>
        <p>จำนวน: {{ $visits->count() }} รายการ</p>
        <p>วันที่สร้างรายงาน: {{ $generated_at }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>วันที่</th>
                <th>นักเรียน</th>
                <th>โซน</th>
                <th>ครูผู้เยี่ยม</th>
                <th>สถานะ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($visits as $visit)
                <tr>
                    <td>{{ $visit->id }}</td>
                    <td>{{ $visit->visit_date->format('d/m/Y') }}</td>
                    <td>
                        {{ $visit->student ? "{$visit->student->first_name_th} {$visit->student->last_name_th}" : '-' }}
                    </td>
                    <td>{{ $visit->zone?->zone_name ?? '-' }}</td>
                    <td>
                        @if($visit->participants->count() > 0)
                            {{ $visit->participants->pluck('participant_name')->join(', ') }}
                        @else
                            {{ $visit->visitor_name ?? '-' }}
                        @endif
                    </td>
                    <td>{{ $visit->visit_status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
```

### 4. Update AdminController

ลบ comment `// TODO` และใช้งานจริง:

```php
public function downloadReport($visitId)
{
    $visit = StudentHomeVisit::with([
        'student',
        'zone',
        'participants',
        'images',
        'creator'
    ])->findOrFail($visitId);

    $pdf = \PDF::loadView('reports.home-visit-detail', [
        'visit' => $visit
    ]);

    return $pdf->download("home-visit-report-{$visitId}.pdf");
}

public function exportToExcel(Request $request)
{
    $visitIds = $request->get('visits', []);

    $visits = StudentHomeVisit::with([
        'student',
        'zone',
        'participants',
        'images'
    ])->whereIn('id', $visitIds)->get();

    return Excel::download(
        new HomeVisitsExport($visits),
        'home-visits-' . now()->format('Y-m-d') . '.xlsx'
    );
}

public function exportToPDF(Request $request)
{
    $visitIds = $request->get('visits', []);

    $visits = StudentHomeVisit::with([
        'student',
        'zone',
        'participants',
        'images'
    ])->whereIn('id', $visitIds)->get();

    $filters = $request->get('filters');

    $pdf = \PDF::loadView('reports.home-visits-summary', [
        'visits' => $visits,
        'filters' => $filters,
        'generated_at' => now()->format('d/m/Y H:i:s')
    ]);

    return $pdf->download('home-visits-summary-' . now()->format('Y-m-d') . '.pdf');
}
```

### 5. Config DomPDF (ถ้าใช้ DomPDF)

**ไฟล์:** `config/dompdf.php` (สร้างใหม่ถ้ายังไม่มี)

```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

แก้ไข config เพื่อรองรับภาษาไทย:

```php
return [
    'font_dir' => storage_path('fonts/'),
    'font_cache' => storage_path('fonts/'),
    'temp_dir' => sys_get_temp_dir(),
    'chroot' => realpath(base_path()),
    'enable_font_subsetting' => false,
    'pdf_backend' => 'CPDF',
    'default_media_type' => 'screen',
    'default_paper_size' => 'a4',
    'default_font' => 'sarabun',
    'dpi' => 96,
    'enable_php' => false,
    'enable_javascript' => true,
    'enable_remote' => true,
    'font_height_ratio' => 1.1,
    'enable_html5_parser' => true,
];
```

ดาวน์โหลดฟอนต์ Sarabun ไปไว้ที่ `storage/fonts/`

### 6. สร้างข้อมูลทดสอบ (Optional)

```bash
php artisan make:seeder HomeVisitReportSeeder
```

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudentHomeVisit;
use App\Models\Student;
use App\Models\HomeVisitZone;
use App\Models\HomeVisitParticipant;
use App\Models\Learn\Student\HomeVisitImage;

class HomeVisitReportSeeder extends Seeder
{
    public function run()
    {
        $students = Student::take(30)->get();
        $zones = HomeVisitZone::all();

        if ($students->isEmpty() || $zones->isEmpty()) {
            $this->command->error('กรุณาสร้างข้อมูล Students และ Zones ก่อน');
            return;
        }

        foreach ($students as $student) {
            $visit = StudentHomeVisit::create([
                'student_id' => $student->id,
                'zone_id' => $zones->random()->id,
                'visit_date' => now()->subDays(rand(0, 60)),
                'visit_time' => sprintf('%02d:%02d:00', rand(8, 16), rand(0, 59)),
                'visit_status' => collect(['pending', 'in-progress', 'completed'])->random(),
                'observations' => fake()->paragraphs(2, true),
                'notes' => fake()->paragraph(),
                'recommendations' => fake()->paragraph(),
                'follow_up' => rand(0, 1) ? fake()->sentence() : null,
                'next_visit' => rand(0, 1) ? now()->addDays(rand(30, 90)) : null,
                'created_by' => 1,
            ]);

            // Add 1-3 participants
            for ($i = 0; $i < rand(1, 3); $i++) {
                HomeVisitParticipant::create([
                    'home_visit_id' => $visit->id,
                    'participant_name' => fake('th_TH')->name(),
                    'participant_position' => collect(['ครูประจำชั้น', 'ครูแนะแนว', 'ผู้อำนวยการ'])->random(),
                    'participant_role' => collect(['หัวหน้าทีม', 'ผู้ช่วย', 'ผู้บันทึก'])->random(),
                ]);
            }

            // Add 2-6 images
            for ($i = 0; $i < rand(2, 6); $i++) {
                HomeVisitImage::create([
                    'home_visit_id' => $visit->id,
                    'image_path' => 'home-visits/sample' . rand(1, 5) . '.jpg',
                    'image_type' => collect(['evidence', 'activity'])->random(),
                    'image_description' => fake()->sentence(),
                ]);
            }
        }

        $this->command->info('สร้างข้อมูลทดสอบ ' . $students->count() . ' รายการเรียบร้อย');
    }
}
```

รัน Seeder:

```bash
php artisan db:seed --class=HomeVisitReportSeeder
```

### 7. ทดสอบระบบ

1. **ทดสอบการแสดงผล:**

    - เปิด `/home-visit/admin/dashboard`
    - คลิกแท็บ "รายงานการเยี่ยมบ้าน"
    - ตรวจสอบว่าข้อมูลแสดงครบถ้วน

2. **ทดสอบการกรอง:**

    - กรองตามวันที่
    - กรองตามสถานะ
    - กรองตามโซน
    - ค้นหาตามชื่อ

3. **ทดสอบ Modal:**

    - คลิกดูรายละเอียด
    - ตรวจสอบข้อมูลทุกส่วน
    - ทดสอบ Image Gallery

4. **ทดสอบ Export:**
    - ทดสอบส่งออก Excel
    - ทดสอบส่งออก PDF รายบุคคล
    - ทดสอบส่งออก PDF สรุป

## 🎨 Customization

### เปลี่ยนสีธีม

แก้ไขใน `VisitReports.vue`:

```vue
<!-- จาก indigo-600 เป็นสีอื่น เช่น -->
<button class="bg-blue-600 hover:bg-blue-700">
```

### เพิ่ม Columns ในตาราง

แก้ไขใน `VisitReports.vue`:

```vue
<th>คอลัมน์ใหม่</th>
<!-- และ -->
<td>{{ visit.new_field }}</td>
```

### เพิ่มตัวกรอง

แก้ไขใน `VisitReports.vue`:

```javascript
const filters = ref({
    // ... existing filters
    newFilter: "",
});
```

## 🐛 Troubleshooting

### ปัญหา: รูปภาพไม่แสดงใน PDF

**แก้ไข:** ใช้ `public_path()` แทน URL

```php
<img src="{{ public_path('storage/' . $image->image_path) }}">
```

### ปัญหา: ฟอนต์ไทยไม่แสดงใน PDF

**แก้ไข:** ติดตั้งฟอนต์ Sarabun และ config DomPDF

### ปัญหา: Excel ไม่มีสไตล์

**แก้ไข:** implement `WithStyles` interface

### ปัญหา: Memory limit ตอน export รายการเยอะ

**แก้ไข:** เพิ่ม memory_limit ใน php.ini หรือใช้ chunk

```php
StudentHomeVisit::chunk(100, function($visits) {
    // process
});
```

## 📝 Checklist

-   [ ] ติดตั้ง dependencies (Laravel Excel, DomPDF)
-   [ ] สร้าง Export class
-   [ ] สร้าง PDF views
-   [ ] Update Controller methods
-   [ ] Config DomPDF สำหรับภาษาไทย
-   [ ] สร้างข้อมูลทดสอบ
-   [ ] ทดสอบทุก features
-   [ ] ตรวจสอบ responsive design
-   [ ] ทดสอบ performance กับข้อมูลจำนวนมาก
-   [ ] Deploy to production

## 🚀 Production Deployment

1. Clear cache:

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

2. Optimize:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. Build frontend:

```bash
npm run build
```

4. Set permissions:

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

## 📞 Support

หากมีปัญหาหรือข้อสงสัย:

-   ดูเอกสาร: `docs/HOME-VISIT-REPORTS-ADMIN-GUIDE.md`
-   ตรวจสอบ logs: `storage/logs/laravel.log`
-   Debug mode: แก้ `APP_DEBUG=true` ใน `.env`

---

**Version:** 1.0.0  
**Created:** 2025-11-19  
**Status:** Ready for Implementation
