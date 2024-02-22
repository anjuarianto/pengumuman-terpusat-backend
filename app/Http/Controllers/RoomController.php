<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $room = Room::search($request->search)->paginate();
        
        !$room->count() > 0 ? $message = 'No data retrieved' : '';

        $room = RoomResource::collection($room);
        return $this->success($room, $message ?? 'Data retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomRequest $request)
    {
        Room::create([
            'name' => $request->name
        ]);

        return $this->success([], 'Data created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        $room = new RoomResource($room);
        return $this->success($room, 'Request success');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomRequest $request, Room $room)
    {
        $room->update([
            'name' => $request->name
        ]);

        return $this->success([], 'Data updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        $room->delete();
        $room->success([], 'Data deleted successfully');
    }
}
