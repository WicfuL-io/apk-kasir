<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Midtrans\Config;
use App\Events\CartUpdated;

class PaymentController extends Controller
{
    /*
    ======================================
    CREATE PAYMENT
    ======================================
    */
    public function create(Request $request)
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = false;

        $total  = (int) $request->total;
        $cart   = $request->cart ?? [];
        $method = $request->method ?? 'QRIS';
        $bank   = strtolower($request->bank ?? '');

        if ($total <= 0 || empty($cart)) {
            return response()->json([
                'status' => false,
                'message' => 'Data pembayaran tidak valid'
            ]);
        }

        $order_id = "POS-" . time() . rand(100,999);

        // 🔥 SIMPAN CART KE CACHE
        Cache::put('order_'.$order_id, $cart, now()->addMinutes(30));

        try {

            /*
            ======================================
            PAYMENT CONFIG
            ======================================
            */
            if ($method === "QRIS") {

                $payload = [
                    "payment_type" => "qris",
                    "transaction_details" => [
                        "order_id" => $order_id,
                        "gross_amount" => $total
                    ]
                ];

            } elseif ($method === "Transfer") {

                $allowedBanks = [
                    'bca','bni','bri','permata',
                    'cimb','danamon','bsi','sea','mandiri'
                ];

                if (!in_array($bank, $allowedBanks)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Bank tidak valid'
                    ]);
                }

                $payload = [
                    "transaction_details" => [
                        "order_id" => $order_id,
                        "gross_amount" => $total
                    ]
                ];

                if ($bank === "permata") {
                    $payload["payment_type"] = "bank_transfer";
                    $payload["bank_transfer"] = ["bank" => "permata"];

                } elseif ($bank === "mandiri") {
                    $payload["payment_type"] = "echannel";
                    $payload["echannel"] = [
                        "bill_info1" => "Payment:",
                        "bill_info2" => "POS Payment"
                    ];

                } else {
                    $payload["payment_type"] = "bank_transfer";
                    $payload["bank_transfer"] = ["bank" => $bank];
                }

            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Metode tidak didukung'
                ]);
            }

            /*
            ======================================
            REQUEST MIDTRANS
            ======================================
            */
            $response = Http::timeout(30)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->withBasicAuth(env('MIDTRANS_SERVER_KEY'), '')
                ->post('https://api.sandbox.midtrans.com/v2/charge', $payload);

            if (!$response->successful()) {
                Log::error('MIDTRANS ERROR', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Midtrans gagal',
                    'debug' => $response->body()
                ]);
            }

            $data = $response->json();

            if (!$data) {
                return response()->json([
                    'status' => false,
                    'message' => 'Response Midtrans kosong'
                ]);
            }

            /*
            ======================================
            HANDLE RESPONSE
            ======================================
            */
            $qr = null;
            $va_number = null;
            $bankName = null;

            // QRIS
            if ($method === "QRIS") {

                foreach ($data['actions'] ?? [] as $action) {
                    if (($action['name'] ?? '') === "generate-qr-code") {
                        $qr = $action['url'] ?? null;
                    }
                }
            }

            // TRANSFER
            if ($method === "Transfer") {

                if (isset($data['va_numbers'][0])) {
                    $va_number = $data['va_numbers'][0]['va_number'] ?? null;
                    $bankName = strtoupper($data['va_numbers'][0]['bank'] ?? '');

                } elseif (isset($data['permata_va_number'])) {
                    $va_number = $data['permata_va_number'];
                    $bankName = "PERMATA";

                } elseif (isset($data['bill_key'])) {
                    $va_number = $data['bill_key'];
                    $bankName = "MANDIRI";
                }
            }

        } catch (\Throwable $e) {

            Log::error('MIDTRANS EXCEPTION', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Gagal koneksi ke Midtrans',
                'error' => $e->getMessage()
            ]);
        }

        /*
        ======================================
        BROADCAST (FIX)
        ======================================
        */
        broadcast(new CartUpdated(
            $cart, // ✅ FIX: tidak kosong
            $total,
            0,
            0,
            "Kasir 1",
            $method,
            0,
            0,
            $qr,
            "pending",
            $va_number,
            $bankName
        ));

        return response()->json([
            'status' => true,
            'order_id' => $order_id,
            'qr' => $qr,
            'va_number' => $va_number,
            'bank' => $bankName
        ]);
    }

    /*
    ======================================
    CEK STATUS
    ======================================
    */
    public function status($order_id)
    {
        try {

            $response = Http::timeout(15)
                ->withBasicAuth(env('MIDTRANS_SERVER_KEY'), '')
                ->get("https://api.sandbox.midtrans.com/v2/$order_id/status");

            Log::info("STATUS CHECK:", $response->json()); // ✅ tambahan debug

            return response()->json($response->json());

        } catch (\Throwable $e) {

            Log::error('STATUS ERROR', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'transaction_status' => 'error'
            ]);
        }
    }

    /*
    ======================================
    CALLBACK (WEBHOOK)
    ======================================
    */
    public function callback(Request $request)
    {
        Log::info('MIDTRANS CALLBACK:', $request->all());

        $transaction_status = $request->transaction_status ?? null;
        $payment_type = $request->payment_type ?? 'Unknown';
        $order_id = $request->order_id ?? null;

        // 🔥 mapping status
        $map = [
            "settlement" => "success",
            "capture" => "success",
            "pending" => "pending",
            "expire" => "failed",
            "cancel" => "failed",
            "deny" => "failed"
        ];

        $statusFix = $map[$transaction_status] ?? "unknown";

        if ($statusFix === "success" && $order_id) {

            // 🔥 ambil cart dari cache
            $cart = Cache::get('order_'.$order_id, []);

            // 🔥 finalize transaksi (tanpa ubah struktur)
            try {
                app(\App\Http\Controllers\CashierController::class)
                    ->checkoutUniversal(new Request([
                        'order_id' => $order_id
                    ]));
            } catch (\Throwable $e) {
                Log::error("FINALIZE ERROR", [
                    'message' => $e->getMessage()
                ]);
            }

            // 🔥 hapus cache
            Cache::forget('order_'.$order_id);

            broadcast(new CartUpdated(
                $cart,
                0,
                0,
                0,
                "Kasir 1",
                $payment_type,
                0,
                0,
                null,
                "success"
            ));
        }

        return response()->json(["status" => "ok"]);
    }
}