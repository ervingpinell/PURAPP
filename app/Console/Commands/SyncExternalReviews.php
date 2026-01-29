<?php

namespace App\Console\Commands;

use App\Models\Review;
use App\Models\ReviewProvider;
use App\Services\Reviews\ReviewAggregator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncExternalReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reviews:sync 
                            {--provider= : Specific provider slug to sync}
                            {--all : Sync all active external providers}
                            {--dry-run : Preview changes without persisting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync external reviews from providers (Viator, GYG, etc.) to local database';

    protected ReviewAggregator $aggregator;
    protected array $stats = [
        'inserted' => 0,
        'updated' => 0,
        'deleted' => 0,
        'errors' => 0,
    ];

    public function __construct(ReviewAggregator $aggregator)
    {
        parent::__construct();
        $this->aggregator = $aggregator;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = now();
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be persisted');
        }

        $this->info('🔄 Starting external reviews sync...');

        // Get providers to sync
        $providers = $this->getProvidersToSync();

        if ($providers->isEmpty()) {
            $this->error('❌ No providers found to sync');
            return 1;
        }

        $this->info("📋 Found {$providers->count()} provider(s) to sync");

        // Sync each provider
        foreach ($providers as $provider) {
            $this->syncProvider($provider, $isDryRun);
        }

        $duration = now()->diffInSeconds($startTime);

        // Display summary
        $this->newLine();
        $this->info('✅ Sync completed!');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Inserted', $this->stats['inserted']],
                ['Updated', $this->stats['updated']],
                ['Deleted', $this->stats['deleted']],
                ['Errors', $this->stats['errors']],
                ['Duration', "{$duration}s"],
            ]
        );

        // Log summary
        Log::channel('reviews')->info('External reviews sync completed', [
            'stats' => $this->stats,
            'duration' => $duration,
            'dry_run' => $isDryRun,
        ]);

        return 0;
    }

    /**
     * Get providers to sync based on options
     */
    protected function getProvidersToSync()
    {
        $query = ReviewProvider::where('is_active', true)
            ->where('driver', '!=', 'local');

        if ($providerSlug = $this->option('provider')) {
            $query->where('slug', $providerSlug);
        } elseif (!$this->option('all')) {
            $this->error('❌ Please specify --provider=slug or --all');
            exit(1);
        }

        return $query->get();
    }

    protected function syncProvider(ReviewProvider $provider, bool $isDryRun): void
    {
        $this->info("🔄 Syncing provider: {$provider->name}");

        try {
            // Get product mappings
            $productMap = $provider->settings['product_map'] ?? [];

            if (empty($productMap)) {
                $this->warn("   ⚠️  No product mappings configured for {$provider->name}");
                return;
            }

            $this->line("   📦 Found " . count($productMap) . " product mapping(s)");

            // Track all fetched review IDs across all products
            $allFetchedIds = [];

            // Fetch reviews for each mapped product
            foreach ($productMap as $productId => $productCode) {
                $this->line("   🔍 Fetching reviews for product #{$productId} (code: {$productCode})");

                // Fetch reviews from external API for this specific product
                $externalReviews = $this->aggregator->aggregate([
                    'provider' => $provider->slug,
                    'product_id' => (int) $productId,
                    'limit' => 100,
                ]);

                $this->line("      📥 Fetched " . count($externalReviews) . " reviews");

                // Process each review
                foreach ($externalReviews as $reviewData) {
                    $this->processReview($provider, $reviewData, $isDryRun, $allFetchedIds);
                }
            }

            $this->line("   📥 Total fetched: " . count($allFetchedIds) . " reviews from API");

            // Delete orphaned reviews (reviews in DB but not in API)
            $this->deleteOrphanedReviews($provider, $allFetchedIds, $isDryRun);
        } catch (\Throwable $e) {
            $this->error("   ❌ Error syncing {$provider->name}: {$e->getMessage()}");
            $this->stats['errors']++;

            Log::channel('reviews')->error('Provider sync failed', [
                'provider' => $provider->slug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Process a single review (insert or update)
     */
    protected function processReview(ReviewProvider $provider, array $reviewData, bool $isDryRun, array &$fetchedIds): void
    {
        // Extract provider review ID
        $providerReviewId = $reviewData['id'] ?? $reviewData['provider_review_id'] ?? null;

        if (!$providerReviewId) {
            $this->warn("   ⚠️  Skipping review without ID");
            return;
        }

        $fetchedIds[] = $providerReviewId;

        // Prepare review data
        $data = [
            'provider' => $provider->slug,
            'provider_review_id' => $providerReviewId,
            'product_id' => $reviewData['product_id'] ?? null,
            'rating' => $reviewData['rating'] ?? 5,
            'title' => $reviewData['title'] ?? null,
            'body' => $reviewData['body'] ?? '',
            'author_name' => $reviewData['author_name'] ?? 'Anonymous',
            'language' => $reviewData['language'] ?? 'en',
            'status' => 'published', // External reviews are pre-approved
            'is_public' => true,
            'indexable' => false, // External reviews should not be indexed
            'created_at' => $reviewData['date'] ?? now(),
        ];

        if ($isDryRun) {
            $exists = Review::where('provider', $provider->slug)
                ->where('provider_review_id', $providerReviewId)
                ->exists();

            if ($exists) {
                $this->line("   🔄 Would update: {$providerReviewId}");
            } else {
                $this->line("   ➕ Would insert: {$providerReviewId}");
            }
            return;
        }

        // Upsert review
        $review = Review::updateOrCreate(
            [
                'provider' => $provider->slug,
                'provider_review_id' => $providerReviewId,
            ],
            $data
        );

        if ($review->wasRecentlyCreated) {
            $this->stats['inserted']++;
            $this->line("   ✅ Inserted: {$providerReviewId}");
        } else {
            $this->stats['updated']++;
            $this->line("   🔄 Updated: {$providerReviewId}");
        }
    }

    /**
     * Delete reviews that exist in DB but not in API response
     */
    protected function deleteOrphanedReviews(ReviewProvider $provider, array $fetchedIds, bool $isDryRun): void
    {
        $orphaned = Review::where('provider', $provider->slug)
            ->whereNotIn('provider_review_id', $fetchedIds)
            ->get();

        if ($orphaned->isEmpty()) {
            $this->line("   ✅ No orphaned reviews");
            return;
        }

        $this->warn("   🗑️  Found {$orphaned->count()} orphaned review(s)");

        foreach ($orphaned as $review) {
            if ($isDryRun) {
                $this->line("   🗑️  Would delete: {$review->provider_review_id}");
            } else {
                $this->line("   🗑️  Deleting: {$review->provider_review_id}");
                $review->delete();
                $this->stats['deleted']++;

                Log::channel('reviews')->info('Orphaned review deleted', [
                    'provider' => $provider->slug,
                    'provider_review_id' => $review->provider_review_id,
                ]);
            }
        }
    }
}
