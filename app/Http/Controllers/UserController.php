<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use HttpResponses;

    public function index(Request $request)
    {
        $users = User::all();

        if ($request->role) {
            if (Role::where('name', $request->role)->count() == 0) {
                return $this->error(null, 'Role tidak ditemukan', 404);
            }

            $users = $users->map(function ($user) use ($request) {
                $user->role = User::getRoleBasedOnEmailDomain($user->email);
                return $user;
            });

            $users = $users->filter(function ($user) use ($request) {
                return $user->role == Role::where('name', $request->role)->first()->name;
            });
        }

        $users = UserResource::collection($users);

        return $this->success($users);
    }
}
