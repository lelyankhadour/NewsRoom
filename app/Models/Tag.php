<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

#[Fillable(['name', 'slug'])]
class Tag extends Model
{
    use HasFactory;

 
    public function articles(): MorphToMany
    {
        return $this->morphedByMany(Article::class, 'taggable', 'taggables', 'tag_id', 'taggable_id');
    }
}