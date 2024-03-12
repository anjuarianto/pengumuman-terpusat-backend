<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use App\Traits\HttpResponses;
use Illuminate\Http\Response;

class RoomController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
        $room = Room::create([
            'name' => $request->name
        ]);

        return $this->success(new RoomResource($room), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {

        $room->usersFromRoom = $room->getUsersFromRoomAttribute();

//        return $room;

        return $this->success(new RoomResource($room));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomRequest $request, Room $room)
    {
        $room->update([
            'name' => $request->name,
            // Add other fields as needed
        ]);

        return $this->success(new RoomResource($room));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        $room->delete();

        return $this->success(null, Response::HTTP_NO_CONTENT);
    }
}
