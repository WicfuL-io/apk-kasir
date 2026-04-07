<x-app-layout>

<div class="p-10 bg-gray-50 min-h-screen">

<h1 class="text-3xl font-bold text-gray-800 mb-6">
📊 Aktivitas Sistem
</h1>

<!-- 🔍 SEARCH + FILTER -->
<form method="GET" class="mb-6 flex gap-2">

<input type="text" name="search" value="{{ request('search') }}"
placeholder="Cari aktivitas..."
class="px-3 py-2 border rounded w-full">

<select name="model" class="px-3 py-2 border rounded">
<option value="ALL">Semua</option>
<option value="Transaction" {{ request('model')=='Transaction'?'selected':'' }}>Transaksi</option>
<option value="Product" {{ request('model')=='Product'?'selected':'' }}>Produk</option>
<option value="Payment" {{ request('model')=='Payment'?'selected':'' }}>Payment</option>
<option value="Auth" {{ request('model')=='Auth'?'selected':'' }}>Auth</option>
</select>

<button class="px-4 py-2 bg-blue-500 text-white rounded">
Filter
</button>

</form>

<div class="max-w-3xl mx-auto">

<div class="relative border-l-2 border-gray-200">

@forelse($logs as $log)

<a href="{{ route('audit-log.show', $log->id) }}" class="block mb-6 ml-6">

<!-- DOT -->
<span class="absolute -left-3 flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full ring-4 ring-white">
    {{ $log->icon }}
</span>

<!-- CARD -->
<div class="bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition">

<div class="flex justify-between">

<div class="font-semibold text-gray-800">
    {{ $log->description }}
</div>

<span class="text-xs px-2 py-1 rounded
{{ $log->status === 'SUCCESS' 
    ? 'bg-green-100 text-green-700' 
    : 'bg-red-100 text-red-700' }}">
    {{ $log->status }}
</span>

</div>

<div class="text-xs text-gray-400 mt-1 flex justify-between">

<span>{{ $log->user_name }}</span>
<span>{{ $log->time }}</span>

</div>

</div>

</a>

@empty

<p class="text-center text-gray-400 py-10">
    Tidak ada aktivitas ditemukan
</p>

@endforelse

</div>

<!-- 🔥 PAGINATION -->
<div class="mt-6">
{{ $logs->links() }}
</div>

</div>

</div>

</x-app-layout>