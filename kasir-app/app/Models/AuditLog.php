<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',

        'action',
        'model',
        'model_id',

        'description',

        'new_values',

        'ip_address',
        'method',
        'url',

        'status',
        'level',

        'duration'
    ];

    protected $casts = [
        'new_values' => 'array',
    ];

    /*
    =====================================
    RELATION
    =====================================
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    =====================================
    🔥 ICON (UNTUK UI)
    =====================================
    */
    public function getIconAttribute()
    {
        return match ($this->model) {
            'Transaction' => '🧾',
            'Payment' => '💳',
            'Product' => '📦',
            'Auth' => '🔐',
            default => '📝'
        };
    }

    /*
    =====================================
    🔥 USER NAME SAFE
    =====================================
    */
    public function getUserNameAttribute()
    {
        return $this->user->name ?? 'System';
    }

    /*
    =====================================
    🔥 FORMAT WAKTU
    =====================================
    */
    public function getTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}