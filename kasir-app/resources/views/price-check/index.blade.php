<x-app-layout>

<div class="p-10">

<div class="max-w-3xl mx-auto bg-white shadow-xl rounded-xl p-8">


<!-- SEARCH BAR -->

<div class="mb-6">

<input
type="text"
id="search"
placeholder="Scan barcode atau ketik nama barang..."
class="border p-3 w-full rounded-lg focus:ring-indigo-500">

</div>


<!-- SCAN BUTTON -->

<button
onclick="startScanner()"
class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded mb-6">

Scan Barcode Kamera

</button>


<div id="reader"></div>


<!-- RESULT -->

<div id="result" class="hidden mt-6">

<div class="flex gap-6 items-center">

<img id="image"
class="w-32 h-32 object-cover rounded-lg shadow">

<div>

<h2 id="name" class="text-2xl font-bold"></h2>

<p id="barcode" class="text-gray-500"></p>

</div>

</div>


<div class="grid grid-cols-2 gap-6 mt-8">


<div class="bg-gray-100 p-4 rounded-lg">

<p class="text-gray-500 text-sm">
Harga Jual
</p>

<p id="price"
class="text-xl font-bold text-indigo-600"></p>

</div>


<div class="bg-gray-100 p-4 rounded-lg">

<p class="text-gray-500 text-sm">
Discount
</p>

<p id="discount"
class="text-xl font-bold text-red-600"></p>

</div>


<div class="bg-gray-100 p-4 rounded-lg">

<p class="text-gray-500 text-sm">
Harga Setelah Discount
</p>

<p id="final"
class="text-xl font-bold text-green-600"></p>

</div>


<div class="bg-gray-100 p-4 rounded-lg">

<p class="text-gray-500 text-sm">
Stok
</p>

<p id="stock"
class="text-xl font-bold"></p>

</div>


</div>

</div>

</div>

</div>



<script src="https://unpkg.com/html5-qrcode"></script>

<script>

let input = document.getElementById("search");


input.addEventListener("keyup",function(){

fetchProduct(this.value);

});


function fetchProduct(search){

fetch("/cek-harga/search?search="+search)

.then(res=>res.json())

.then(data=>{

if(!data) return;

document.getElementById("result").classList.remove("hidden");


document.getElementById("name").innerText = data.name;
document.getElementById("barcode").innerText = data.barcode;


document.getElementById("price").innerText =
"Rp "+Number(data.sell_price).toLocaleString();


document.getElementById("discount").innerText =
data.discount+"%";


let final =
data.sell_price - (data.sell_price * data.discount / 100);


document.getElementById("final").innerText =
"Rp "+Number(final).toLocaleString();


let stock =
data.stock_in - data.stock_out;


document.getElementById("stock").innerText = stock;


if(data.image){

document.getElementById("image").src =
"/storage/"+data.image;

}else{

document.getElementById("image").src =
data.image_url;

}

})

}


function startScanner(){

const scanner = new Html5Qrcode("reader");

scanner.start(
{ facingMode: "environment" },
{
fps: 10,
qrbox: 250
},

(barcode)=>{

document.getElementById("search").value = barcode;

fetchProduct(barcode);

scanner.stop();

}

);

}

</script>

</x-app-layout>