<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixActivityNamespaces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activities:fix-namespaces';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix activityable_type namespace values in activities table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fix activityable_type namespaces...');

        // Show current namespace distribution before fixing
        $this->info('Current namespace distribution:');
        $currentNamespaces = DB::table('activities')
            ->select('activityable_type', DB::raw('count(*) as count'))
            ->groupBy('activityable_type')
            ->orderBy('count', 'desc')
            ->get();

        foreach ($currentNamespaces as $namespace) {
            $this->line("  {$namespace->activityable_type}: {$namespace->count}");
        }

        // Define namespace mappings
        $namespaceMappings = [
            'App\Models\Post' => 'App\Models\Play\Post',
            'Post' => 'App\Models\Play\Post',
            'App\Models\AcademyPost' => 'App\Models\Learn\Academy\AcademyPost',
            'AcademyPost' => 'App\Models\Learn\Academy\AcademyPost',
            'App\Models\CoursePost' => 'App\Models\Learn\Course\Post\CoursePost',
            'CoursePost' => 'App\Models\Learn\Course\Post\CoursePost',
            'App\Models\Donate' => 'App\Models\Earn\Donate',
            'Donate' => 'App\Models\Earn\Donate',
            'App\Models\Support' => 'App\Models\Earn\Support',
            'Support' => 'App\Models\Earn\Support',
            'App\Models\DonateRecipient' => 'App\Models\Earn\DonateRecipient',
            'DonateRecipient' => 'App\Models\Earn\DonateRecipient',
            'App\Models\Poll' => 'App\Models\Play\Poll',
            'Poll' => 'App\Models\Play\Poll',
            'App\Models\SupportViewer' => 'App\Models\Earn\SupportViewer',
            'SupportViewer' => 'App\Models\Earn\SupportViewer',
        ];

        $totalUpdated = 0;

        foreach ($namespaceMappings as $oldNamespace => $newNamespace) {
            $count = DB::table('activities')
                ->where('activityable_type', $oldNamespace)
                ->count();

            if ($count > 0) {
                $this->info("Updating {$count} records from '{$oldNamespace}' to '{$newNamespace}'");
                
                $updated = DB::table('activities')
                    ->where('activityable_type', $oldNamespace)
                    ->update(['activityable_type' => $newNamespace]);

                $totalUpdated += $updated;
                $this->line("  ✓ Updated {$updated} records");
            }
        }

        // Show final namespace distribution
        $this->info("\nFinal namespace distribution:");
        $finalNamespaces = DB::table('activities')
            ->select('activityable_type', DB::raw('count(*) as count'))
            ->groupBy('activityable_type')
            ->orderBy('count', 'desc')
            ->get();

        foreach ($finalNamespaces as $namespace) {
            $this->line("  {$namespace->activityable_type}: {$namespace->count}");
        }

        $this->info("\n✅ Successfully updated {$totalUpdated} activity records!");
        
        Log::info("Activity namespaces fixed: {$totalUpdated} records updated");

        return 0;
    }
}