<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Events\CartUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CashierController extends Controller
{

    /*
    ======================================
    HALAMAN KASIR
    ======================================
    */
    public function index()
    {
        return view('cashier.index');
    }

    /*
    ======================================
    GET PRODUCT BY BARCODE
    ======================================
    */
    public function getProduct(Request $request)
    {
        if (!$request->barcode) {
            return response()->json([
                'status' => false,
                'message' => 'Barcode kosong'
            ]);
        }

        $product = Product::where('barcode', $request->barcode)->first();

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Produk tidak ditemukan'
            ]);
        }

        return response()->json([
            'status' => true,
            'id' => $product->id,
            'barcode' => $product->barcode,
            'name' => $product->name,
            'sell_price' => (int) $product->sell_price,
            'discount' => (int) ($product->discount ?? 0)
        ]);
    }

    /*
    ======================================
    UPDATE CART (DISPLAY)
    ======================================
    */
    public function updateCart(Request $request)
    {
        broadcast(new CartUpdated(
            $request->cart ?? [],
            (int) ($request->total ?? 0),
            (int) ($request->subtotal ?? 0),
            (int) ($request->discount ?? 0),
            $request->cashier ?? "Kasir 1",
            "",
            0,
            0
        ));

        return response()->json([
            'status' => true,
            'message' => 'Cart berhasil diperbarui'
        ]);
    }

    /*
    ======================================
    CHECKOUT CASH
    ======================================
    */
    public function checkout(Request $request)
    {
        return $this->saveTransaction(
            $request->cart,
            (int) $request->total,
            (int) $request->pay,
            (int) $request->change,
            'Cash',
            null
        );
    }

    /*
    ======================================
    CHECKOUT UNIVERSAL (QRIS & TRANSFER)
    ======================================
    */
    public function checkoutUniversal(Request $request)
    {
        $order_id = $request->order_id;

        if (!$order_id) {
            return response()->json([
                'status' => false,
                'message' => 'Order ID tidak ada'
            ]);
        }

        // 🔥 AMBIL CART DARI CACHE
        $cart = Cache::get('order_'.$order_id);

        if (!$cart) {
            return response()->json([
                'status' => false,
                'message' => 'Cart expired / tidak ditemukan'
            ]);
        }

        // 🔥 METHOD DARI FRONTEND
        $method = $request->method ?? 'QRIS';

        return $this->saveTransaction(
            $cart,
            0, // total dihitung ulang
            0,
            0,
            $method,
            $order_id
        );
    }

    /*
    ======================================
    CORE FUNCTION
    ======================================
    */
    private function saveTransaction($cart, $total, $pay, $change, $method, $midtrans_id = null)
    {

        if (!$cart || count($cart) === 0) {
            app(\App\Http\Controllers\ProductController::class)->autoLayout();
            return response()->json([
                'status' => false,
                'message' => 'Cart kosong'
            ]);
        }

        DB::beginTransaction();

        try {

            $invoice = "INV-" . date('YmdHis');
            $grandTotal = 0;

            /*
            ======================================
            VALIDASI & HITUNG ULANG TOTAL
            ======================================
            */
            foreach ($cart as $item) {

                if (!isset($item['id']) || !isset($item['qty'])) {
                    throw new \Exception("Data cart tidak valid");
                }

                $product = Product::lockForUpdate()->find($item['id']);

                if (!$product) {
                    throw new \Exception("Produk tidak ditemukan (ID: {$item['id']})");
                }

                $qty = max(1, (int) $item['qty']);
                $price = (int) $product->sell_price;
                $discount = (int) ($item['discount'] ?? 0);

                $final = $price - ($price * $discount / 100);
                $subtotal = $final * $qty;

                $grandTotal += $subtotal;
            }

            /*
            ======================================
            VALIDASI CASH
            ======================================
            */
            if ($method === "Cash" && $pay < $grandTotal) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Uang tidak cukup'
                ]);
            }

            /*
            ======================================
            CREATE TRANSACTION
            ======================================
            */
            $trx = Transaction::create([
                'invoice' => $invoice,
                'total' => $grandTotal,
                'pay' => $pay,
                'change' => $method === "Cash" ? ($pay - $grandTotal) : 0,
                'method' => strtoupper($method),
                'midtrans_id' => $midtrans_id
            ]);

            /*
            ======================================
            LOOP ITEM + STOCK VALIDATION
            ======================================
            */
            foreach ($cart as $item) {

                $product = Product::lockForUpdate()->find($item['id']);

                if (!$product) continue;

                $qty = max(1, (int) $item['qty']);

                $availableStock = $product->stock_in - $product->stock_out;

                if ($availableStock < $qty) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Stock tidak cukup untuk ' . $product->name
                    ]);
                }

                /*
                UPDATE STOCK
                */
                $product->stock_out += $qty;
                $product->save();

                /*
                SAVE ITEM
                */
                TransactionItem::create([
                    'transaction_id' => $trx->id,
                    'name' => $product->name,
                    'price' => (int) $product->sell_price,
                    'qty' => $qty,
                    'discount' => (int) ($item['discount'] ?? 0)
                ]);
            }

            DB::commit();

            /*
            ======================================
            HAPUS CACHE (BIAR GA DOUBLE)
            ======================================
            */
            if ($midtrans_id) {
                Cache::forget('order_'.$midtrans_id);
            }

            /*
            ======================================
            BROADCAST SUCCESS
            ======================================
            */
            broadcast(new CartUpdated(
                [],
                0,
                0,
                0,
                "Kasir 1",
                strtoupper($method),
                0,
                0,
                null,
                "success"
            ));

            return response()->json([
                'status' => true,
                'message' => 'Transaksi berhasil',
                'transaction_id' => $trx->id,
                'invoice' => $invoice
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('CHECKOUT ERROR', [
                'message' => $e->getMessage(),
                'cart' => $cart
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ]);
        }
    }
}