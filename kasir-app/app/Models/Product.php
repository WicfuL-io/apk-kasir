<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

use HasFactory;

protected $fillable = [
'location',

'barcode',
'name',
'category',

'stock_in',
'stock_out',

'buy_price',
'sell_price',

'discount',

'image',
'image_url'

];


/*
TOTAL STOCK
*/

public function getStockAttribute()
{

return $this->stock_in - $this->stock_out;

}


/*
FINAL PRICE AFTER DISCOUNT
*/

public function getFinalPriceAttribute()
{

$price = $this->sell_price ?? 0;

$discount = $this->discount ?? 0;

return $price - ($price * $discount / 100);

}

}