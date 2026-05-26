<?php

namespace App\Models;

use App\Enums\ArticleStatus;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute; 
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

#[Fillable(['title', 'slug', 'content', 'status', 'user_id', 'published_at'])] 
#[Appends(['read_time'])]
class Article extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => ArticleStatus::class,
            'published_at' => 'datetime'
        ];
    }

    // ---------- Relationships ----------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable', 'taggables', 'taggable_id', 'tag_id');
    }

    // ----------- Accessors ----------

    
    protected function readTime(): Attribute 
    
    { 
        return Attribute::make(
            get: function () {
                $wordCount = str_word_count(strip_tags($this->content ?? ''));
                $minutes = ceil($wordCount / 200);
                return $minutes . ' min' . ($minutes > 1 ? 's' : '');
            }
        );
    }

    // ---------- Scopes ----------

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ArticleStatus::PUBLISHED);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', ArticleStatus::DRAFT);
    }
}