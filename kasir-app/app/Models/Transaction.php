<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['invoice','total','pay','change','method'];

    public function items(){
        return $this->hasMany(TransactionItem::class);
    }
}
