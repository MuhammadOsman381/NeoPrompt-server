<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $chats = Chat::all();
        return response()->json($chats);
    }

    public function createChat($collection_id, Request $request)
    {
        $validated = $request->validate([
            'prompt' => 'required|string',
            'response' => 'required|string',
        ]);
        try {
            $new_chat = Chat::create([
                'prompt' => $validated['prompt'],
                'response' => $validated['response'],
                'collection_id' => $collection_id,
            ]);
            return response()->json([
                "message" => "Chat created succesfully",
                "chat" => $new_chat,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'There is something wrong'], 500);
        }
    }
    public function getCollectionChat($collection_id)
    {
        try {
            $chat_array = Chat::where("collection_id", $collection_id)->get();
            return response()->json([
                "message" => "Chat found succesfully",
                "chat" => $chat_array,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'There is something wrong'], 500);
        }
    }

    public function deleteChat($chat_id)
    {
        try {
            $chat = Chat::find($chat_id);
            if (!$chat) {
                return response()->json([
                    "message" => "Chat not found",
                ], 404);
            } else {
                $chat = $chat->delete();
                return response()->json([
                    "message" => "Chat deleted succesfully",
                    "chat" => $chat,
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'There is something wrong'], 500);
        }
    }
}
