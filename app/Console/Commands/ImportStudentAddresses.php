<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\StudentAddress;

class ImportStudentAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'student:import-addresses {--force : Force import even if addresses exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ดึงข้อมูลที่อยู่จากตาราง jsm_student_info มาใส่ในตาราง student_addresses';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🏠 เริ่มดึงข้อมูลที่อยู่จาก jsm_student_info...');
        
        // Check if force flag is set
        $force = $this->option('force');
        
        // Get count of existing addresses
        $existingCount = StudentAddress::count();
        
        if ($existingCount > 0 && !$force) {
            $this->warn("⚠️  พบข้อมูลที่อยู่อยู่แล้ว $existingCount รายการ");
            if (!$this->confirm('คุณต้องการดำเนินการต่อหรือไม่? (ใช้ --force เพื่อบังคับ)')) {
                $this->info('ยกเลิกการดำเนินการ');
                return 0;
            }
        }

        try {
            // Get all records from jsm_student_info with address data
            $jsmRecords = DB::table('jsm_student_info')
                ->select([
                    'student_id',
                    'house_number',
                    'village_number', 
                    'alley',
                    'road',
                    'subdistrict',
                    'district',
                    'province',
                    'postal_code'
                ])
                ->whereNotNull('student_id')
                ->where('student_id', '!=', '')
                ->get();

            $this->info("📊 พบข้อมูล JSM: {$jsmRecords->count()} รายการ");

            $imported = 0;
            $skipped = 0;
            $errors = 0;

            $progressBar = $this->output->createProgressBar($jsmRecords->count());
            $progressBar->start();

            foreach ($jsmRecords as $jsmRecord) {
                try {
                    // Find matching student by student_id
                    $student = Student::where('student_id', $jsmRecord->student_id)->first();
                    
                    if (!$student) {
                        $skipped++;
                        $progressBar->advance();
                        continue;
                    }

                    // Check if this student already has addresses (unless force)
                    if (!$force) {
                        $hasAddress = StudentAddress::where('student_id', $student->id)->exists();
                        if ($hasAddress) {
                            $skipped++;
                            $progressBar->advance();
                            continue;
                        }
                    }

                    // Check if we have meaningful address data
                    if (empty($jsmRecord->house_number) && empty($jsmRecord->subdistrict) && empty($jsmRecord->district)) {
                        $skipped++;
                        $progressBar->advance();
                        continue;
                    }

                    // Create address record
                    StudentAddress::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'address_type' => 'permanent' // Default to permanent address from JSM
                        ],
                        [
                            'house_number' => $this->cleanString($jsmRecord->house_number),
                            'village_number' => $this->cleanString($jsmRecord->village_number),
                            'alley' => $this->cleanString($jsmRecord->alley),
                            'road' => $this->cleanString($jsmRecord->road),
                            'subdistrict' => $this->cleanString($jsmRecord->subdistrict),
                            'district' => $this->cleanString($jsmRecord->district),
                            'province' => $this->cleanString($jsmRecord->province),
                            'postal_code' => $this->cleanString($jsmRecord->postal_code),
                            'is_current' => true // Set as current address
                        ]
                    );

                    $imported++;

                } catch (\Exception $e) {
                    $this->error("\n❌ Error processing student {$jsmRecord->student_id}: {$e->getMessage()}");
                    $errors++;
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            // Summary
            $this->info('✅ การดำเนินการเสร็จสิ้น!');
            $this->table(
                ['สถานะ', 'จำนวน'],
                [
                    ['✅ นำเข้าสำเร็จ', $imported],
                    ['⏭️  ข้าม', $skipped],
                    ['❌ ข้อผิดพลาด', $errors],
                    ['📊 รวม', $jsmRecords->count()]
                ]
            );

            // Show sample imported data
            if ($imported > 0) {
                $this->info("\n📋 ตัวอย่างข้อมูลที่นำเข้า:");
                $sampleAddresses = StudentAddress::with('student')
                    ->latest()
                    ->limit(3)
                    ->get();

                $tableData = [];
                foreach ($sampleAddresses as $address) {
                    $tableData[] = [
                        $address->student->student_id ?? 'N/A',
                        $address->student->first_name_th ?? 'N/A',
                        $address->house_number ?? '-',
                        $address->subdistrict ?? '-',
                        $address->district ?? '-',
                        $address->province ?? '-'
                    ];
                }

                $this->table(
                    ['รหัสนักเรียน', 'ชื่อ', 'บ้านเลขที่', 'ตำบล', 'อำเภอ', 'จังหวัด'],
                    $tableData
                );
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ เกิดข้อผิดพลาด: {$e->getMessage()}");
            return 1;
        }
    }

    /**
     * Clean string data
     */
    private function cleanString($value)
    {
        if (empty($value) || $value === '-' || $value === '0' || $value === 'NULL') {
            return null;
        }
        
        return trim($value);
    }
}
