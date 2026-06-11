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
    \Log::info('Observer: Updated triggered for article ' . $article->id);
       
    $isPublished = ($article->status instanceof ArticleStatus) 
        ? ($article->status === ArticleStatus::PUBLISHED) 
        : ($article->status == ArticleStatus::PUBLISHED->value);

    if ($article->wasChanged('status') && $isPublished) {
        \Log::info('Observer: Dispatching ArticlePublished event...');
        
        \App\Events\ArticlePublished::dispatch($article);
    } else {
        \Log::info('Observer: No publication detected.');
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
