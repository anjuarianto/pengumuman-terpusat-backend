<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengumumanRequest;
use App\Http\Requests\UpdatePengumumanRequest;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\PengumumanResource;
use App\Http\Resources\PengungumanResource;
use App\Models\Pengumuman;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pengumumans = Pengumuman::search($request->search)->room($request->room_id)->paginate();

        $pengumumans->each(function ($pengumuman) {
            $pengumuman->load('pengumumanToUsers');

            $pengumuman->usersFromPengumumanTo = $pengumuman->getUsersFromPengumumanToAttribute();
        });

        if ($pengumumans->isEmpty()) {
            return $this->error(null, 'No pengumuman found', Response::HTTP_NOT_FOUND);
        }

        $pengumuman = PengumumanResource::collection($pengumumans)->response()->getData(true);

        return $this->success($pengumuman);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePengumumanRequest $request)
    {

        $pengumuman = Pengumuman::create([
            'judul' => $request->post('judul'),
            'konten' => $request->konten,
            'waktu' => $request->waktu,
            'created_by' => Auth::user()->id,
            'room_id' => $request->room_id,
        ]);

        return $this->success($pengumuman, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pengumuman $pengumuman)
    {
        $pengumuman = new PengungumanResource($pengumuman);
        return $this->success($pengumuman);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePengumumanRequest $request, Pengumuman $pengumuman)
    {
        $pengumuman->update([
            'judul' => $request->judul,
            'konten' => $request->konten,
            'waktu' => $request->waktu,
            'created_by' => Auth::user()->id,
            'room_id' => $request->room_id,
        ]);

        return $this->success($pengumuman);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pengumuman $pengumuman)
    {
        $pengumuman->delete();
        return $this->success(null, Response::HTTP_NO_CONTENT);
    }
}
