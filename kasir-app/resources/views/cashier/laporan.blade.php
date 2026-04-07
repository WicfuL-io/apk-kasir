<x-app-layout>

<div class="p-8">

<div class="max-w-6xl mx-auto">

<!-- HEADER -->
<div class="flex justify-between items-center mb-6">

<h2 class="text-2xl font-bold text-indigo-600">
Data Struk Transaksi
</h2>

<input
type="text"
id="search"
placeholder="Cari invoice..."
class="border px-4 py-2 rounded-lg focus:ring-2 focus:ring-indigo-500">

</div>


<!-- CARD TABLE -->
<div class="bg-white shadow-xl rounded-xl overflow-hidden">

<table class="w-full text-sm">

<thead class="bg-indigo-50 text-gray-700">
<tr>
<th class="p-4 text-left">Invoice</th>
<th class="p-4 text-center">Total</th>
<th class="p-4 text-center">Metode</th>
<th class="p-4 text-center">Tanggal</th>
<th class="p-4 text-center">Aksi</th>
</tr>
</thead>

<tbody id="tableBody" class="divide-y">

@foreach($data as $d)
<tr class="hover:bg-gray-50 transition">

<td class="p-4 font-semibold text-gray-800">
{{ $d->invoice }}
</td>

<td class="p-4 text-center font-bold text-indigo-600">
Rp {{ number_format($d->total) }}
</td>

<td class="p-4 text-center">

@if($d->method == 'Cash')
<span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
Cash
</span>
@else
<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">
{{ $d->method }}
</span>
@endif

</td>

<td class="p-4 text-center text-gray-500">
{{ $d->created_at->format('d M Y H:i') }}
</td>

<td class="p-4 text-center">

<a href="/struk/{{ $d->id }}"
target="_blank"
class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm shadow">
Lihat
</a>

</td>

</tr>
@endforeach

</tbody>

</table>

</div>

</div>

</div>


<!-- SEARCH SCRIPT -->
<script>

document.getElementById("search").addEventListener("keyup", function(){

let value = this.value.toLowerCase()
let rows = document.querySelectorAll("#tableBody tr")

rows.forEach(row => {

let invoice = row.children[0].innerText.toLowerCase()

if(invoice.includes(value)){
row.style.display = ""
}else{
row.style.display = "none"
}

})

})

</script>

</x-app-layout>