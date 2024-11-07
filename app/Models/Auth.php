<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Auth extends Model
{
    use HasFactory , Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    /**
     * Masquer certains attributs dans la réponse JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
