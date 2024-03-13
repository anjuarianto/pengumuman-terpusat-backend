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
    return Auth::user()->load(['roles.permissions', 'rooms' => function($query) {
        return $query->select('id', 'name');
    }]);

});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('pengumuman', PengumumanController::class);
    Route::resource('user-group', UserGroupController::class);
    Route::resource('room', RoomController::class);


    Route::get('/room-member', [\App\Http\Controllers\RoomMemberController::class, 'index']);
    Route::post('/room-member/join', [\App\Http\Controllers\RoomMemberController::class, 'join']);
    Route::post('/room-member/unjoin', [\App\Http\Controllers\RoomMemberController::class, 'unjoin']);

    Route::get('/user', function () {
        return User::all();
    });

    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
});

