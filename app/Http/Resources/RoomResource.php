<?php

namespace App\Http\Resources;

use App\Models\RoomHasMembers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'members' => $this->id == 1 ? User::all()->map(function ($user) {
                return ['id' => $user->id, 'name' => $user->name . ' (' . ucfirst($user->getRoleBasedOnEmailDomain($user->email)) . ')', 'is_single_user' => '1'];
            }) : $this->members->map(function ($member) {
                if ($member->is_single_user) {
                    return ['id' => $member->user->id, 'name' => $member->user->name, 'is_single_user' => $member->is_single_user];
                } else {
                    return ['id' => $member->userGroup->id, 'name' => $member->userGroup->name, 'is_single_user' => $member->is_single_user];
                }

            })
        ];
    }
}
