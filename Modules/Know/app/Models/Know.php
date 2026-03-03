<?php

namespace Modules\Know\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Know extends Model
{
    protected $table = 'app_know_daftar';

    protected $fillable = [
        'nama',
        'deskripsi',
        'link',
        'pic',
        'tanggal_pelaksanaan',
        'kategori',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'link' => 'array',
        'kategori' => 'array',
        'tanggal_pelaksanaan' => 'datetime',
    ];

    public function creator()
    {
        return $this->hasOne(User::class, 'username', 'created_by');
    }

    public function updater()
    {
        return $this->hasOne(User::class, 'username', 'updated_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = Auth::user()?->username ?? 'unknown';
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::user()?->username ?? 'unknown';
        });
    }
}
