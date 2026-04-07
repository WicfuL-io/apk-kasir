<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class PriceCheckController extends Controller
{

public function index()
{
return view('price-check.index');
}


public function search(Request $request)
{

$search = $request->search;

$product = Product::where('barcode',$search)
        ->orWhere('name','like','%'.$search.'%')
        ->first();

return response()->json($product);

}

}