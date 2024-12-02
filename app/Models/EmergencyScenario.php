<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyScenario extends Model
{
    use HasFactory;

    /**
     * Таблиця, пов'язана з моделлю.
     *
     * @var string
     */
    protected $table = 'emergency_scenarios';

    /**
     * Атрибути, які можна масово призначати.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'created_at',
        'updated_at',
    ];

    /**
     * Атрибути, які слід приховати при серіалізації.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Кастинг атрибутів до певних типів.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
