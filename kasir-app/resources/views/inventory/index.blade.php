<x-app-layout>

<div class="p-8">

<a href="/inventory/create"
class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded shadow">
Tambah Barang
</a>


<!-- SEARCH -->

<form method="GET" class="mt-6 mb-6 flex gap-2">

<input
type="text"
name="search"
value="{{ $search }}"
placeholder="Cari nama atau barcode..."
class="border p-2 rounded w-72">

<button
class="bg-gray-700 text-white px-4 py-2 rounded">
Search
</button>

</form>



<!-- ======================= -->
<!-- PRODUK DISCOUNT -->
<!-- ======================= -->

<h2 class="text-lg font-bold text-red-600 mb-4">
Produk Discount
</h2>

<div class="bg-white shadow rounded-xl overflow-x-auto mb-10">

<table class="min-w-full text-sm text-left border-collapse">

<thead class="bg-red-100 sticky top-0 z-10">

<tr>

<th class="p-3 border whitespace-nowrap">No</th>
<th class="p-3 border whitespace-nowrap">Gambar</th>
<th class="p-3 border whitespace-nowrap">Barcode</th>
<th class="p-3 border whitespace-nowrap">Nama Barang</th>
<th class="p-3 border whitespace-nowrap">Kategori</th>
<th class="p-3 border whitespace-nowrap">Total Stok</th>
<th class="p-3 border whitespace-nowrap">Stok Masuk</th>
<th class="p-3 border whitespace-nowrap">Stok Keluar</th>
<th class="p-3 border whitespace-nowrap">Harga Beli</th>
<th class="p-3 border whitespace-nowrap">Harga Jual</th>
<th class="p-3 border whitespace-nowrap">Discount</th>
<th class="p-3 border whitespace-nowrap">Harga Final</th>
<th class="p-3 border whitespace-nowrap text-center">Aksi</th>

</tr>

</thead>

<tbody>

@foreach($discountProducts as $product)

<tr class="hover:bg-gray-50">

<td class="p-3 border">{{ $loop->iteration }}</td>

<td class="p-3 border">

@if($product->image)

<img src="{{ asset('storage/'.$product->image) }}"
class="w-12 h-12 object-cover rounded">

@elseif($product->image_url)

<img src="{{ $product->image_url }}"
class="w-12 h-12 object-cover rounded">

@else

<img src="https://via.placeholder.com/60"
class="w-12 h-12 object-cover rounded">

@endif

</td>

<td class="p-3 border font-mono">
{{ $product->barcode }}
</td>

<td class="p-3 border font-semibold">
{{ $product->name }}
</td>

<td class="p-3 border">
{{ $product->category }}
</td>

<td class="p-3 border text-indigo-600 font-bold">
{{ $product->stock }}
</td>

<td class="p-3 border text-green-600">
+ {{ $product->stock_in }}
</td>

<td class="p-3 border text-red-600">
- {{ $product->stock_out }}
</td>

<td class="p-3 border">
Rp {{ number_format($product->buy_price) }}
</td>

<td class="p-3 border">
Rp {{ number_format($product->sell_price) }}
</td>

<td class="p-3 border text-red-600 font-bold">
{{ $product->discount }}%
</td>

<td class="p-3 border text-green-700 font-bold">
Rp {{ number_format($product->final_price) }}
</td>

<td class="p-3 border">

<div class="flex gap-2 justify-center">

<a href="/inventory/{{ $product->id }}/edit"
class="bg-yellow-400 hover:bg-yellow-500 px-3 py-1 rounded text-white">
Edit
</a>

<form method="POST"
action="/inventory/{{ $product->id }}"
onsubmit="return confirm('Yakin ingin menghapus barang ini?')">

@csrf
@method('DELETE')

<button
class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-white">
Hapus
</button>

</form>

</div>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>



<!-- ======================= -->
<!-- SEMUA PRODUK -->
<!-- ======================= -->

<h2 class="text-lg font-bold text-indigo-600 mb-4">
Semua Produk
</h2>

<div class="bg-white shadow rounded-xl overflow-x-auto">

<table class="min-w-full text-sm text-left border-collapse">

<thead class="bg-gray-100 sticky top-0 z-10">

<tr>

<th class="p-3 border whitespace-nowrap">No</th>
<th class="p-3 border whitespace-nowrap">Gambar</th>
<th class="p-3 border whitespace-nowrap">Barcode</th>
<th class="p-3 border whitespace-nowrap">Nama Barang</th>
<th class="p-3 border whitespace-nowrap">Kategori</th>
<th class="p-3 border whitespace-nowrap">Total Stok</th>
<th class="p-3 border whitespace-nowrap">Stok Masuk</th>
<th class="p-3 border whitespace-nowrap">Stok Keluar</th>
<th class="p-3 border whitespace-nowrap">Harga Beli</th>
<th class="p-3 border whitespace-nowrap">Harga Jual</th>
<th class="p-3 border whitespace-nowrap">Discount</th>
<th class="p-3 border whitespace-nowrap">Harga Final</th>
<th class="p-3 border whitespace-nowrap text-center">Aksi</th>

</tr>

</thead>

<tbody>

@foreach($products as $product)

<tr class="hover:bg-gray-50">

<td class="p-3 border">{{ $loop->iteration }}</td>

<td class="p-3 border">

@if($product->image)

<img src="{{ asset('storage/'.$product->image) }}"
class="w-12 h-12 object-cover rounded">

@elseif($product->image_url)

<img src="{{ $product->image_url }}"
class="w-12 h-12 object-cover rounded">

@else

<img src="https://via.placeholder.com/60"
class="w-12 h-12 object-cover rounded">

@endif

</td>

<td class="p-3 border font-mono">
{{ $product->barcode }}
</td>

<td class="p-3 border font-semibold">
{{ $product->name }}
</td>

<td class="p-3 border">
{{ $product->category }}
</td>

<td class="p-3 border text-indigo-600 font-bold">
{{ $product->stock }}
</td>

<td class="p-3 border text-green-600">
+ {{ $product->stock_in }}
</td>

<td class="p-3 border text-red-600">
- {{ $product->stock_out }}
</td>

<td class="p-3 border">
Rp {{ number_format($product->buy_price) }}
</td>

<td class="p-3 border">
Rp {{ number_format($product->sell_price) }}
</td>

<td class="p-3 border">
{{ $product->discount }}%
</td>

<td class="p-3 border text-green-700 font-bold">
Rp {{ number_format($product->final_price) }}
</td>

<td class="p-3 border">

<div class="flex gap-2 justify-center">

<a href="/inventory/{{ $product->id }}/edit"
class="bg-yellow-400 hover:bg-yellow-500 px-3 py-1 rounded text-white">
Edit
</a>

<form method="POST"
action="/inventory/{{ $product->id }}"
onsubmit="return confirm('Yakin ingin menghapus barang ini?')">

@csrf
@method('DELETE')

<button
class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-white">
Hapus
</button>

</form>

</div>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

</x-app-layout>