<?php

namespace App\Http\Controllers;
use App\Helpers\JwtHandler;
use App\Models\Collection;
use App\Models\Chat;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CollectionController extends Controller
{
    public function index()
    {
        $collections = Collection::all();
        return response()->json($collections);
    }

    public function createCollection(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);
        $authorizationHeader = $request->header('access_token');
        if (!$authorizationHeader) {
            return response()->json(['error' => 'Token is missing'], 400);
        }
        try {
            $decoded_token = JWTAuth::setToken($authorizationHeader)->getPayload();
            $userId = $decoded_token["sub"];
            $existingCollection = Collection::where('user_id', $userId)
                ->where('title', $validated["title"])
                ->first();
            if ($existingCollection) {
                return response()->json($existingCollection);
            } else {
                $newCollection = Collection::create([
                    'user_id' => $userId,
                    'title' => $validated["title"],
                ]);
                return response()->json($newCollection);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token is invalid'], 400);
        }
    }


    public function getCollection(Request $request)
    {
        $authorizationHeader = $request->header('access_token');
        if (!$authorizationHeader) {
            return response()->json(['error' => 'Token is missing'], 400);
        }

        try {
            $decoded_token = JWTAuth::setToken($authorizationHeader)->getPayload();
            $userId = $decoded_token["sub"];
            $user_collections = Collection::where('user_id', $userId)->with('chats')->get();
            if ($user_collections->isEmpty()) {
                return response()->json(['message' => 'No collections found'], 404);
            }
            $parsed_data = $user_collections->map(function ($item) {
                $item->title = ucwords(str_replace('_', ' ', $item->title));
                $item->chats = $item->chats->map(function ($chat) {
                    $chat->content = ucfirst($chat->content);
                    return $chat;
                });
                return $item;
            });
            return response()->json($parsed_data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'There is something wrong'], 500);
        }
    }


    public function deleteCollection($collection_id)
    {
        try {
            $collection = Collection::find($collection_id);
            if (!$collection) {
                return response()->json([
                    'message' => 'Collection not found',
                ], 404);
            }
            Chat::where('collection_id', $collection_id)->delete();
            $collection->delete();
            return response()->json([
                'message' => 'Collection and its associated chats deleted successfully',
                'collection_id' => $collection_id
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'There is something wrong', 'details' => $e->getMessage()], 500);
        }
    }
    

    public function updateCollection(Request $request, $collection_id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);
        try {
            $wordsArray = explode(' ', $validated["title"]);
            $combinedString = implode('_', $wordsArray);
            $parserdString = strtolower($combinedString);
            $collection = Collection::find($collection_id);
            if (!$collection) {
                return response()->json([
                    'message' => 'Collection not found',
                ], 404);
            } else {
                $collection->title = $parserdString;
                $collection->save();
                return response()->json([
                    'message' => 'Collection updated succesfully',
                    "collection" => $collection,
                ]);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'There is something wrong'], 500);
        }
    }



}
