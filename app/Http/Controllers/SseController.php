<?php

namespace App\Http\Controllers;

use App\Services\SseNotifier;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SseController extends Controller
{
    /**
     * @OA\Get(
     *     path="/sse/stream",
     *     tags={"SSE"},
     *     summary="Open SSE stream for buyer/renter notifications",
     *     description="Keep this connection open. EventSource cannot send Authorization headers: pass Sanctum token as ?token=YOUR_TOKEN (without the word Bearer). Event name is notification. Types: purchase_request_status_updated, rental_request_status_updated. data.rejection_reason is set when the seller rejects.",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         required=false,
     *         description="Optional Sanctum token for EventSource clients that cannot set Authorization headers",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="text/event-stream connection",
     *         @OA\MediaType(mediaType="text/event-stream")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * @OA\Get(
     *     path="/seller/sse/stream",
     *     tags={"SSE"},
     *     summary="Open SSE stream for seller notifications",
     *     description="Keep this connection open. Pass token as ?token=YOUR_TOKEN. Event name is notification. Types: car_approval_status_updated (admin approve/reject car), purchase_request_created (new purchase request), rental_request_created (new rental request).",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="text/event-stream connection", @OA\MediaType(mediaType="text/event-stream")),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     *
     * @OA\Get(
     *     path="/admin/sse/stream",
     *     tags={"SSE"},
     *     summary="Open SSE stream for admin notifications",
     *     description="Keep this connection open. Pass token as ?token=YOUR_TOKEN. Event name is notification. Types: car_created (new car or pending review), car_updated (rejected car edited and returned to pending), message_received (new contact message).",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="text/event-stream connection", @OA\MediaType(mediaType="text/event-stream")),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function stream(Request $request, SseNotifier $notifier): StreamedResponse
    {
        $userId = $request->user()->id;

        return response()->stream(function () use ($userId, $notifier) {
            set_time_limit(0);
            ignore_user_abort(true);

            // Disable compression / buffering for live stream
            if (function_exists('apache_setenv')) {
                @apache_setenv('no-gzip', '1');
            }
            @ini_set('zlib.output_compression', '0');
            @ini_set('implicit_flush', '1');
            while (ob_get_level() > 0) {
                ob_end_flush();
            }

            echo "retry: 3000\n\n";
            $this->flush();

            $heartbeatEvery = 15;
            $lastHeartbeat = time();
            $startedAt = time();
            $maxSeconds = 55; // client should reconnect; safer for Apache/PHP

            while (!connection_aborted() && (time() - $startedAt) < $maxSeconds) {
                $events = $notifier->pull($userId);

                foreach ($events as $event) {
                    echo 'id: ' . $event['id'] . "\n";
                    echo "event: notification\n";
                    echo 'data: ' . json_encode($event, JSON_UNESCAPED_UNICODE) . "\n\n";
                    $this->flush();
                }

                if ((time() - $lastHeartbeat) >= $heartbeatEvery) {
                    echo ": heartbeat\n\n";
                    $this->flush();
                    $lastHeartbeat = time();
                }

                usleep(500000); // 0.5s
            }
        }, 200, [
            'Content-Type' => 'text/event-stream; charset=UTF-8',
            'Cache-Control' => 'no-cache, no-store',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    private function flush(): void
    {
        if (ob_get_level() > 0) {
            @ob_flush();
        }
        @flush();
    }
}
