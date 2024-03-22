<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\PengumumanTo;
use App\Models\Room;
use App\Models\RoomHasMembers;
use App\Traits\HttpResponses;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->cannot('view-room', Room::class)) {
            return $this->error(null, 'You are not authorized to view a room', Response::HTTP_FORBIDDEN);
        }
        $rooms = Room::all();

        if ($rooms->isEmpty()) {
            return $this->error(null, 'No rooms found', Response::HTTP_NOT_FOUND);
        }

        return $this->success(RoomResource::collection($rooms));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomRequest $request)
    {
        if (Auth::user()->cannot('create-room', Room::class)) {
            return $this->error(null, 'You are not authorized to create a room', Response::HTTP_FORBIDDEN);
        }

        $room = Room::create([
            'name' => $request->name,
            'description' => $request->description
        ]);


        if (!empty($request->members)) {
            foreach ($request->members as $member) {
                RoomHasMembers::create([
                    'room_id' => $room->id,
                    'user_id' => explode('|', $member)[1],
                    'is_single_user' => explode('|', $member)[0] === '1' ? 1 : 0,
                ]);
            }
        }

        return $this->success(new RoomResource($room), Response::HTTP_CREATED, 'Room created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        if (Auth::user()->cannot('view-room', Room::class)) {
            return $this->error(null, 'You are not authorized to view a room', Response::HTTP_FORBIDDEN);
        }

        $room->usersFromRoom = $room->getUsersFromRoomAttribute();

        return $this->success(new RoomResource($room), Response::HTTP_OK, 'Room found');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomRequest $request, Room $room)
    {
        if (Auth::user()->cannot('edit-room', Room::class)) {
            return $this->error(null, 'You are not authorized to update a room', Response::HTTP_FORBIDDEN);
        }

        if ($room->id === Room::GENERAL_ROOM_ID) {
            return $this->error(null, 'You are not authorized to update a general room', Response::HTTP_FORBIDDEN);
        }


        $room->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        RoomHasMembers::where('room_id', $room->id)->delete();

        if (!empty($request->members)) {
            foreach ($request->members as $member) {
                RoomHasMembers::create([
                    'room_id' => $room->id,
                    'user_id' => explode('|', $member)[1],
                    'is_single_user' => explode('|', $member)[0] === '1' ? 1 : 0,
                ]);
            }
        }

        return $this->success(new RoomResource($room), Response::HTTP_ACCEPTED, 'Room updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        if (Auth::user()->cannot('delete-room', Room::class)) {
            return $this->error(null, 'You are not authorized to view a room', Response::HTTP_FORBIDDEN);
        }

        if ($room->id === Room::GENERAL_ROOM_ID) {
            return $this->error(null, 'You are not authorized to delete a general room', Response::HTTP_FORBIDDEN);
        }

        $room->delete();

        return $this->success(null, Response::HTTP_NO_CONTENT);
    }
}
