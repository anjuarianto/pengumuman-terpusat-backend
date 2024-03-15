<?php

use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserGroupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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

Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    $user = User::with(['rooms' => function ($query) {
        $query->select('id', 'name');
    }])->find(Auth::user()->id);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->getRoleNames()->first(),
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            'rooms' => $user->rooms->toArray()
        ];
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('pengumuman', PengumumanController::class);
    Route::resource('user-group', UserGroupController::class);
    Route::resource('room', RoomController::class);
    Route::resource('pengumuman/{pengumuman}/reply', \App\Http\Controllers\PengumumanReplyController::class);


    Route::get('/room-member', [\App\Http\Controllers\RoomMemberController::class, 'index']);
    Route::post('/room-member/join', [\App\Http\Controllers\RoomMemberController::class, 'join']);
    Route::post('/room-member/unjoin', [\App\Http\Controllers\RoomMemberController::class, 'unjoin']);

    Route::get('/user', function () {
        return User::all();
    });

    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
});

