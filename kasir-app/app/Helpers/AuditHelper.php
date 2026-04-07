<?php

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

if (!function_exists('audit_log')) {

    function audit_log(
        string $action,
        string $model,
        $model_id = null,
        string $description = null,
        $old = null,
        $new = null
    ) {
        try {

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => $action,
                'model'      => $model,
                'model_id'   => $model_id,
                'description'=> $description,

                'old_values' => $old,
                'new_values' => $new,

                'ip_address' => Request::ip(),
                'user_agent' => Request::header('User-Agent'),
            ]);

        } catch (\Throwable $e) {
            Log::error('AUDIT LOG ERROR: '.$e->getMessage());
        }
    }
}