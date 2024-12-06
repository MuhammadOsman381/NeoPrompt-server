<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\UserController;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Auth related API
Route::get('/all-users', [UserController::class, 'index']);
Route::post('/log-in', [UserController::class, 'login']);
Route::post('/sign-up', [UserController::class, 'signUp']);
Route::get('/get-user', [UserController::class, 'getUser']);
Route::put('/update-user', [UserController::class, 'updateUser']);
Route::delete('/delete-user', [UserController::class, 'deleteUser']);


// Collection related API
Route::get('/all-collections', [CollectionController::class, 'index']);
Route::post('/create-collection', [CollectionController::class, 'createCollection']);
Route::delete('/delete-collection/{collection_id}', [CollectionController::class, 'deleteCollection']);
Route::put('/update-collection/{collection_id}', [CollectionController::class, 'updateCollection']);
Route::get('/get-user-collection-with-chat', [CollectionController::class, 'getCollection']);

// Chat related API
Route::get('/all-chats', [ChatController::class, 'index']);
Route::post('/create-chat/{collection_id}', [ChatController::class, 'createChat']);
Route::get('/get-collection-chat/{collection_id}', [ChatController::class, 'getCollectionChat']);
Route::delete('/delete-chat/{chat_id}', [ChatController::class, 'deleteChat']);
