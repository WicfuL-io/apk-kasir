<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{

public function autoLayout()
{
    $products = Product::all();

    foreach ($products as $product) {

        $total = \App\Models\TransactionItem::where('name',$product->name)->sum('qty');

        $category = strtolower($product->category);

        // 🚬 PRIORITAS KASIR
        if (in_array($category, ['rokok','kesehatan'])) {
            $location = 'A1';
        }

        // 🔥 LARIS → BELAKANG
        elseif ($total >= 50) {
            $location = 'C1';
        }

        // ⚖️ TENGAH
        elseif ($total >= 20) {
            $location = 'B1';
        }

        // 🐢 DEPAN
        else {
            $location = 'A2';
        }

        $product->update([
            'location' => $location
        ]);
    }
}

/*
==============================
HALAMAN INVENTORY
==============================
*/

public function index(Request $request)
{

$search = $request->search;

$query = Product::query();

/*
SEARCH BARANG
*/

if($search){

$query->where(function($q) use ($search){

$q->where('name','like','%'.$search.'%')
  ->orWhere('barcode','like','%'.$search.'%');

});

}


/*
PRODUK DISCOUNT
*/

$discountProducts = (clone $query)
                    ->where('discount','>',0)
                    ->orderBy('name','asc')
                    ->get();


/*
PRODUK TANPA DISCOUNT
*/

$products = (clone $query)
            ->where('discount',0)
            ->orderBy('name','asc')
            ->get();


return view('inventory.index',compact(
'products',
'discountProducts',
'search'
));

}



/*
==============================
HALAMAN TAMBAH BARANG
==============================
*/

public function create()
{
return view('inventory.create');
}



/*
==============================
SIMPAN BARANG
==============================
*/

public function store(Request $request)
{

$request->validate([

'barcode' => 'required|unique:products',
'name' => 'nullable|string|max:255',
'buy_price' => 'nullable|numeric|min:0',
'sell_price' => 'nullable|numeric|min:0',
'discount' => 'nullable|numeric|min:0|max:100'

]);

$image = null;

if($request->hasFile('image')){
$image = $request->file('image')->store('products','public');
}

Product::create([

'barcode'=>$request->barcode,
'name'=>$request->name,
'category'=>$request->category,

'stock_in'=>$request->stock_in ?? 0,
'stock_out'=>0,

'buy_price'=>$request->buy_price,
'sell_price'=>$request->sell_price,
'discount'=>$request->discount ?? 0,

'image'=>$image,
'image_url'=>$request->image_url,

'location' => 'C1'
]);

$this->autoLayout();
return redirect('/inventory')->with('success','Barang berhasil ditambahkan');

}



/*
==============================
EDIT BARANG
==============================
*/

public function edit($id)
{

$product = Product::findOrFail($id);

return view('inventory.edit',compact('product'));

}



/*
==============================
UPDATE BARANG
==============================
*/

public function update(Request $request,$id)
{

$product = Product::findOrFail($id);

$image = $product->image;

if($request->hasFile('image')){
$image = $request->file('image')->store('products','public');
}


/*
LOGIKA STOK
*/

$newStockIn = $product->stock_in + ($request->stock_in ?? 0);

$newStockOut = $product->stock_out + ($request->stock_out ?? 0);

$totalStock = $newStockIn - $newStockOut;

if($totalStock < 0){
return back()->with('error','Stok tidak mencukupi');
}


$product->update([

'barcode'=>$request->barcode,
'name'=>$request->name,
'category'=>$request->category,

'stock_in'=>$newStockIn,
'stock_out'=>$newStockOut,

'buy_price'=>$request->buy_price,
'sell_price'=>$request->sell_price,
'discount'=>$request->discount ?? 0,

'image'=>$image,
'image_url'=>$request->image_url

]);

return redirect('/inventory')->with('success','Barang berhasil diperbarui');

}



/*
==============================
HAPUS BARANG
==============================
*/

public function destroy($id)
{

Product::findOrFail($id)->delete();

return back()->with('success','Barang berhasil dihapus');

}



/*
==============================
FETCH API OPEN FOOD FACTS
==============================
*/

public function fetchProduct($barcode)
{

// 🔥 BERSIHKAN BARCODE (BIAR TIDAK ERROR)
$barcode = trim($barcode, '"\' ');
$barcode = preg_replace('/[^0-9]/', '', $barcode);

try {

$response = Http::timeout(10)
    ->acceptJson()
    ->get("https://world.openfoodfacts.org/api/v0/product/".$barcode.".json");

if(!$response->successful()){
    return response()->json([
        'success' => false,
        'error' => 'Gagal koneksi ke OpenFoodFacts'
    ], 500);
}

$data = $response->json();

// 🔥 VALIDASI RESMI API
if(($data['status'] ?? 0) == 1 && isset($data['product'])){

    $product = $data['product'];

    return response()->json([
        'success' => true,
        'name' => $product['product_name'] ?? null,
        'category' => $product['categories'] ?? null,
        'image_url' => $product['image_url'] ?? null,
        'barcode' => $barcode
    ]);
}

// 🔥 FALLBACK KE DATABASE LOKAL
$productLocal = Product::where('barcode',$barcode)->first();

if($productLocal){
    return response()->json([
        'success' => true,
        'name' => $productLocal->name,
        'category' => $productLocal->category,
        'image_url' => $productLocal->image_url,
        'barcode' => $barcode
    ]);
}

// 🔥 JIKA TIDAK ADA
return response()->json([
    'success' => false,
    'error' => 'Produk tidak ditemukan'
], 404);

} catch (\Exception $e){

return response()->json([
    'success' => false,
    'error' => 'Server error: '.$e->getMessage()
], 500);

}

}

}