<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Зв'язок з організаціями (один до багатьох).
     */
    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }

    /**
     * Зв’язок багато-до-багатьох з `EmergencySituation`.
     */
    public function emergencyScenarios()
    {
        return $this->belongsToMany(EmergencyScenario::class, 'emergency_scenario_organization_type');
    }
}
