<x-app-layout>

<div class="py-10 bg-gray-100 min-h-screen">
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

<!-- STAT -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

<div class="bg-white shadow rounded-xl p-6">
<p>Total Produk</p>
<p class="text-2xl font-bold text-indigo-600">{{ $totalProduk }}</p>
</div>

<div class="bg-white shadow rounded-xl p-6">
<p>Total Stok</p>
<p class="text-2xl font-bold text-blue-600">{{ $totalStok }}</p>
</div>

<div class="bg-white shadow rounded-xl p-6">
<p>Penjualan Hari Ini</p>
<p class="text-2xl font-bold text-green-600">
Rp {{ number_format($penjualanHariIni,0,',','.') }}
</p>
</div>

<div class="bg-white shadow rounded-xl p-6">
<p>Total Transaksi</p>
<p class="text-2xl font-bold text-purple-600">{{ $totalTransaksi }}</p>
</div>

</div>

<!-- 🔥 PREDIKSI + TREND -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

<div class="bg-yellow-100 shadow rounded-xl p-6">
<p>Prediksi Besok</p>
<p class="text-2xl font-bold text-yellow-600">
Rp {{ number_format($prediksiBesok ?? 0,0,',','.') }}
</p>
</div>

<div class="bg-white shadow rounded-xl p-6">
<p>Trend Penjualan</p>

@if(($trend ?? '') == 'up')
<p class="text-green-600 text-xl">🔼 Naik</p>
@elseif(($trend ?? '') == 'down')
<p class="text-red-600 text-xl">🔽 Turun</p>
@else
<p class="text-gray-600 text-xl">➖ Stabil</p>
@endif

</div>

</div>

<!-- FILTER -->
<div class="flex justify-end mt-6">
<select onchange="changeFilter(this.value)" class="border rounded px-3 py-1">
<option value="daily" {{ $type == 'daily' ? 'selected' : '' }}>Harian</option>
<option value="monthly" {{ $type == 'monthly' ? 'selected' : '' }}>Bulanan</option>
<option value="yearly" {{ $type == 'yearly' ? 'selected' : '' }}>Tahunan</option>
</select>
</div>

<!-- GRAFIK -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-6">

<!-- SALES -->
<div class="bg-white p-6 rounded-xl shadow">
<h3 class="font-semibold mb-4">Grafik Penjualan</h3>
<canvas id="salesChart"></canvas>
</div>

<!-- STOCK -->
<div class="bg-white p-6 rounded-xl shadow">
<h3 class="font-semibold mb-4">Grafik Stok</h3>
<canvas id="stockChart"></canvas>
</div>

</div>

<!-- PRODUK + NOTIF -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-10">

<!-- PRODUK -->
<div class="bg-white p-6 rounded-xl shadow">
<h3 class="font-semibold mb-4">Produk Terlaris</h3>

<table class="w-full">
<tr class="border-b">
<th>Produk</th>
<th>Terjual</th>
</tr>

@foreach($produkTerlaris as $item)
<tr>
<td>{{ $item->name }}</td>
<td>{{ $item->total }}</td>
</tr>
@endforeach

</table>
</div>

<!-- SMART ALERT -->
<div class="bg-white p-6 rounded-xl shadow">
<h3 class="font-semibold mb-4">Smart Stok Alert</h3>

<ul>
@foreach($prediksiStok ?? [] as $item)
<li class="text-red-600">
⚠ {{ $item['name'] }} akan habis dalam {{ $item['hari'] }} hari
</li>
@endforeach
</ul>

</div>

</div>

<!-- 📦 RESTOCK -->
<div class="bg-white p-6 rounded-xl shadow mt-6">
<h3 class="font-semibold mb-4">Rekomendasi Restock</h3>

<ul>
@foreach($restock ?? [] as $item)
<li>
📦 {{ $item['name'] }} → tambah {{ $item['qty'] }} pcs
</li>
@endforeach
</ul>

</div>

<!-- TRANSAKSI -->
<div class="bg-white p-6 rounded-xl shadow mt-10">

<h3 class="font-semibold mb-4">Transaksi Terakhir</h3>

<table class="w-full">
<tr class="border-b">
<th>ID</th>
<th>Total</th>
</tr>

@foreach($transaksiTerakhir as $trx)
<tr>
<td>{{ $trx->invoice }}</td>
<td>Rp {{ number_format($trx->total,0,',','.') }}</td>
</tr>
@endforeach

</table>

</div>

<div class="bg-indigo-100 p-6 rounded-xl shadow mt-6">
<h3 class="font-semibold mb-4">⚙️ Konfigurasi Rak</h3>

<form action="{{ route('setting.update') }}" method="POST">
@csrf

<div class="grid grid-cols-3 gap-4">

<div>
<label>Jumlah Rak</label>
<input type="number" name="jumlah_rak" value="{{ $setting->jumlah_rak ?? 3 }}"
class="w-full border rounded px-2 py-1">
</div>

<div>
<label>Tingkat Rak</label>
<input type="number" name="tingkat_rak" value="{{ $setting->tingkat_rak ?? 3 }}"
class="w-full border rounded px-2 py-1">
</div>

<div>
<label>Kapasitas / Rak</label>
<input type="number" name="kapasitas_per_rak" value="{{ $setting->kapasitas_per_rak ?? 20 }}"
class="w-full border rounded px-2 py-1">
</div>

</div>

<button class="mt-4 bg-indigo-600 text-white px-4 py-2 rounded">
Simpan
</button>

@if(session('success'))
<div class="bg-green-200 text-green-800 p-3 rounded mb-4">
    {{ session('success') }}
</div>
@endif

</form>
</div>

<div class="bg-white p-6 rounded-xl shadow mt-6">
<h3 class="font-semibold mb-4">🧱 Layout Rak Toko</h3>

<div class="grid grid-cols-{{ $setting->tingkat_rak ?? 3 }} gap-3">

@foreach($layout ?? [] as $slot)

<div class="p-4 text-center rounded-xl shadow
@if($slot['zone']=='front') bg-green-200
@elseif($slot['zone']=='middle') bg-yellow-200
@else bg-red-200
@endif
">

<p class="font-bold">{{ $slot['kode'] }}</p>

@foreach($rackMap[$slot['kode']] ?? [] as $p)
<p class="text-xs">📦 {{ $p->name }}</p>
@endforeach

</div>

@endforeach

</div>
</div>


</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function changeFilter(type) {
    window.location.href = '?type=' + type;
}

document.addEventListener("DOMContentLoaded", function () {

    const labels = {!! json_encode($labels ?? []) !!};
    const salesData = {!! json_encode($salesData ?? []) !!};
    const stokKeluar = {!! json_encode($stokKeluar ?? []) !!};
    const stokMasuk = {!! json_encode($stokMasuk ?? []) !!};
    const forecastData = {!! json_encode($forecastData ?? []) !!};

    // ===============================
    // SALES CHART (REAL + AI)
    // ===============================
    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Penjualan Real',
                    data: salesData,
                    borderWidth: 3,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#3b82f6'
                },
                {
                    label: 'Prediksi AI',
                    data: forecastData,
                    borderDash: [6,6],
                    borderWidth: 2,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.raw.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // ===============================
    // STOCK CHART
    // ===============================
    new Chart(document.getElementById('stockChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
            {
                label: 'Barang Keluar',
                data: stokKeluar,
                backgroundColor: '#ef4444',
                borderWidth: 1
            },
            {
                label: 'Barang Masuk',
                data: stokMasuk,
                backgroundColor: '#22c55e',
                borderWidth: 1
            }
        ]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.raw + ' item';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

});
</script>

</x-app-layout>