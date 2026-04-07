<x-app-layout>

<x-slot name="header">
<h2 class="text-xl font-semibold text-indigo-600">
Edit Barang
</h2>
</x-slot>

<div class="p-8 flex justify-center">

<div class="bg-white shadow rounded-xl p-6 w-full max-w-3xl">

<form method="POST" action="/inventory/{{ $product->id }}" enctype="multipart/form-data">

@csrf
@method('PUT')


<!-- BARCODE -->

<label class="block mb-1 font-semibold">
Barcode
</label>

<input
type="text"
name="barcode"
value="{{ $product->barcode }}"
class="border p-2 w-full mb-4 rounded"
required>



<!-- NAMA -->

<label class="block mb-1 font-semibold">
Nama Produk
</label>

<input
type="text"
name="name"
value="{{ $product->name }}"
class="border p-2 w-full mb-4 rounded">



<!-- KATEGORI -->

<label class="block mb-1 font-semibold">
Kategori
</label>

<input
type="text"
name="category"
value="{{ $product->category }}"
class="border p-2 w-full mb-4 rounded">



<!-- GAMBAR SEKARANG -->

<label class="block mb-1 font-semibold">
Gambar Sekarang
</label>

@if($product->image)

<img
src="{{ asset('storage/'.$product->image) }}"
class="w-24 mb-4 rounded shadow">

@elseif($product->image_url)

<img
src="{{ $product->image_url }}"
class="w-24 mb-4 rounded shadow">

@endif



<!-- GANTI GAMBAR -->

<label class="block mb-1 font-semibold">
Upload Gambar Baru
</label>

<input
type="file"
name="image"
class="border p-2 w-full mb-4 rounded">



<!-- IMAGE URL -->

<label class="block mb-1 font-semibold">
Image URL
</label>

<input
type="text"
id="image_url"
name="image_url"
value="{{ $product->image_url }}"
class="border p-2 w-full mb-3 rounded"
oninput="previewImage()">



<!-- PREVIEW IMAGE -->

<img
id="preview"
class="w-24 mb-4 rounded shadow hidden">



<!-- STOK SEKARANG -->

<label class="block mb-1 font-semibold">
Stok Sekarang
</label>

<input
type="text"
value="{{ $product->stock }}"
class="border p-2 w-full mb-4 bg-gray-100 rounded"
readonly>



<!-- TAMBAH STOK -->

<label class="block mb-1 font-semibold text-green-600">
Tambah Barang Masuk
</label>

<input
type="number"
name="stock_in"
placeholder="Tambah stok"
class="border p-2 w-full mb-4 rounded"
min="0">



<!-- KURANGI STOK -->

<label class="block mb-1 font-semibold text-red-600">
Barang Keluar
</label>

<input
type="number"
name="stock_out"
placeholder="Kurangi stok"
class="border p-2 w-full mb-4 rounded"
min="0">



<!-- HARGA -->

<div class="grid grid-cols-2 gap-4 mb-4">

<div>

<label class="block mb-1 font-semibold">
Harga Beli
</label>

<input
type="number"
name="buy_price"
value="{{ $product->buy_price }}"
class="border p-2 w-full rounded"
min="0">

</div>

<div>

<label class="block mb-1 font-semibold">
Harga Jual
</label>

<input
type="number"
name="sell_price"
value="{{ $product->sell_price }}"
class="border p-2 w-full rounded"
min="0">

</div>

</div>



<!-- DISCOUNT -->

<label class="block mb-1 font-semibold">
Discount (%)
</label>

<input
type="number"
name="discount"
value="{{ $product->discount }}"
class="border p-2 w-full rounded mb-6"
min="0"
max="100">



<!-- BUTTON -->

<div class="flex gap-3">

<a
href="/inventory"
class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded shadow">

Kembali

</a>

<button
class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded shadow">

Update Barang

</button>

</div>

</form>

</div>

</div>



<script>

function previewImage(){

let url = document.getElementById("image_url").value;

let img = document.getElementById("preview");

if(url){

img.src = url;
img.classList.remove("hidden");

}else{

img.classList.add("hidden");

}

}

</script>

</x-app-layout>