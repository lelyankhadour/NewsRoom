<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password','role'])]
#[Hidden(['password', 'remember_token'])]
#[Appends(['is_admin'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable,HasApiTokens;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role'=>UserRole::class,
        ];
    }
//---------------------Relationships--------------------
public function Profile():HasOne{
    return $this->hasOne(Profile::class);
}
public function articles():HasMany{
    return $this->hasMany(Article::class);
}
public function comments():HasMany{
    return $this->hasMany(Comment::class);
}
// ------------Accessors & Mutators----------
protected function name():Attribute{
    return Attribute::make(get:fn(string $value)=>ucfirst($value),);
}
protected function isAdmin():Attribute{
    return Attribute::make(get:fn()=>$this->role ===UserRole::ADMIN,);
}
// ---------------Scopes--------------
// get Roles
public function scopeOfRole(Builder $query,UserRole $role){
    return $query->where('role',$role->value);  
}
}
