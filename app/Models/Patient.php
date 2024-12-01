<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
    ];

    /**
     * Get the feedback for the patient.
     */
    public function feedback()
    {
        return $this->hasMany(PatientFeedback::class, 'patient_id', 'patient_id');
    }

    /**
     * Accessor for first_name to return capitalized.
     */
    public function getFirstNameAttribute($value)
    {
        return Str::title(strtolower($value));
    }

    /**
     * Accessor for last_name to return capitalized.
     */
    public function getLastNameAttribute($value)
    {
        return Str::title(strtolower($value));
    }
}
