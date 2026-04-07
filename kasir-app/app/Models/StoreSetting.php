<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'jumlah_rak',
        'tingkat_rak',
        'kapasitas_per_rak'
    ];
}