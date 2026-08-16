<?php

namespace App\Services;

use App\Models\Car;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

class SseNotifier
{
    public function send(int $userId, string $type, array $data = [], ?string $title = null, ?string $body = null): void
    {
        $notification = Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);

        $event = [
            'id' => (string) $notification->id,
            'type' => $notification->type,
            'title' => $notification->title,
            'body' => $notification->body,
            'data' => $notification->data ?? [],
            'sent_at' => $notification->created_at->toIso8601String(),
        ];

        $key = $this->key($userId);
        $lock = Cache::lock("sse:lock:{$userId}", 5);

        $lock->block(3, function () use ($key, $event) {
            $events = Cache::get($key, []);
            $events[] = $event;
            Cache::put($key, $events, now()->addMinutes(5));
        });
    }

    public function sendToAdmins(string $type, array $data = [], ?string $title = null, ?string $body = null): void
    {
        $adminIds = User::role('admin')->where('is_active', true)->pluck('id');

        $adminRoleId = Role::where('name', 'admin')->value('id');
        if ($adminRoleId) {
            $adminIds = $adminIds->merge(
                User::where('role_id', $adminRoleId)->where('is_active', true)->pluck('id')
            )->unique();
        }

        foreach ($adminIds as $adminId) {
            $this->send((int) $adminId, $type, $data, $title, $body);
        }
    }

    public function notifyAdminsOfPendingCar(Car $car): void
    {
        $this->sendToAdmins(
            'car_created',
            [
                'car_id' => $car->id,
                'seller_id' => $car->user_id,
                'name' => $car->name,
                'brand' => $car->brand,
                'type' => $car->type,
                'approval_status' => $car->approval_status,
            ],
            'New car pending approval',
            'A new car was added and needs admin review'
        );
    }

    public function notifyAdminsOfUpdatedCar(Car $car): void
    {
        $this->sendToAdmins(
            'car_updated',
            [
                'car_id' => $car->id,
                'seller_id' => $car->user_id,
                'name' => $car->name,
                'brand' => $car->brand,
                'type' => $car->type,
                'approval_status' => $car->approval_status,
            ],
            'Updated car pending approval',
            'A rejected car was edited and returned for review'
        );
    }

    public function pull(int $userId): array
    {
        $key = $this->key($userId);
        $lock = Cache::lock("sse:lock:{$userId}", 5);

        return $lock->block(3, function () use ($key) {
            $events = Cache::get($key, []);
            Cache::forget($key);

            return $events;
        });
    }

    private function key(int $userId): string
    {
        return "sse:user:{$userId}";
    }
}
