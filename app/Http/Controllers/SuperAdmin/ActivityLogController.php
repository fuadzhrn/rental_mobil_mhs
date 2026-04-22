<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'action' => ['nullable', 'string', 'max:100'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $activityLogs = ActivityLog::query()
            ->with('user')
            ->when($request->filled('action'), function ($query) use ($request): void {
                $query->where('action', 'like', '%' . $request->string('action')->toString() . '%');
            })
            ->when($request->filled('user_id'), function ($query) use ($request): void {
                $query->where('user_id', (int) $request->input('user_id'));
            })
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $userOptions = User::query()->orderBy('name')->get(['id', 'name', 'email']);

        return view('super-admin.activity-logs.index', compact('activityLogs', 'userOptions'));
    }
}
