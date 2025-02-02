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
        'policy_number',
        'address',
        'is_employee',
        'ssn',
    ];

    /**
     * Relationship to PatientFeedback.
     */
    public function feedback()
    {
        return $this->hasMany(PatientFeedback::class, 'patient_id', 'patient_id');
    }

        /**
     * Relationship to PatientFile.
     */
    public function files()
    {
        return $this->hasMany(PatientFile::class, 'patient_id', 'patient_id');
    }
}

