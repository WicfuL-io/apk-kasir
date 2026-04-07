<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Transaction;
use App\Events\CartUpdated;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PriceCheckController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\AdminController;

Route::post('/setting/update',[DashboardController::class,'updateSetting'])
    ->name('setting.update');
    
Route::get('/test-email', function () {
    \Mail::raw('TES EMAIL BERHASIL 🔥', function ($message) {
        $message->to('wicful.15@gmail.com')
                ->subject('TEST EMAIL');
    });

    return "OK";
});

Route::get('/approve-user/{id}', [AdminController::class, 'approve']);
Route::get('/reject-user/{id}', [AdminController::class, 'reject']);

/*
|--------------------------------------------------------------------------
| 🔥 MIDTRANS CALLBACK (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::post('/payment/callback', function (Request $request) {

    try {

        Log::info('MIDTRANS CALLBACK', $request->all());

        if (!$request->order_id) {
            return response()->json(['status' => 'invalid']);
        }

        return app(PaymentController::class)->callback($request);

    } catch (\Throwable $e) {

        Log::error('CALLBACK ERROR', [
            'message' => $e->getMessage()
        ]);

        return response()->json(['status' => 'error']);
    }

})
->name('payment.callback')
->withoutMiddleware(['web']);


/*
|--------------------------------------------------------------------------
| 🌐 PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'))->name('home');


/*
|--------------------------------------------------------------------------
| 🔥 BROADCAST CART
|--------------------------------------------------------------------------
*/
Route::post('/broadcast/cart', function (Request $request) {

    try {

        $data = $request->validate([
            'cart' => 'nullable|array',
            'total' => 'nullable|numeric',
            'subtotal' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'cashier' => 'nullable|string',
            'payment' => 'nullable|string',
            'paid' => 'nullable|numeric',
            'change' => 'nullable|numeric',
            'qr' => 'nullable|string',
            'status' => 'nullable|string',
            'va' => 'nullable|string',
            'bank' => 'nullable|string',
        ]);

        event(new CartUpdated(
            $data['cart'] ?? [],
            (int) ($data['total'] ?? 0),
            (int) ($data['subtotal'] ?? 0),
            (int) ($data['discount'] ?? 0),
            $data['cashier'] ?? '-',
            $data['payment'] ?? '-',
            (int) ($data['paid'] ?? 0),
            (int) ($data['change'] ?? 0),
            $data['qr'] ?? null,
            $data['status'] ?? null,
            $data['va'] ?? null,
            $data['bank'] ?? null
        ));

        return response()->json([
            'status' => true,
            'message' => 'Broadcast sent'
        ]);

    } catch (\Throwable $e) {

        Log::error('BROADCAST ERROR', [
            'message' => $e->getMessage()
        ]);

        return response()->json([
            'status' => false,
            'message' => 'Broadcast gagal'
        ]);
    }

})
->middleware('throttle:60,1');


/*
|--------------------------------------------------------------------------
| 📊 DASHBOARD
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| 🧾 PRINT STRUK
|--------------------------------------------------------------------------
*/
Route::post('/print-struk', function (Request $request) {

    try {

        return view('cashier.struk', [
            'cart'   => json_decode($request->cart, true) ?? [],
            'total'  => (int) $request->total,
            'pay'    => (int) $request->pay,
            'change' => (int) $request->change,
            'method' => $request->method
        ]);

    } catch (\Throwable $e) {

        Log::error('PRINT STRUK ERROR', [
            'message' => $e->getMessage()
        ]);

        abort(500);
    }

})->name('struk.legacy');


/*
|--------------------------------------------------------------------------
| 🔐 AUTH REQUIRED
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | 🧾 KASIR
    |--------------------------------------------------------------------------
    */
    Route::prefix('kasir')->name('kasir.')->group(function () {

        Route::get('/', [CashierController::class, 'index'])->name('index');
        Route::get('/product', [CashierController::class, 'getProduct'])->name('product');

        Route::post('/update-cart', [CashierController::class, 'updateCart'])->name('update');
        Route::post('/checkout', [CashierController::class, 'checkout'])->name('checkout');
        Route::post('/checkout-universal', [CashierController::class, 'checkoutUniversal'])->name('checkout.universal');
    });

    /*
    |--------------------------------------------------------------------------
    | 💳 PAYMENT
    |--------------------------------------------------------------------------
    */
    Route::prefix('payment')->name('payment.')->group(function () {

        Route::post('/create', [PaymentController::class, 'create'])->name('create');

        Route::get('/status/{order_id}', [PaymentController::class, 'status'])
            ->where('order_id', '.*')
            ->name('status');
    });

    /*
    |--------------------------------------------------------------------------
    | 📊 LAPORAN
    |--------------------------------------------------------------------------
    */
    Route::get('/laporan', function () {

        $data = Transaction::latest()->limit(100)->get();

        return view('cashier.laporan', compact('data'));

    })->name('laporan');

    Route::get('/struk/{id}', function ($id) {

        $trx = Transaction::with('items')->findOrFail($id);

        return view('cashier.struk-db', compact('trx'));

    })->name('struk.detail');

    Route::get('/customer-display', fn () => view('cashier.customer-display'))
        ->name('customer.display');

    /*
    |--------------------------------------------------------------------------
    | 🔍 CEK HARGA
    |--------------------------------------------------------------------------
    */
    Route::prefix('cek-harga')->name('cek.')->group(function () {

        Route::get('/', [PriceCheckController::class, 'index'])->name('index');
        Route::get('/search', [PriceCheckController::class, 'search'])->name('search');
    });

    /*
    |--------------------------------------------------------------------------
    | 📦 INVENTORY
    |--------------------------------------------------------------------------
    */
    Route::resource('inventory', ProductController::class);

    /*
    |--------------------------------------------------------------------------
    | 👤 PROFILE
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->group(function () {

        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | 📜 AUDIT LOG
    |--------------------------------------------------------------------------
    */
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log');
    Route::get('/audit-log/{id}', [AuditLogController::class, 'show'])->name('audit-log.show');
});


/*
|--------------------------------------------------------------------------
| 🔓 API
|--------------------------------------------------------------------------
*/
Route::get('/api/product/{barcode}', [ProductController::class, 'fetchProduct'])
    ->name('api.product');


/*
|--------------------------------------------------------------------------
| ❤️ HEALTH
|--------------------------------------------------------------------------
*/
Route::get('/health', fn () => response()->json([
    'status' => 'ok',
    'app' => config('app.name'),
    'time' => now()
]));


/*
|--------------------------------------------------------------------------
| ❌ FALLBACK
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});


/*
|--------------------------------------------------------------------------
| 🔐 AUTH
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';