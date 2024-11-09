<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $fillable = [
        'administrator',
        'records_request',
        'records_add',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
