<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientFile extends Model
{
    use HasFactory;

    protected $table = 'patient_files';
    protected $primaryKey = 'file_id';

    protected $fillable = [
        'patient_id',
        'filename',
        'path',
    ];

    /**
     * Relationship to Patient.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}