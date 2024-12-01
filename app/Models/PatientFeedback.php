<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientFeedback extends Model
{
    use HasFactory;

    protected $table = 'patient_feedback';

    protected $primaryKey = 'patient_feedback_id';

    protected $fillable = [
        'patient_id',
        'feedback',
    ];

    /**
     * Get the patient that owns the feedback.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}
