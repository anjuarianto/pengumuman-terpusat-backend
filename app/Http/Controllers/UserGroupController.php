<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserGroupRequest;
use App\Http\Requests\UpdateUserGroupRequest;
use App\Http\Resources\UserGroupResource;
use App\Models\UserGroup;
use App\Models\UserGroupHasUser;
use App\Traits\HttpResponses;
use Illuminate\Http\Response;

class UserGroupController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userGroup = UserGroup::get();

        if ($userGroup->isEmpty()) {
            return $this->error(null, 'No data found', Response::HTTP_NOT_FOUND);
        }

        return $this->success(
            UserGroupResource::collection($userGroup)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserGroupRequest $request)
    {
        $userGroup = UserGroup::create([
            'name' => $request->name
        ]);

        if ($userGroup && $request->user) {
            foreach ($request->user as $user) {
                UserGroupHasUser::create([
                    'user_group_id' => $userGroup->id,
                    'user_id' => $user
                ]);
            }
        }

        return $this->success(new UserGroupResource($userGroup), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserGroup $userGroup)
    {
        return $this->success(new UserGroupResource($userGroup));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserGroupRequest $request, UserGroup $userGroup)
    {
        $userGroup->update([
            'name' => $request->name
        ]);

        UserGroupHasUser::where('user_group_id', $userGroup->id)->delete();

        if ($request->user && $userGroup) {
            foreach ($request->user as $user) {
                UserGroupHasUser::create([
                    'user_group_id' => $userGroup->id,
                    'user_id' => $user
                ]);
            }
        }


        return $this->success(new UserGroupResource($userGroup));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserGroup $userGroup)
    {
        $userGroup->delete();

        if ($userGroup) {
            UserGroupHasUser::where('user_group_id', $userGroup->id)->delete();
        }

        return $this->success(null, Response::HTTP_NO_CONTENT);
    }
}
