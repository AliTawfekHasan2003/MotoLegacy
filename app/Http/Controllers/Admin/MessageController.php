<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Http\Resources\MessageResource;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/messages",
     *     tags={"Admin Messages"},
     *     summary="List all contact messages (Admin only)",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function index()
    {
        return MessageResource::collection(Message::latest()->get());
    }
}
