<?php

namespace Modules\Drive\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Drive\Database\Factories\DriveFactory;

class Drive extends Model
{
    use HasFactory;
    protected $table = 'app_drive_list';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nama',
        'link',
        'jenis',
        'personal',
        'tim',
        'akses',
        'created_by',
        'updated_by',
    ];

    public function personalUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'personal');
    }

    public function timRole()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'tim');
    }

    // protected static function newFactory(): DriveFactory
    // {
    //     // return DriveFactory::new();
    // }
}
