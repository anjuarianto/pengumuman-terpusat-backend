<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengumumanRequest;
use App\Http\Requests\UpdatePengumumanRequest;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\PengumumanResource;
use App\Http\Resources\PengungumanResource;
use App\Models\Pengumuman;
use App\Models\PengumumanTo;
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
        if(!Auth::user()->checkPermissionTo('view-pengumuman')) {
            return $this->error(null, 'Tidak memiliki akses untuk melihat pengumuman', Response::HTTP_FORBIDDEN);
        }

        $pengumumans = Pengumuman::filterRoom($request->room_id)->filterSearch($request->search)->orderBy('created_at', 'desc')->paginate();

        $pengumumans->each(function ($pengumuman) {
            $pengumuman->load('pengumumanToUsers');

            $pengumuman->usersFromPengumumanTo = $pengumuman->getUsersFromPengumumanToAttribute();
        });

        $pengumuman = PengumumanResource::collection($pengumumans)->response()->getData(true);

        return $this->success($pengumuman);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePengumumanRequest $request)
    {
        if(!Auth::user()->checkPermissionTo('create-pengumuman')) {
            return $this->error(null, 'Tidak memiliki akses untuk membuat pengumuman', Response::HTTP_FORBIDDEN);
        }

        if($request->waktu < date('Y-m-d H:i:s')) {
            return $this->error(null, 'Waktu pengumuman tidak boleh kurang dari waktu sekarang', Response::HTTP_BAD_REQUEST);
        }

        $pengumuman = Pengumuman::create([
            'judul' => $request->post('judul'),
            'konten' => $request->konten,
            'waktu' => $request->waktu,
            'created_by' => Auth::user()->id,
            'room_id' => $request->room_id,
        ]);

        foreach ($request->recipients as $recipient) {
            PengumumanTo::create([
                'pengumuman_id' => $pengumuman->id,
                'penerima_id' => explode('|', $recipient)[1],
                'is_single_user' => explode('|', $recipient)[0] === '1' ? 1 : 0,
            ]);
        }

        return $this->success($pengumuman, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pengumuman $pengumuman)
    {
        if(!Auth::user()->checkPermissionTo('view-pengumuman')) {
            return $this->error(null, 'Tidak memiliki akses untuk melihat pengumuman', Response::HTTP_FORBIDDEN);
        }

        $pengumuman = new PengumumanResource($pengumuman);
        return $this->success($pengumuman);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePengumumanRequest $request, Pengumuman $pengumuman)
    {
        if( !(Auth::user()->checkPermissionTo('edit-pengumuman') && Auth::user()->id == $pengumuman->created_by) ) {
            return $this->error(null, 'Tidak memiliki akses untuk mengedit pengumuman', Response::HTTP_FORBIDDEN);
        }

        if($request->waktu < date('Y-m-d H:i:s')) {
            return $this->error(null, 'Waktu pengumuman tidak boleh kurang dari waktu sekarang', Response::HTTP_BAD_REQUEST);
        }

        $pengumuman->update([
            'judul' => $request->judul,
            'konten' => $request->konten,
            'waktu' => $request->waktu,
            'created_by' => Auth::user()->id,
            'room_id' => $request->room_id,
        ]);

        $pengumuman->pengumumanToUsers()->delete();

        foreach ($request->recipients as $user_id) {
            PengumumanTo::create([
                'pengumuman_id' => $pengumuman->id,
                'penerima_id' => explode('|', $user_id)[1],
                'is_single_user' => explode('|', $user_id)[0] === '1' ? 1 : 0,
            ]);
        }

        return $this->success($pengumuman);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pengumuman $pengumuman)
    {
        if(! (Auth::user()->checkPermissionTo('delete-pengumuman') && Auth::user()->id == $pengumuman->created_by)) {

            return $this->error(null, 'Tidak memiliki akses untuk menghapus pengumuman', Response::HTTP_FORBIDDEN);
        }

        $pengumuman->delete();
        return $this->success(null, Response::HTTP_NO_CONTENT);
    }
}
