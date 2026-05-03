<?php

namespace Modules\Know\Models;

use Illuminate\Database\Eloquent\Model;

class KnowCategory extends Model
{
    protected $table = 'app_know_kategori';

    public $timestamps = false;

    protected $fillable = [
        'nama',
    ];
}
