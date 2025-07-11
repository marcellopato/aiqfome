<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Client extends Authenticatable
{
    use HasFactory, HasRoles;

    protected $fillable = [
        'name',
        'email',
    ];

    protected $guard_name = 'web'; // ou 'sanctum' se for o caso

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
}
