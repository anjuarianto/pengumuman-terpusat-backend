<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

    public function store(Request $request)
    {

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return $this->success(new UserResource($user));
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->error(null, 'User tidak ditemukan', 404);
        }

        return $this->success(new UserResource($user));
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->error(null, 'User tidak ditemukan', 404);
        }

        $user->update([
            'name' => $request->name,
        ]);

        return $this->success(new UserResource($user));
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->error(null, 'User tidak ditemukan', 404);
        }

        $user->delete();

        return $this->success(null, 200, 'User berhasil dihapus');
    }
}
