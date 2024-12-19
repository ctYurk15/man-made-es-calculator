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

    public function calculation()
    {
        return $this->belongsTo(CalculationArchive::class, 'calculation_id');
    }

    public function scenario()
    {
        return $this->belongsTo(EmergencyScenario::class, 'scenario_id');
    }
}
