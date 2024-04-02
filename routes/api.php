<?php

use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserGroupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Notifications\ReminderPengumuman;

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

Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
Route::get('/test', function () {
    $pengumuman = \App\Models\Pengumuman::notificationDaily()[0];
//    return $pengumuman;
    if ($pengumuman) {
        return (new ReminderPengumuman($pengumuman))->toMail($pengumuman->dibuat_oleh);
    }
});

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return User::mySession();
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

