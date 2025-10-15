<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\StudentCard;
use Carbon\Carbon;

class PopulateStudentsFromStudentCardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting to populate students table from student_cards...');
        
        // Get all student cards
        $studentCards = StudentCard::all();
        $this->command->info("Found {$studentCards->count()} student cards");
        
        $created = 0;
        $updated = 0;
        $skipped = 0;
        
        foreach ($studentCards as $card) {
            try {
                // Check if student already exists
                $student = Student::where('student_id', $card->student_number)
                    ->orWhere('citizen_id', $card->national_id)
                    ->first();
                
                // Prepare student data
                $studentData = [
                    'student_id' => $card->student_number ?: 'STD-' . str_pad($card->id, 6, '0', STR_PAD_LEFT),
                    'citizen_id' => $card->national_id,
                    'title_prefix_th' => $card->title_name,
                    'first_name_th' => $card->first_name_thai,
                    'last_name_th' => $card->last_name_thai,
                    'date_of_birth' => $this->parseDate($card->birth_date),
                    'nationality' => 'ไทย',
                    'profile_image' => $card->profile_image,
                    'status' => 'active',
                    'enrollment_date' => now(),
                ];
                
                // Parse English name if available
                if ($card->first_name_english) {
                    $englishNames = explode(' ', trim($card->first_name_english), 2);
                    $studentData['first_name_en'] = $englishNames[0] ?? '';
                    $studentData['last_name_en'] = $englishNames[1] ?? '';
                }
                
                // Set title prefix in English based on Thai title
                $studentData['title_prefix_en'] = $this->mapTitleToEnglish($card->title_name);
                
                // Determine gender from title
                $studentData['gender'] = $this->determineGender($card->title_name);
                
                if ($student) {
                    // Update existing student with new data (only if fields are empty)
                    $updateData = [];
                    foreach ($studentData as $key => $value) {
                        if ($value && empty($student->$key)) {
                            $updateData[$key] = $value;
                        }
                    }
                    
                    if (!empty($updateData)) {
                        $student->update($updateData);
                        $updated++;
                        $this->command->line("Updated student: {$card->student_number} - {$card->first_name_thai} {$card->last_name_thai}");
                    } else {
                        $skipped++;
                    }
                } else {
                    // Create new student
                    $student = Student::create($studentData);
                    $created++;
                    $this->command->line("Created student: {$card->student_number} - {$card->first_name_thai} {$card->last_name_thai}");
                }
                
            } catch (\Exception $e) {
                $this->command->error("Error processing card ID {$card->id}: " . $e->getMessage());
                continue;
            }
        }
        
        $this->command->info("Population completed!");
        $this->command->info("Created: {$created} students");
        $this->command->info("Updated: {$updated} students");
        $this->command->info("Skipped: {$skipped} students");
    }
    
    /**
     * Parse date from various formats
     */
    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }
        
        try {
            // Try different date formats
            $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d'];
            
            foreach ($formats as $format) {
                $date = Carbon::createFromFormat($format, $dateString);
                if ($date) {
                    return $date->format('Y-m-d');
                }
            }
            
            // Try to parse as general date
            $date = Carbon::parse($dateString);
            return $date->format('Y-m-d');
            
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Map Thai title to English
     */
    private function mapTitleToEnglish($thaiTitle)
    {
        $titleMap = [
            'เด็กชาย' => 'Mr.',
            'เด็กหญิง' => 'Ms.',
            'นาย' => 'Mr.',
            'นางสาว' => 'Ms.',
            'นาง' => 'Mrs.',
        ];
        
        return $titleMap[$thaiTitle] ?? '';
    }
    
    /**
     * Determine gender from Thai title
     */
    private function determineGender($thaiTitle)
    {
        $maleTitle = ['เด็กชาย', 'นาย'];
        $femaleTitle = ['เด็กหญิง', 'นางสาว', 'นาง'];
        
        if (in_array($thaiTitle, $maleTitle)) {
            return 1; // ชาย
        } elseif (in_array($thaiTitle, $femaleTitle)) {
            return 0; // หญิง
        }
        
        return 0; // Default เป็นหญิง
    }
}
