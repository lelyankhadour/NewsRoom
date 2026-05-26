<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 #[Fillable(['department','job_title','phone_number','bio'])]
class Profile extends Model
{use HasFactory;
    //----------Relationships---------

   public function user(){
    return $this->belongsTo(User::class);
   }
public function attachments(){
    return $this->morphMany(Attachment::class,"attachable");
}
}
