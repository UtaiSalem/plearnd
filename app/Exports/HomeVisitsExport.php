<?php

namespace App\Exports;

use App\Models\StudentHomeVisit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class HomeVisitsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $visits;

    public function __construct($visits)
    {
        $this->visits = $visits;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->visits;
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'ID',
            'วันที่เยี่ยม',
            'เวลา',
            'ชื่อนักเรียน',
            'ชั้นเรียน',
            'โซน',
            'ครูผู้เยี่ยม',
            'สถานะ',
            'หมายเหตุ',
            'ข้อสังเกต',
            'ข้อเสนอแนะ',
            'การติดตามผล',
            'จำนวนรูปภาพ',
            'สร้างเมื่อ',
        ];
    }

    /**
     * Map data for each row
     */
    public function map($visit): array
    {
        // Student name
        $studentName = 'ไม่ระบุ';
        if ($visit->student) {
            $firstName = $visit->student->first_name_th ?? $visit->student->first_name ?? '';
            $lastName = $visit->student->last_name_th ?? $visit->student->last_name ?? '';
            $studentName = trim($firstName . ' ' . $lastName) ?: 'ไม่ระบุ';
        }

        // Classroom
        $classroom = $visit->student->classroom ?? '-';

        // Zone
        $zoneName = $visit->zone->zone_name ?? '-';

        // Teachers/Participants
        $teachers = [];
        if ($visit->participants && $visit->participants->count() > 0) {
            $teachers = $visit->participants->pluck('participant_name')->toArray();
        }
        $teacherNames = $teachers ? implode(', ', $teachers) : ($visit->visitor_name ?? '-');

        // Status
        $statusMap = [
            'pending' => 'รอดำเนินการ',
            'in-progress' => 'กำลังดำเนินการ',
            'completed' => 'เสร็จสิ้น',
            'cancelled' => 'ยกเลิก'
        ];
        $status = $statusMap[$visit->visit_status] ?? $visit->visit_status;

        return [
            $visit->id,
            Carbon::parse($visit->visit_date)->format('d/m/Y'),
            $visit->visit_time ?? '-',
            $studentName,
            $classroom,
            $zoneName,
            $teacherNames,
            $status,
            $visit->notes ?? '-',
            $visit->observations ?? '-',
            $visit->recommendations ?? '-',
            $visit->follow_up ?? '-',
            $visit->images->count(),
            Carbon::parse($visit->created_at)->format('d/m/Y H:i'),
        ];
    }

    /**
     * Style the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }

    /**
     * Define column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,   // ID
            'B' => 15,  // วันที่เยี่ยม
            'C' => 10,  // เวลา
            'D' => 25,  // ชื่อนักเรียน
            'E' => 12,  // ชั้นเรียน
            'F' => 15,  // โซน
            'G' => 25,  // ครูผู้เยี่ยม
            'H' => 15,  // สถานะ
            'I' => 40,  // หมายเหตุ
            'J' => 40,  // ข้อสังเกต
            'K' => 40,  // ข้อเสนอแนะ
            'L' => 40,  // การติดตามผล
            'M' => 12,  // จำนวนรูปภาพ
            'N' => 18,  // สร้างเมื่อ
        ];
    }
}
