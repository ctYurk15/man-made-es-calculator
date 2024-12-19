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

    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }

    public function emergencyScenarios()
    {
        return $this->belongsToMany(EmergencyScenario::class, 'emergency_scenario_organization_type');
    }
}
