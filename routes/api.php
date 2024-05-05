<?php

use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserGroupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Room;
use App\Http\Controllers\MyPengumumanController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/upload', function () {
    $path = request()->file('upload')->store('public');

    $url = str_replace('public', 'storage', $path);
    return [
        'path' => $path,
        'url' => URL::to($url)
    ];
});

Route::get('/user-list', \App\Http\Controllers\UserListController::class);

Route::get('/room-list', \App\Http\Controllers\RoomListController::class);

Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return User::mySession();
});

Route::resource('pengumuman', PengumumanController::class);

Route::middleware('auth:sanctum')->group(function () {


    Route::resource('user-group', UserGroupController::class);
    Route::resource('room', RoomController::class);
    Route::resource('pengumuman/{pengumuman}/reply', \App\Http\Controllers\PengumumanReplyController::class);

    Route::get('/my-pengumuman/{date}', MyPengumumanController::class);

    Route::get('/room-member', [\App\Http\Controllers\RoomMemberController::class, 'index']);
    Route::post('/room-member/join', [\App\Http\Controllers\RoomMemberController::class, 'join']);
    Route::post('/room-member/unjoin', [\App\Http\Controllers\RoomMemberController::class, 'unjoin']);

    Route::resource('/user', UserController::class);

    Route::get('/user/dosen', function () {
        return User::where('role', 'dosen')->get();
    });

    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
});

