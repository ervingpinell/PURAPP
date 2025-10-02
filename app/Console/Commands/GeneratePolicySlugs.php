<?php

namespace App\Console\Commands;

use App\Models\Policy;
use Illuminate\Console\Command;

class GeneratePolicySlugs extends Command
{
    protected $signature = 'policies:generate-slugs {--force : Regenerate all slugs even if they exist}';
    protected $description = 'Generate slugs for existing policies';

    public function handle()
    {
        $query = Policy::query();

        if (!$this->option('force')) {
            $query->where(function($q) {
                $q->whereNull('slug')
                  ->orWhere('slug', '');
            });
        }

        $policies = $query->get();

        if ($policies->isEmpty()) {
            $this->info('No policies found to process.');
            $this->newLine();
            $this->info('Tip: Use --force to regenerate all slugs');
            return 0;
        }

        $this->info("Found {$policies->count()} policies to process.");
        $bar = $this->output->createProgressBar($policies->count());
        $bar->start();

        foreach ($policies as $policy) {
            $policy->regenerateSlug();
            $this->newLine();
            $this->line("  {$policy->name} → {$policy->slug}");
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✓ Generated slugs for {$policies->count()} policies.");

        return 0;
    }
}
