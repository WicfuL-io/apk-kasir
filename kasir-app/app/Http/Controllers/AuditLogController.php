<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        /*
        =====================================
        🔥 QUERY DASAR
        =====================================
        */
        $query = AuditLog::with('user')->latest();

        /*
        =====================================
        🔍 SEARCH
        =====================================
        */
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        /*
        =====================================
        🔥 FILTER MODEL
        =====================================
        */
        if ($request->filled('model') && $request->model !== 'ALL') {
            $query->where('model', $request->model);
        }

        /*
        =====================================
        🔥 PAGINATION (LEBIH BAGUS DARI LIMIT)
        =====================================
        */
        $logs = $query->paginate(20)->withQueryString();

        return view('audit.index', compact('logs'));
    }

    /*
    =====================================
    🔥 DETAIL LOG
    =====================================
    */
    public function show($id)
    {
        $log = AuditLog::with('user')->findOrFail($id);

        return view('audit.show', compact('log'));
    }
}