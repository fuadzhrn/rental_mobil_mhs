<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Eloquent\Collection;

class NotificationService
{
    public function notifyUser(int $userId, string $title, string $message, string $type = 'info', ?string $url = null, ?string $referenceType = null, ?int $referenceId = null): void
    {
        UserNotification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'url' => $url,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
        ]);
    }

    public function notifyUsers(iterable $userIds, string $title, string $message, string $type = 'info', ?string $url = null, ?string $referenceType = null, ?int $referenceId = null): void
    {
        foreach ($userIds as $userId) {
            $this->notifyUser((int) $userId, $title, $message, $type, $url, $referenceType, $referenceId);
        }
    }

    public function notifyRole(string $role, string $title, string $message, string $type = 'info', ?string $url = null, ?string $referenceType = null, ?int $referenceId = null): void
    {
        $userIds = User::query()->where('role', $role)->pluck('id');
        $this->notifyUsers($userIds, $title, $message, $type, $url, $referenceType, $referenceId);
    }

    public function unreadForUser(int $userId): Collection
    {
        return UserNotification::query()
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->latest('id')
            ->get();
    }
}
