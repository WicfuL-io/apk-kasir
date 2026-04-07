<?php

namespace App\Observers;

use App\Models\AuditLog;

class AuditLogObserver
{
    public function created($model)
    {
        if ($this->skip($model)) return;

        $this->handle($model, 'CREATE');
    }

    public function updated($model)
    {
        if ($this->skip($model)) return;

        $this->handle($model, 'UPDATE');
    }

    public function deleted($model)
    {
        if ($this->skip($model)) return;

        $this->handle($model, 'DELETE');
    }

    /*
    =====================================
    🔥 SKIP BIAR TIDAK SPAM
    =====================================
    */
    private function skip($model)
    {
        return in_array(class_basename($model), [
            'TransactionItem', // ❌ ini penyebab spam
        ]);
    }

    /*
    =====================================
    🔥 HANDLE LOG
    =====================================
    */
    private function handle($model, $action)
    {
        try {

            $name = class_basename($model);

            /*
            =====================================
            🔥 KHUSUS TRANSACTION (1 LOG SAJA)
            =====================================
            */
            if ($name === 'Transaction') {

                // ❌ hanya log saat CREATE
                if ($action !== 'CREATE') return;

                AuditLog::create([
                    'user_id' => auth()->id(),

                    'action' => 'CHECKOUT',
                    'model' => 'Transaction',
                    'model_id' => $model->id,

                    'description' => "🧾 Transaksi {$model->invoice} - Rp " . number_format($model->total),

                    'status' => 'SUCCESS',
                    'level' => 'INFO',

                    'ip_address' => request()->ip(),
                    'method' => request()->method(),
                ]);

                return;
            }

            /*
            =====================================
            🔥 PRODUCT
            =====================================
            */
            if ($name === 'Product') {

                $desc = match ($action) {
                    'CREATE' => "📦 Tambah produk {$model->name}",
                    'UPDATE' => "✏️ Update produk {$model->name}",
                    'DELETE' => "🗑 Hapus produk {$model->name}",
                };

                AuditLog::create([
                    'user_id' => auth()->id(),

                    'action' => $action,
                    'model' => 'Product',
                    'model_id' => $model->id,

                    'description' => $desc,

                    'status' => 'SUCCESS',
                    'level' => 'INFO',

                    'ip_address' => request()->ip(),
                    'method' => request()->method(),
                ]);

                return;
            }

        } catch (\Exception $e) {}
    }
}