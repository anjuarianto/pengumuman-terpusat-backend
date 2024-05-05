<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomMemberResource;
use App\Models\Room;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class RoomListController extends Controller
{
    use HttpResponses;

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $room = Room::get();
        return $this->success($room);
    }
}
