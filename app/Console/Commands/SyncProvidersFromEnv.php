<?php

namespace App\Console\Commands;

use App\Models\ReviewProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SyncProvidersFromEnv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reviews:sync-providers 
                            {--force : Force sync even if provider exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-configure review providers from .env variables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Syncing review providers from .env...');

        // Get enabled providers from env
        $enabled = env('REVIEWS_PROVIDERS_ENABLED', 'local');
        $providers = array_map('trim', explode(',', $enabled));

        $this->info("📋 Found " . count($providers) . " provider(s): " . implode(', ', $providers));

        $synced = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($providers as $slug) {
            $slug = strtolower(trim($slug));

            // Skip local provider (managed separately)
            if ($slug === 'local') {
                $this->line("   ⏭️  Skipping 'local' (system provider)");
                $skipped++;
                continue;
            }

            try {
                $result = $this->syncProvider($slug);

                if ($result) {
                    $this->info("   ✅ Synced: {$slug}");
                    $synced++;
                } else {
                    $this->warn("   ⚠️  Skipped: {$slug} (already exists, use --force to update)");
                    $skipped++;
                }
            } catch (\Throwable $e) {
                $this->error("   ❌ Error syncing {$slug}: {$e->getMessage()}");
                $errors++;
            }
        }

        // Summary
        $this->newLine();
        $this->info('✅ Sync completed!');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Synced', $synced],
                ['Skipped', $skipped],
                ['Errors', $errors],
            ]
        );

        // Clear cache
        Cache::flush();
        $this->info('🗑️  Cache cleared');

        return 0;
    }

    /**
     * Sync a single provider from env variables
     */
    protected function syncProvider(string $slug): bool
    {
        $force = $this->option('force');

        // Check if provider already exists
        $existing = ReviewProvider::where('slug', $slug)->first();
        if ($existing && !$force) {
            return false;
        }

        // Get env variables
        $prefix = strtoupper($slug);

        // Prioritize REVIEWS_BASE over API_URL
        $apiUrl = env("{$prefix}_REVIEWS_BASE") ?? env("{$prefix}_API_URL");
        $apiKey = env("{$prefix}_API_KEY");
        $apiKeyHeader = env("{$prefix}_API_KEY_HEADER", 'Authorization');
        $listPath = env("{$prefix}_LIST_PATH", 'reviews');

        // Validate required variables
        if (!$apiUrl) {
            throw new \Exception("Missing {$prefix}_REVIEWS_BASE or {$prefix}_API_URL in .env");
        }

        if (!$apiKey) {
            throw new \Exception("Missing {$prefix}_API_KEY in .env");
        }

        // Build settings
        $settings = [
            'url' => '{env:' . $prefix . '_REVIEWS_BASE}',
            'headers' => [],
            'list_path' => $listPath,
            'map' => [
                'id' => 'id',
                'rating' => 'rating',
                'title' => 'title',
                'body' => 'text',
                'author_name' => 'author',
                'date' => 'date',
            ],
        ];

        // Set authorization header
        if ($apiKeyHeader === 'Authorization') {
            $settings['headers']['Authorization'] = 'Bearer {env:' . $prefix . '_API_KEY}';
        } else {
            $settings['headers'][$apiKeyHeader] = '{env:' . $prefix . '_API_KEY}';
        }

        // Specific configuration for Viator
        if ($slug === 'viator') {
            $settings['method'] = 'POST';
            $settings['headers']['Accept'] = 'application/json;version=2.0';
            $settings['headers']['Content-Type'] = 'application/json';
            $settings['headers']['Accept-Language'] = 'en-US'; // Default language for API response

            // Viator requires a JSON body with productCode and other params
            $settings['payload'] = [
                'productCode' => '{product_code}',
                'count'       => 10,
                'start'       => 1,
                'provider'    => 'VIATOR',
                'sortBy'      => 'MOST_RECENT',
            ];

            // Override default map for Viator's specific response structure
            $settings['map'] = [
                'id'          => 'reviewId',
                'rating'      => 'rating',
                'title'       => 'title',
                'body'        => 'text',
                'author_name' => [
                    'viatorConsumerName',
                    'consumerName',
                    'userNickname',
                    'userName'
                ],
                'date'        => 'publishedDate',
                'language'    => 'language',
            ];
        }

        // Preserve existing product_map if provider already exists
        if ($existing && isset($existing->settings['product_map'])) {
            $settings['product_map'] = $existing->settings['product_map'];
        }

        // Create or update provider
        $data = [
            'name' => ucfirst($slug),
            'slug' => $slug,
            'driver' => 'http_json',
            'settings' => $settings,
            'is_active' => true,
            'indexable' => false, // External reviews should not be indexed
            'cache_ttl_sec' => 3600,
        ];

        if ($existing) {
            $existing->update($data);
        } else {
            ReviewProvider::create($data);
        }

        return true;
    }
}
