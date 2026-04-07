<x-app-layout>

<x-slot name="header">
<h2 class="text-xl font-semibold text-indigo-600">
Tambah Barang
</h2>
</x-slot>

<div class="p-8 flex justify-center">

<div class="bg-white shadow rounded-xl p-6 w-full max-w-3xl">

<form method="POST" action="/inventory" enctype="multipart/form-data">

@csrf


<!-- SCAN BARCODE -->

<label class="block mb-2 font-semibold text-gray-700">
Scan Barcode
</label>

<button type="button"
onclick="startScanner()"
class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow mb-3">

Scan dengan Kamera

</button>

<div id="reader" class="mb-6"></div>


<!-- BARCODE -->

<label class="block mb-1 font-semibold">
Barcode
</label>

<input type="text"
id="barcode"
name="barcode"
class="border p-2 w-full mb-4 rounded"
placeholder="Scan atau masukkan barcode"
required>


<!-- NAMA -->

<label class="block mb-1 font-semibold">
Nama Produk
</label>

<input type="text"
id="name"
name="name"
class="border p-2 w-full mb-4 rounded">


<!-- KATEGORI -->

<label class="block mb-1 font-semibold">
Kategori
</label>

<input type="text"
id="category"
name="category"
class="border p-2 w-full mb-4 rounded">


<!-- IMAGE URL -->

<label class="block mb-1 font-semibold">
Image URL
</label>

<input type="text"
id="image_url"
name="image_url"
class="border p-2 w-full mb-3 rounded"
oninput="previewImage()">


<!-- PREVIEW IMAGE -->

<img id="preview"
class="w-28 mb-4 hidden rounded shadow">


<!-- UPLOAD IMAGE -->

<label class="block mb-1 font-semibold">
Upload Gambar
</label>

<input type="file"
name="image"
class="border p-2 w-full mb-6 rounded">


<!-- BARANG MASUK -->

<label class="block mb-1 font-semibold">
Barang Masuk
</label>

<input type="number"
name="stock_in"
placeholder="Jumlah barang masuk"
class="border p-2 w-full rounded mb-6"
min="0"
required>


<!-- HARGA -->

<div class="grid grid-cols-2 gap-4 mb-4">

<div>

<label class="block mb-1 font-semibold">
Harga Beli
</label>

<input type="number"
name="buy_price"
placeholder="Harga beli dari supplier"
class="border p-2 w-full rounded"
min="0">

</div>

<div>

<label class="block mb-1 font-semibold">
Harga Jual
</label>

<input type="number"
name="sell_price"
placeholder="Harga jual ke pelanggan"
class="border p-2 w-full rounded"
min="0">

</div>

</div>


<!-- DISCOUNT -->

<label class="block mb-1 font-semibold">
Discount (%)
</label>

<input type="number"
name="discount"
placeholder="Contoh: 10"
class="border p-2 w-full rounded mb-6"
min="0"
max="100">


<!-- BUTTON -->

<div class="flex gap-3 mt-6">

<a href="/inventory"
class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded shadow">

Kembali

</a>

<button
class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded shadow">

Simpan Barang

</button>

</div>

</form>

</div>

</div>



<!-- BARCODE SCANNER -->

<script src="https://unpkg.com/html5-qrcode"></script>

<script>

let scanner;

function startScanner(){

if(scanner){
scanner.stop();
}

scanner = new Html5Qrcode("reader");

scanner.start(
{ facingMode: "environment" },
{
fps: 10,
qrbox: 250
},

(barcode) => {

document.getElementById("barcode").value = barcode;

scanner.stop();

fetchProduct(barcode);

},

(error) => {}

);

}


function fetchProduct(barcode){

if(!barcode) return;

// 🔥 kasih loading biar UX bagus
document.getElementById("name").value = "Loading...";

fetch("/api/product/" + barcode) // ✅ FIX DI SINI

.then(res => res.json())

.then(data => {

if(data.success){

document.getElementById("name").value = data.name ?? "";

document.getElementById("category").value = data.category ?? "";

document.getElementById("image_url").value = data.image_url ?? "";

previewImage();

}else{

document.getElementById("name").value = "";

alert(data.error || "Produk tidak ditemukan");

}

})

.catch(err => {

console.error(err);

alert("Gagal mengambil data dari server");

});

}


document.getElementById("barcode").addEventListener("change", function(){

fetchProduct(this.value);

});


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