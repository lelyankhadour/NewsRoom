<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['content', 'commentable_id', 'commentable_type' 
,'user_id'
])]
class Comment extends Model
{
    use HasFactory;

  
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}
