<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SseNotifier
{
    public function send(int $userId, string $type, array $data = [], ?string $title = null, ?string $body = null): void
    {
        $key = $this->key($userId);
        $lock = Cache::lock("sse:lock:{$userId}", 5);

        $lock->block(3, function () use ($key, $type, $data, $title, $body) {
            $events = Cache::get($key, []);

            $events[] = [
                'id' => (string) Str::uuid(),
                'type' => $type,
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'sent_at' => now()->toIso8601String(),
            ];

            Cache::put($key, $events, now()->addMinutes(5));
        });
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
