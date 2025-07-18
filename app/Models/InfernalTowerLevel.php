<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfernalTowerLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'level',
        'team',
        'team_cp',
        'rewards',
        'trainer_avatar',
    ];

    protected $casts = [
        'team' => 'array',
        'rewards' => 'array',
    ];
}
