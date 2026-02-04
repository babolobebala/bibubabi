<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'nip',
        'nip_baru',
        'username',
        'nama',
        'email_bps',
        'email_gmail',
        'golongan',
        'jabatan',
        'url_foto'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
