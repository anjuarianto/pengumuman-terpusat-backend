<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengumumanRequest;
use App\Http\Requests\UpdatePengumumanRequest;
use App\Http\Resources\PengumumanResource;
use App\Jobs\KirimEmailPengumumanBaruJob;
use App\Models\Pengumuman;
use App\Models\PengumumanTo;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengumumanController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!Auth::user()->checkPermissionTo('view-pengumuman')) {
            return $this->error(null, 'Tidak memiliki akses untuk melihat pengumuman', Response::HTTP_FORBIDDEN);
        }

        $pengumumans = Pengumuman::filter($request)
            ->orderBy('created_at', $request->order ?? 'desc')
            ->paginate();

        $pengumuman = PengumumanResource::collection($pengumumans)->response()->getData(true);

        return $this->success($pengumuman);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePengumumanRequest $request)
    {
        if (!Auth::user()->checkPermissionTo('create-pengumuman')) {
            return $this->error(null, 'Tidak memiliki akses untuk membuat pengumuman', Response::HTTP_FORBIDDEN);
        }

        if ($request->waktu < date('Y-m-d H:i:s')) {
            return $this->error(null, 'Waktu pengumuman tidak boleh kurang dari waktu sekarang', Response::HTTP_BAD_REQUEST);
        }

        DB::beginTransaction();

        try {
            $pengumuman = Pengumuman::create([
                'judul' => $request->judul,
                'konten' => $request->konten,
                'waktu' => $request->waktu,
                'is_private' => $request->is_private,
                'created_by' => Auth::user()->id,
                'room_id' => $request->room_id,
            ]);

            if ($request->recipients) {
                foreach ($request->recipients as $recipient) {
                    PengumumanTo::create([
                        'pengumuman_id' => $pengumuman->id,
                        'penerima_id' => explode('|', $recipient)[1],
                        'is_single_user' => explode('|', $recipient)[0] === '1' ? 1 : 0,
                    ]);
                }
            }


            if ($request->attachment) {
                foreach ($request->attachment as $file) {
                    if ($file->getSize() > 25000000) {
                        return $this->error(null, 'Ukuran file attachment tidak boleh lebih dari 25MB', Response::HTTP_BAD_REQUEST);
                    }
                    $file->store('public/pengumuman');
                    $pengumuman->files()->create([
                        'file' => $file->hashName(),
                        'original_name' => $file->getClientOriginalName()
                    ]);
                }
            }


            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 'Gagal membuat pengumuman', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        KirimEmailPengumumanBaruJob::dispatch($pengumuman)->onQueue('default');

        return $this->success($pengumuman, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pengumuman $pengumuman)
    {
        if (!Auth::user()->checkPermissionTo('view-pengumuman')) {
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
        if (!(Auth::user()->checkPermissionTo('edit-pengumuman') && Auth::user()->id == $pengumuman->created_by)) {
            return $this->error(null, 'Tidak memiliki akses untuk mengedit pengumuman', Response::HTTP_FORBIDDEN);
        }

        if ($request->waktu < date('Y-m-d H:i:s')) {
            return $this->error(null, 'Waktu pengumuman tidak boleh kurang dari waktu sekarang', Response::HTTP_BAD_REQUEST);
        }

        $pengumuman->update([
            'judul' => $request->judul,
            'konten' => $request->konten,
            'waktu' => $request->waktu,
            'is_private' => $request->is_private,
            'created_by' => Auth::user()->id,
            'room_id' => $request->room_id,
        ]);

        $pengumuman->pengumumanToUsers()->delete();

        if ($request->recipients) {
            foreach ($request->recipients as $user_id) {
                PengumumanTo::create([
                    'pengumuman_id' => $pengumuman->id,
                    'penerima_id' => explode('|', $user_id)[1],
                    'is_single_user' => explode('|', $user_id)[0] === '1' ? 1 : 0,
                ]);
            }
        }

        if ($request->attachment) {
            foreach ($request->attachment as $file) {
                if ($file->getSize() > 2000000) {
                    return $this->error(null, 'Ukuran file attachment tidak boleh lebih dari 2MB', Response::HTTP_BAD_REQUEST);
                }
                $file->store('public/pengumuman');
                $pengumuman->files()->create([
                    'file' => $file->hashName(),
                    'original_name' => $file->getClientOriginalName()
                ]);
            }
        }

        return $this->success($pengumuman);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pengumuman $pengumuman)
    {
        if (!(Auth::user()->checkPermissionTo('delete-pengumuman') && Auth::user()->id == $pengumuman->created_by)) {

            return $this->error(null, 'Tidak memiliki akses untuk menghapus pengumuman', Response::HTTP_FORBIDDEN);
        }

        $pengumuman->delete();
        return $this->success(null, Response::HTTP_NO_CONTENT);
    }
}
