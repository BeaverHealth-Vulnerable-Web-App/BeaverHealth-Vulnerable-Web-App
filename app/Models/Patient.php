<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patient';
    protected $primaryKey = 'patient_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'ssn',
        'policy_number',
        'address',
        'is_employee',
    ];

    /**
     * Relationship to PatientFeedback.
     */
    public function feedback()
    {
        return $this->hasMany(PatientFeedback::class, 'patient_id', 'patient_id');
    }
}
