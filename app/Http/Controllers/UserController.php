<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserGroupHasUser;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    use HttpResponses;

    public function index(Request $request)
    {
        $users = User::get();

        $users = UserResource::collection($users);

        return $this->success($users);
    }

    public function store(Request $request)
    {

        if (User::getRoleBasedOnEmailDomain($request->email) == null) {
            return $this->error(null, 'Email tidak valid', 400);
        }

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make('password')
            ]);

            UserGroupHasUser::create([
                'user_id' => $user->id,
                'user_group_id' => UserGroup::USER_GROUP[User::getRoleBasedOnEmailDomain($request->email)]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage(), 500);
        }

        DB::commit();

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
