<?php

namespace App\Console\Commands;

use App\Contracts\ArticleRepositoryInterface;
use App\Enums\ArticleStatus;
use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;


class ArchiveArticles extends Command
{protected ArticleRepositoryInterface $articleRepository;
    protected $signature = 'articles:archive {days?} {--dry-run}';
    protected $description = 'Archive old published articles';

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        parent::__construct();
        $this->articleRepository = $articleRepository;
    }

       public function handle(): int
    {
        $this->info('Initializing structural article archiving pipeline...');

    
        $days = $this->argument('days') ? (int) $this->argument('days') : 30;
        $thresholdDate = Carbon::now()->subDays($days);
        
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('!! DRY RUN MODE ENABLED !! No changes will be persisted to the database.');
        }

        $this->comment("Target configuration: Searching for articles published before: {$thresholdDate->toDateTimeString()}");

 
        $query = Article::select(['id', 'status', 'created_at'])
            ->where('status', ArticleStatus::PUBLISHED)
            ->where('created_at', '<', $thresholdDate);

        $totalCount = $query->count();

        if ($totalCount === 0) {
            $this->info('No eligible user article entities found for archiving.');
            return self::SUCCESS;
        }

        $this->warn("Found {$totalCount} articles pending transition. Processing batches...");
        $archivedCount = 0;


        $query->chunkById(100, function ($articles) use (&$archivedCount, $totalCount, $isDryRun) {
            foreach ($articles as $article) {
                /** @var Article $article */
                

                if (!$isDryRun) {
                    $this->articleRepository->update($article, [
                        'status' => ArticleStatus::ARCHIVED
                    ]);
                }
                
                $archivedCount++;
            }
            
            $statusText = $isDryRun ? "Simulated" : "Updated";
            $this->line("Batch processed. Current progress: [{$archivedCount}/{$totalCount}] entries {$statusText}.");
        });

        $finalMessage = $isDryRun 
            ? "Dry run sequence executed successfully. Total simulated archive: {$archivedCount} entities."
            : "Archiving sequence executed successfully. Total archived: {$archivedCount} entities.";

        $this->info($finalMessage);
        
        return self::SUCCESS;
    }
}