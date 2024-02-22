<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserGroupRequest;
use App\Http\Requests\UpdateUserGroupRequest;
use App\Http\Resources\UserGroupResource;
use App\Models\UserGroup;
use App\Traits\HttpResponses;

class UserGroupController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_group = UserGroup::get();
        
        
        !$user_group->count() > 0 ? $message = 'No data retrieved' : '';

        $user_groups = UserGroupResource::collection($user_group);
        return $this->success($user_groups, $message ?? 'Data retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserGroupRequest $request)
    {
        UserGroup::create([
            'name' => $request->name
        ]);

        return $this->success([], 'Data created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(UserGroup $userGroup)
    {
        $user_group = new UserGroupResource($userGroup);
        return $this->success($user_group, 'Request success');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserGroupRequest $request, UserGroup $userGroup)
    {
        $userGroup->update([
            'name' => $request->name
        ]);

        return $this->success([], 'Data updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserGroup $userGroup)
    {
        $userGroup->delete();

        return $this->success([], 'Data deleted successfully');
    }
}
