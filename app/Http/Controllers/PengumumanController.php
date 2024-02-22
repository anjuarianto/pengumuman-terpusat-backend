<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengumumanRequest;
use App\Http\Requests\StorePengungumanRequest;
use App\Http\Requests\UpdatePengumumanRequest;
use App\Http\Requests\UpdatePengungumanRequest;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\PengumumanResource;
use App\Http\Resources\PengungumanResource;
use App\Models\Pengumuman;
use App\Models\Pengunguman;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pengumuman = Pengumuman::search($request->search)->paginate();
        
        !$pengumuman->count() > 0 ? $message = 'No data retrieved' : '';

        $pengumuman = PengumumanResource::collection($pengumuman)->response()->getData(true);
        return $this->success($pengumuman, $message ?? 'Data retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePengumumanRequest $request)
    {
        Pengumuman::create([
            'judul' => $request->post('judul'),
            'konten' => $request->konten,
            'waktu' => $request->waktu,
            'created_by' => 5
        ]);

        return $this->success([], 'Data created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pengumuman $pengumuman)
    {
        $pengumuman = new PengungumanResource($pengumuman);
        return $this->success($pengumuman, 'Request success');
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
            'created_by' => 4
        ]);

        return $this->success([], 'Data updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pengumuman $pengumuman)
    {
        $pengumuman->delete();
        return $this->success([], 'Data deleted successfully');
    }
}
