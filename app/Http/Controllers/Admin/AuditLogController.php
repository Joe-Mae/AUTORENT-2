<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Journal d'activité : chaque action sensible (changements de statut,
 * consultations de documents d'identité, uploads de permis…) y est
 * tracée avec son auteur (spécification §6.3 et §8).
 */
class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $filters = array_filter($request->only(['action', 'user_id']), fn ($value) => $value !== null && $value !== '');

        $logs = ActivityLog::with('user')
            ->when($filters['action'] ?? null, fn ($query, $action) => $query->where('action', 'like', "%{$action}%"))
            ->when($filters['user_id'] ?? null, fn ($query, $userId) => $query->where('user_id', $userId))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.audit.index', [
            'logs' => $logs,
            'filters' => $filters,
            'actions' => ActivityLog::distinct()->orderBy('action')->pluck('action'),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
