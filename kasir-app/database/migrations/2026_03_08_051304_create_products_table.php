<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up(): void
{

Schema::create('products', function (Blueprint $table) {

$table->id();

$table->string('barcode')->unique();

$table->string('name')->nullable();

$table->string('category')->nullable();


/*
STOCK SYSTEM
*/

$table->integer('stock_in')->default(0);
$table->integer('stock_out')->default(0);


/*
PRICE
*/

$table->integer('buy_price')->nullable();

$table->integer('sell_price')->nullable();


/*
DISCOUNT
*/

$table->integer('discount')->default(0);


/*
IMAGE
*/

$table->string('image')->nullable();

$table->string('image_url')->nullable();


$table->timestamps();

});

}

public function down(): void
{

Schema::dropIfExists('products');

}

};