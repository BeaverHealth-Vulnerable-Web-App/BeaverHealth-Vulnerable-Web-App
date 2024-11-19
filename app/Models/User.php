<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'username',
        'password',
        'is_admin',
        'request_records',
        'load_records',
        'view_employee_info',
        'sqli_on',
        'file_upload_on',
        'cmd_inject_on',
        'xss_reflected_on',
        'xss_stored_on',
        'idor_on',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'request_records' => 'boolean',
        'load_records' => 'boolean',
        'view_employee_info' => 'boolean',
        'sqli_on' => 'boolean',
        'file_upload_on' => 'boolean',
        'cmd_inject_on' => 'boolean',
        'xss_reflected_on' => 'boolean',
        'xss_stored_on' => 'boolean',
        'idor_on' => 'boolean',
    ];
}
