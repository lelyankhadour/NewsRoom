<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
protected $fillable = ['file_path', 'file_name', 'file_type', 'file_size', 'attachable_id', 'attachable_type'];
    use HasFactory;

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }}
