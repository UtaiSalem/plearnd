<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\StudentCard;
use Illuminate\Support\Facades\DB;

class UpdateStudentsClassFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting to update class_level and class_section fields from student_cards...');
        
        // Get all student cards with class information
        $studentCards = StudentCard::whereNotNull('class_level')
            ->whereNotNull('class_section')
            ->get();
            
        $this->command->info("Found {$studentCards->count()} student cards with class information");
        
        $updated = 0;
        $notFound = 0;
        $errors = 0;
        
        foreach ($studentCards as $card) {
            try {
                // Find corresponding student record
                $student = Student::where('student_id', $card->student_number)
                    ->orWhere('citizen_id', $card->national_id)
                    ->first();
                
                if ($student) {
                    // Update class information
                    $student->update([
                        'class_level' => $card->class_level,
                        'class_section' => $card->class_section
                    ]);
                    
                    $updated++;
                    
                    if ($updated % 100 == 0) {
                        $this->command->info("Updated {$updated} students...");
                    }
                    
                } else {
                    $this->command->warn("Student not found for card ID: {$card->id} (Student Number: {$card->student_number}, National ID: {$card->national_id})");
                    $notFound++;
                }
                
            } catch (\Exception $e) {
                $this->command->error("Error processing card ID {$card->id}: " . $e->getMessage());
                $errors++;
            }
        }
        
        $this->command->info('Class fields update completed!');
        $this->command->info("Updated: {$updated} students");
        $this->command->info("Not found: {$notFound} students");
        
        if ($errors > 0) {
            $this->command->warn("Errors: {$errors}");
        }
        
        // Show summary of class distribution
        $this->command->info("\nClass distribution summary:");
        $classSummary = Student::select('class_level', 'class_section', DB::raw('count(*) as count'))
            ->whereNotNull('class_level')
            ->whereNotNull('class_section')
            ->groupBy('class_level', 'class_section')
            ->orderBy('class_level')
            ->orderBy('class_section')
            ->get();
        
        foreach ($classSummary as $summary) {
            $this->command->info("  {$summary->class_level}/{$summary->class_section}: {$summary->count} students");
        }
    }
}
