<?php

namespace App\Http\Controllers;
use App\Helpers\JwtHandler;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function signUp(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        return response()->json([
            'message' => 'User create successfully',
            'user' => $user,
        ], 200);
    }


    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
        if (!Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }
        try {
            $token = JWTAuth::fromUser($user);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Could not create token',
                'error' => $e->getMessage(),
            ], 500);
        }
        return response()->json([
            'message' => 'User logged in successfully',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    public function getUser(Request $request)
    {
        $authorizationHeader = $request->header('access_token');
        if (!$authorizationHeader) {
            return response()->json(['error' => 'Token is missing'], 400);
        }

        try {
            $decoded_token = JWTAuth::setToken($authorizationHeader)->getPayload();
            $userId = $decoded_token["sub"];
            $user = User::where("id", operator: $userId)->with("collections")->get();
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['error' => 'There is something wrong'], 500);
        }

    }

    public function updateUser(Request $request)
    {
        $authorizationHeader = $request->header('access_token');
        if (!$authorizationHeader) {
            return response()->json(['error' => 'Token is missing'], 400);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        try {
            $decoded_token = JWTAuth::setToken($authorizationHeader)->getPayload();
            $userId = $decoded_token["sub"];
            $user = User::find($userId);
            $user->name = $validated["name"];
            $user->email = $validated["email"];
            $user->password = bcrypt($validated['password']);
            $user->save();
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['error' => 'There is something wrong'], 500);
        }

    }


    public function deleteUser(Request $request)
    {
        $authorizationHeader = $request->header('access_token');
        if (!$authorizationHeader) {
            return response()->json(['error' => 'Token is missing'], 400);
        }
        try {
            $decodedToken = JWTAuth::setToken($authorizationHeader)->getPayload();
            $userId = $decodedToken["sub"];
            $user = User::find($userId);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            foreach ($user->collections as $collection) {
                $collection->chats()->delete();
                $collection->delete();
            }
            $user->delete();
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'There is something wrong', 'details' => $e->getMessage()], 500);
        }
    }



}
