<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengungumanRequest;
use App\Http\Requests\UpdatePengungumanRequest;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\PengungumanResource;
use App\Models\Pengunguman;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class PengungumanController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pengungumans = Pengunguman::search($request->search)->paginate();
        
        
        !$pengungumans->count() > 0 ? $message = 'No data retrieved' : '';

        $pengungumans = PengungumanResource::collection($pengungumans)->response()->getData(true);
        return $this->success($pengungumans, $message ?? 'Data retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePengungumanRequest $request)
    {

        Pengunguman::create([
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
    public function show(Pengunguman $pengunguman)
    {
        $pengunguman = new PengungumanResource($pengunguman);
        return $this->success($pengunguman, 'Request success');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePengungumanRequest $request, Pengunguman $pengunguman)
    {
        $pengunguman->update([
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
    public function destroy(Pengunguman $pengunguman)
    {
        $pengunguman->delete();
        return $this->success([], 'Data deleted successfully');
    }
}
