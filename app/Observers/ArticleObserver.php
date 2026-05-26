<?php

namespace App\Observers;

use App\Enums\ArticleStatus;
use App\Events\ArticlePublished;
use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Log;

class ArticleObserver
{
    
public function created(Article $article): void
{
   
}
public function creating(Article $article): void
{
    $article->slug = Str::slug($article->title) . '-' . time();
    if ($article->status === ArticleStatus::PUBLISHED->value) {
        $article->published_at = \Carbon\Carbon::now();
    }
}
    /**
     * Handle the Article "updated" event.
     */
    public function updated(Article $article): void
    {

        if ($article->wasChanged('status') && $article->status === ArticleStatus::PUBLISHED->value) {
            
            ArticlePublished::dispatch($article);

            Log::info('ArticleObserver detected publish status: ArticlePublished event fired.', [
                'article_id' => $article->id,
            ]);
        }
    }
    

    /**
     * Handle the Article "deleted" event.
     */
    public function deleted(Article $article): void
    {
        //
    }

    /**
     * Handle the Article "restored" event.
     */
    public function restored(Article $article): void
    {
        //
    }

    /**
     * Handle the Article "force deleted" event.
     */
    public function forceDeleted(Article $article): void
    {
        //
    }
}
