<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentHomeVisit;
use App\Models\Student;

class SyncHomeVisitData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'homevisit:sync 
                            {--dry-run : Preview changes without making them}
                            {--force : Force sync even if data exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Home Visit records with Student database using Hybrid approach';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🏠 Home Visit Data Sync - Hybrid Approach');
        $this->info('==========================================');
        
        // Get statistics
        $totalVisits = \App\Models\StudentHomeVisit::count();
        $unlinkedVisits = \App\Models\StudentHomeVisit::whereNull('student_id')->count();
        $linkedVisits = $totalVisits - $unlinkedVisits;
        
        $this->table(['Metric', 'Count'], [
            ['Total Home Visits', $totalVisits],
            ['Already Linked (Relational)', $linkedVisits],
            ['Unlinked (Standalone)', $unlinkedVisits],
        ]);
        
        if ($unlinkedVisits === 0) {
            $this->info('✅ All Home Visit records are already synced!');
            return 0;
        }
        
        if ($this->option('dry-run')) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->previewSync();
            return 0;
        }
        
        if (!$this->option('force') && !$this->confirm('Do you want to sync ' . $unlinkedVisits . ' unlinked records?')) {
            $this->info('❌ Sync cancelled by user');
            return 1;
        }
        
        $this->info('🔄 Starting sync process...');
        $progressBar = $this->output->createProgressBar($unlinkedVisits);
        
        $syncCount = 0;
        $unlinkedVisits = \App\Models\StudentHomeVisit::whereNull('student_id')->get();
        
        foreach ($unlinkedVisits as $visit) {
            if ($this->syncSingleVisit($visit)) {
                $syncCount++;
            }
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("✅ Sync completed!");
        $this->info("📊 Successfully linked: {$syncCount}/{$unlinkedVisits->count()} records");
        
        if ($syncCount > 0) {
            $this->info('🎯 These records now have both relational and standalone data');
        }
        
        $remainingUnlinked = $unlinkedVisits->count() - $syncCount;
        if ($remainingUnlinked > 0) {
            $this->warn("⚠️  {$remainingUnlinked} records remain as standalone (no matching student found)");
        }
        
        return 0;
    }
    
    private function previewSync()
    {
        $unlinkedVisits = \App\Models\StudentHomeVisit::whereNull('student_id')->take(10)->get();
        
        $this->info('📋 Preview of records that would be synced:');
        
        $previewData = [];
        foreach ($unlinkedVisits as $visit) {
            $student = null;
            if ($visit->id_card) {
                $student = \App\Models\Student::where('citizen_id', $visit->id_card)->first();
            }
            
            $previewData[] = [
                $visit->id,
                $visit->student_name,
                $visit->id_card,
                $student ? '✅ Found' : '❌ Not Found',
                $student ? $student->first_name_th . ' ' . $student->last_name_th : 'N/A'
            ];
        }
        
        $this->table(['Visit ID', 'Student Name', 'ID Card', 'Match Status', 'Matched Student'], $previewData);
    }
    
    private function syncSingleVisit(\App\Models\StudentHomeVisit $visit)
    {
        if (!$visit->id_card) {
            return false;
        }
        
        $student = \App\Models\Student::where('citizen_id', $visit->id_card)->first();
        if (!$student) {
            return false;
        }
        
        $visit->update([
            'student_id' => $student->id,
            'student_name' => $student->first_name_th . ' ' . $student->last_name_th,
            'class' => $student->academicInfo->current_class ?? $visit->class,
            'birth_date' => $visit->birth_date ?? $student->date_of_birth,
            'gender' => $visit->gender ?? ($student->gender === 'male' ? 'ชาย' : 'หญิง'),
        ]);
        
        return true;
    }
}
