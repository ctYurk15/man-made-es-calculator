<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalculationArchive extends Model
{
    use HasFactory;

    protected $table = 'calculations_archive';

    protected $fillable = [
        'organization_id',
        'year',
        'numeric_assessment',
        'text_assessment',
    ];

    /**
     * Зв'язок із таблицею organizations
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    /**
     * Зв'язок із таблицею scenario_calculations
     */
    public function scenarioCalculations()
    {
        return $this->hasMany(ScenarioCalculation::class, 'calculation_id');
    }
}
