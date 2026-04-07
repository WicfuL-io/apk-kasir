<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\StoreSetting;

class DashboardController extends Controller
{
    public function updateSetting(Request $request)
    {
        $setting = StoreSetting::first();

        if(!$setting){
            StoreSetting::create($request->all());
        } else {
            $setting->update($request->all());
        }

        return back()->with('success','Setting berhasil disimpan');
    }
    private function getRackCode($i){
        $result = '';

        while ($i >= 0) {
            $result = chr($i % 26 + 65) . $result;
            $i = floor($i / 26) - 1;
        }

        return $result;
    }
    
    public function index()
    {
        $type = request('type', 'daily');

        /*
        ======================================
        STATISTIK
        ======================================
        */
        $totalProduk = Product::count();

        $totalStok = Product::selectRaw('SUM(stock_in - stock_out) as total')
            ->value('total') ?? 0;

        $penjualanHariIni = Transaction::whereDate('created_at', Carbon::today())
            ->sum('total');

        $totalTransaksi = Transaction::count();


        /*
        ======================================
        GRAFIK
        ======================================
        */
        $labels = [];
        $salesData = [];
        $stokKeluar = [];
        $stokMasuk = [];

        if ($type === 'daily') {

            $minDate = Transaction::min(DB::raw('DATE(created_at)'));
            $maxDate = Transaction::max(DB::raw('DATE(created_at)'));

            if ($minDate && $maxDate) {

                $start = Carbon::parse($minDate);
                $end = Carbon::parse($maxDate);

                $salesRaw = DB::table('transactions')
                    ->select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(total) as total')
                    )
                    ->groupBy('date')
                    ->pluck('total', 'date');

                $qtyRaw = DB::table('transaction_items')
                    ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                    ->select(
                        DB::raw('DATE(transactions.created_at) as date'),
                        DB::raw('SUM(transaction_items.qty) as total')
                    )
                    ->groupBy('date')
                    ->pluck('total', 'date');

                // 🔥 STOK MASUK
                $masukRaw = DB::table('products')
                    ->select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(stock_in) as total')
                    )
                    ->groupBy('date')
                    ->pluck('total', 'date');

                $current = $start->copy();

                while ($current <= $end) {

                    $date = $current->format('Y-m-d');

                    $labels[] = $current->format('d M');

                    $salesData[] = $salesRaw[$date] ?? 0;
                    $stokKeluar[] = $qtyRaw[$date] ?? 0;
                    $stokMasuk[]  = $masukRaw[$date] ?? 0;

                    $current->addDay();
                }
            }

        } elseif ($type === 'monthly') {

            $salesRaw = Transaction::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(total) as total')
            )->groupBy('month')->pluck('total', 'month');

            $qtyRaw = DB::table('transaction_items')
                ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                ->select(
                    DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m") as month'),
                    DB::raw('SUM(transaction_items.qty) as total')
                )
                ->groupBy('month')
                ->pluck('total', 'month');

            // 🔥 STOK MASUK
            $masukRaw = Product::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(stock_in) as total')
            )->groupBy('month')->pluck('total', 'month');

            for ($i = 11; $i >= 0; $i--) {

                $month = Carbon::now()->subMonths($i)->format('Y-m');

                $labels[] = Carbon::parse($month . '-01')->format('M');

                $salesData[] = $salesRaw[$month] ?? 0;
                $stokKeluar[] = $qtyRaw[$month] ?? 0;
                $stokMasuk[]  = $masukRaw[$month] ?? 0;
            }

        } else {

            $salesRaw = Transaction::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(total) as total')
            )->groupBy('year')->pluck('total', 'year');

            $qtyRaw = DB::table('transaction_items')
                ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                ->select(
                    DB::raw('YEAR(transactions.created_at) as year'),
                    DB::raw('SUM(transaction_items.qty) as total')
                )
                ->groupBy('year')
                ->pluck('total', 'year');

            // 🔥 STOK MASUK
            $masukRaw = Product::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(stock_in) as total')
            )->groupBy('year')->pluck('total', 'year');

            for ($i = 4; $i >= 0; $i--) {

                $year = Carbon::now()->subYears($i)->format('Y');

                $labels[] = $year;

                $salesData[] = $salesRaw[$year] ?? 0;
                $stokKeluar[] = $qtyRaw[$year] ?? 0;
                $stokMasuk[]  = $masukRaw[$year] ?? 0;
            }
        }


        /*
        ======================================
        PRODUK TERLARIS
        ======================================
        */
        $produkTerlaris = TransactionItem::select('name', DB::raw('SUM(qty) as total'))
            ->groupBy('name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();


        /*
        ======================================
        STOK RENDAH
        ======================================
        */
        $stokRendah = Product::select('*', DB::raw('(stock_in - stock_out) as stock'))
            ->having('stock','<=',5)
            ->get();


        /*
        ======================================
        TRANSAKSI TERAKHIR
        ======================================
        */
        $transaksiTerakhir = Transaction::latest()->limit(5)->get();


        /*
        ======================================
        🔥 AI / PREDIKSI
        ======================================
        */

        $avgSales = collect($salesData)->avg() ?? 0;
        $prediksiBesok = round($avgSales);

        $forecastData = [];
        foreach ($salesData as $val) {
            $forecastData[] = round($avgSales);
        }

        $trend = 'stable';
        if (count($salesData) > 1) {
            $last = end($salesData);
            $prev = prev($salesData);

            if ($last > $prev) $trend = 'up';
            elseif ($last < $prev) $trend = 'down';
        }

        $prediksiStok = [];
        $restock = [];

        foreach ($stokRendah as $item) {

            $avgTerjual = TransactionItem::where('name', $item->name)
                ->avg('qty') ?? 1;

            $hariHabis = $avgTerjual > 0 
                ? ceil($item->stock / $avgTerjual) 
                : 0;

            $prediksiStok[] = [
                'name' => $item->name,
                'hari' => $hariHabis
            ];

            $restock[] = [
                'name' => $item->name,
                'qty' => ceil($avgTerjual * 7)
            ];
        }

        // ambil setting
        $setting = StoreSetting::first();

        // generate rak
        $layout = [];

        if($setting){
            for ($i = 0; $i < $setting->jumlah_rak; $i++) {
                $kodeRak = $this->getRackCode($i); // 🔥 pakai ini
                for ($j = 1; $j <= $setting->tingkat_rak; $j++) {

                    $layout[] = [
                        'kode' => $kodeRak.$j,
                        'zone' => $i == 0 ? 'front' : ($i == $setting->jumlah_rak-1 ? 'back' : 'middle')
                    ];
                }
            }
        }
        
        $products = Product::orderBy('id')->get();

        if($setting){

            $grouped = $products->groupBy(function($p){
                return substr($p->location, 0, 1); // A, B, C
            });

            foreach($grouped as $rak => $items){

                $itemsRak = $items->values();

                foreach($itemsRak as $index => $p){

                    // tentukan slot (A1, A2, dst)
                    $tingkat = floor($index / $setting->kapasitas_per_rak) + 1;

                    // batas maksimal tingkat
                    if($tingkat > $setting->tingkat_rak){
                        continue; // skip kalau melebihi tingkat
                    }

                    $kode = $rak . $tingkat;

                    $rackMap[$kode][] = $p;
                }
            }
        }
        
        $products = Product::leftJoin('transaction_items', 'products.name', '=', 'transaction_items.name')
            ->select(
                'products.id',
                'products.name',
                'products.location',
                DB::raw('SUM(transaction_items.qty) as total_sold')
            )
            ->groupBy('products.id', 'products.name', 'products.location')
            ->orderByDesc('total_sold')
            ->get();

        $products = Product::orderBy('id')->get();

        $rackMap = [];

        if($setting){

            $slotList = [];

            for ($i = 0; $i < $setting->jumlah_rak; $i++) {

                $kodeRak = $this->getRackCode($i);

                for ($j = 1; $j <= $setting->tingkat_rak; $j++) {

                    $slotList[] = $kodeRak . $j;
                }
            }

            $slotIndex = 0;
            $countInSlot = 0;

            foreach($products as $p){

                $currentSlot = $slotList[$slotIndex];

                $rackMap[$currentSlot][] = $p;

                $countInSlot++;

                if($countInSlot >= $setting->kapasitas_per_rak){
                    $slotIndex++;
                    $countInSlot = 0;
                }

                if($slotIndex >= count($slotList)){
                    break;
                }
            }
        }

        $products = Product::select('name','location')->get();


        /*
        ======================================
        RETURN
        ======================================
        */
        return view('dashboard', compact(
            'setting',
            'layout',
            'rackMap',
            'products',
            'totalProduk',
            'totalStok',
            'penjualanHariIni',
            'totalTransaksi',
            'labels',
            'salesData',
            'stokKeluar',
            'stokMasuk', // 🔥 FIX
            'type',
            'produkTerlaris',
            'stokRendah',
            'transaksiTerakhir',
            'prediksiBesok',
            'forecastData',
            'trend',
            'prediksiStok',
            'restock'
        ));
    }
}