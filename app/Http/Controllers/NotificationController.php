<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'status' => ['nullable', 'in:all,unread,read'],
        ]);

        $notifications = UserNotification::query()
            ->where('user_id', (int) $request->user()->id)
            ->when($request->string('status')->toString() === 'unread', function ($query): void {
                $query->whereNull('read_at');
            })
            ->when($request->string('status')->toString() === 'read', function ($query): void {
                $query->whereNotNull('read_at');
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin-rental.notifications.index', compact('notifications'));
    }

    public function read(Request $request, UserNotification $notification): RedirectResponse
    {
        if ((int) $notification->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        $notification->markAsRead();

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function readAll(Request $request): RedirectResponse
    {
        UserNotification::query()
            ->where('user_id', (int) $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
