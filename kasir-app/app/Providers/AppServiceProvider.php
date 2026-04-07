<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// 🔥 MODEL
use App\Models\Product;
use App\Models\Transaction;

// 🔥 OBSERVER
use App\Observers\AuditLogObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /*
        =====================================
        🔥 REGISTER OBSERVER (CLEAN)
        =====================================
        */

        Product::observe(AuditLogObserver::class);
        Transaction::observe(AuditLogObserver::class);

        // ❌ HAPUS INI (TIDAK PERLU)
        // TransactionItem::observe(AuditLogObserver::class);
    }
}