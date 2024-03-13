<?php

namespace App\Http\Resources;

use App\Models\RoomHasMembers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class RoomMemberResource extends JsonResource
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
            'is_joined' => RoomHasMembers::where('room_id', $this->id)->where('user_id', Auth::user()->id)->exists()
        ];
    }

}
