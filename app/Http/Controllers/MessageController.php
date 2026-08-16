<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Http\Resources\MessageResource;
use App\Services\SseNotifier;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * @OA\Post(
     *     path="/messages",
     *     tags={"User - Contact Messages"},
     *     summary="Submit a contact message (notifies admins via SSE type message_received)",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "email", "subject", "message"},
     *                 
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="Ali Hasan"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     format="email",
     *                     example="ali@example.com"
     *                 ),
     *                 @OA\Property(
     *                     property="subject",
     *                     type="string",
     *                     example="Inquiry about a car"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="I would like more details about this car."
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Message submitted"),
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $message = Message::create($validated);

        app(SseNotifier::class)->sendToAdmins(
            'message_received',
            [
                'message_id' => $message->id,
                'name' => $message->name,
                'email' => $message->email,
                'subject' => $message->subject,
            ],
            'New contact message',
            'You received a new message from ' . $message->name
        );

        return new MessageResource($message);
    }
}