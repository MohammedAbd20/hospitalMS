<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class nurse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'gender',
        'address',
        'qualification',
        'photo_path',
        'position',
        'registered',
    ];

    protected $casts = [
        'registered' => 'boolean',
    ];
}
