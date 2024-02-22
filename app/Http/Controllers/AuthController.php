<?php

namespace App\Http\Controllers;

use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use App\Http\Requests\LoginUserRequest;
use Auth;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(LoginUserRequest $request) {
        if(!Auth::attempt($request->only(['email', 'password']))) {
            return $this->error('', 'Credentials do not match', 401);
        }

        $user = User::where('email', $request->email)->first();

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('Api Token of '.$user->name)->plainTextToken
        ]);
    }
}
