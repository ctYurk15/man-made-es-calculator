<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyScenario extends Model
{
    use HasFactory;

    protected $table = 'emergency_scenarios';

    protected $fillable = [
        'name',
        'description',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
