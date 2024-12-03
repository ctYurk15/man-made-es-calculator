<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    /**
     * Масив для масового заповнення.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'organization_type_id',
    ];

    /**
     * Зв'язок з типом організації (один до багатьох).
     */
    public function type()
    {
        return $this->belongsTo(OrganizationType::class, 'organization_type_id');
    }
}
