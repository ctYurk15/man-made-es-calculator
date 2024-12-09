<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScenarioCalculation extends Model
{
    use HasFactory;

    protected $table = 'scenario_calculations';

    protected $fillable = [
        'calculation_id',
        'scenario_id',
        'numeric_assessment',
        'text_assessment',
    ];

    /**
     * Зв'язок із таблицею calculations_archive
     */
    public function calculation()
    {
        return $this->belongsTo(CalculationArchive::class, 'calculation_id');
    }

    /**
     * Зв'язок із таблицею emergency_scenarios
     */
    public function scenario()
    {
        return $this->belongsTo(EmergencyScenario::class, 'scenario_id');
    }
}
