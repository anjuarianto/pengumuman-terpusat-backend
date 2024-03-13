<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomMemberResource;
use App\Models\Room;
use App\Models\RoomHasMembers;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoomMemberController extends Controller
{
    use HttpResponses;

    public function index() {
        $roomMember = Room::get();

        $roomMember = RoomMemberResource::collection($roomMember);
        return $this->success($roomMember);
    }

    public function join(Request $request) {
        if(RoomHasMembers::where('room_id', $request->room_id)->where('user_id', auth()->id())->exists()) {
            return $this->error(null, 'You have already joined the room', 400);
        }

        RoomHasMembers::create([
            'room_id' => $request->room_id,
            'user_id' => auth()->id(),
            'is_single_user' => 1
        ]);

        return $this->success('You have joined the room');
    }

    public function unjoin(Request $request) {
        if(!RoomHasMembers::where('room_id', $request->room_id)->where('user_id', auth()->id())->exists()) {
            return $this->error(null, 'No Data Found', Response::HTTP_NOT_FOUND);
        }

        RoomHasMembers::where('room_id', $request->room_id)->where('user_id', auth()->id())->delete();

        return $this->success(null, Response::HTTP_NO_CONTENT);
    }
}
