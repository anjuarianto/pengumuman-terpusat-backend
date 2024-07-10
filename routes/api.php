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


Route::get('/user-list', \App\Http\Controllers\UserListController::class);

Route::get('/room-list', \App\Http\Controllers\RoomListController::class);

Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return User::mySession();
});

Route::get('/pengumuman-publik', \App\Http\Controllers\PengumumanNoAuthController::class);
Route::middleware('auth:sanctum')->group(function () {


    Route::post('/upload', function () {
//        restrict to not accept script file type
        request()->validate([
            'upload' => 'required|file|mimes:jpeg,png,jpg,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar|max:2048'
        ]);
        $path = request()->file('upload')->store('public');

        $url = str_replace('public', 'storage', $path);
        return [
            'path' => $path,
            'url' => URL::to($url)
        ];
    });

    Route::resource('pengumuman', PengumumanController::class);
    Route::resource('user-group', UserGroupController::class);
    Route::resource('room', RoomController::class);
    Route::resource('pengumuman/{pengumuman}/reply', \App\Http\Controllers\PengumumanReplyController::class);

    Route::get('/my-pengumuman/{date}', MyPengumumanController::class);

    Route::get('/room-member', [\App\Http\Controllers\RoomMemberController::class, 'index']);
    Route::post('/room-member/join', [\App\Http\Controllers\RoomMemberController::class, 'join']);
    Route::post('/room-member/unjoin', [\App\Http\Controllers\RoomMemberController::class, 'unjoin']);

    Route::post('/user-excel-upload', [\App\Http\Controllers\UserController::class, 'upload'])->name('upload-user-excel');
    Route::resource('/user', UserController::class);

    Route::post('delete-attachment/{id}', \App\Http\Controllers\DeleteAttachment::class);

    Route::get('/user/dosen', function () {
        return User::where('role', 'dosen')->get();
    });

    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
});

