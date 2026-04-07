<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Cache;

class AuditLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);

        $response = $next($request);

        /*
        =====================================
        🔥 FILTER LOG
        =====================================
        */
        if (!$this->shouldLog($request)) {
            return $response;
        }

        $action = $this->detectAction($request);

        // ❌ skip GET
        if (!$action) {
            return $response;
        }

        /*
        =====================================
        🔥 ANTI DUPLIKAT (LEBIH KUAT)
        =====================================
        */
        $key = 'audit_' . auth()->id() . '_' . $request->method() . '_' . $request->path();

        if (Cache::has($key)) {
            return $response;
        }

        Cache::put($key, true, 3);

        $duration = round((microtime(true) - $start) * 1000);

        try {

            $statusCode = method_exists($response, 'getStatusCode')
                ? $response->getStatusCode()
                : 200;

            AuditLog::create([
                'user_id' => auth()->id(),

                'action' => $action,
                'model' => $this->detectModel($request),

                'description' => $this->makeDescription($request),

                'status' => $statusCode >= 400 ? 'FAILED' : 'SUCCESS',

                'level' => match (true) {
                    $statusCode >= 500 => 'ERROR',
                    $statusCode >= 400 => 'WARNING',
                    default => 'INFO'
                },

                'ip_address' => $request->ip(),
                'method' => $request->method(),
                'url' => $request->path(),

                'duration' => $duration,
            ]);

        } catch (\Exception $e) {}

        return $response;
    }

    /*
    =====================================
    🔥 FIX ERROR DI SINI
    =====================================
    */
    private function shouldLog($request)
    {
        $path = $request->path();

        return match (true) {

            // ✅ AUTH
            str_contains($path, 'login') => true,
            str_contains($path, 'logout') => true,

            // ✅ PAYMENT
            str_contains($path, 'payment') => true,

            // ❌ JANGAN LOG CHECKOUT (SUDAH DI OBSERVER)
            // str_contains($path, 'checkout') => true,

            default => false
        };
    }

    private function detectAction($request)
    {
        return match ($request->method()) {
            'POST' => 'CREATE',
            'PUT','PATCH' => 'UPDATE',
            'DELETE' => 'DELETE',
            default => null
        };
    }

    private function detectModel($request)
    {
        $path = $request->path();

        if (str_contains($path, 'payment')) return 'Payment';
        if (str_contains($path, 'login')) return 'Auth';
        if (str_contains($path, 'logout')) return 'Auth';

        return 'System';
    }

    private function makeDescription($request)
    {
        $path = $request->path();

        return match (true) {

            str_contains($path, 'login') =>
                '🔐 Login ke sistem',

            str_contains($path, 'logout') =>
                '🚪 Logout dari sistem',

            str_contains($path, 'payment') =>
                '💳 Proses pembayaran',

            default =>
                '⚙️ Aktivitas sistem'
        };
    }
}