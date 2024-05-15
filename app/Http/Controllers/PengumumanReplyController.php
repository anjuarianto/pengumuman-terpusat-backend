<?php

namespace App\Http\Controllers;

use App\Http\Resources\PengumumanReplyResource;
use App\Jobs\NotifikasiReplyBaruJob;
use App\Models\Pengumuman;
use App\Models\PengumumanComment;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PengumumanReplyController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $pengumuman_id)
    {
        if (!Auth::user()->checkPermissionTo('view-pengumuman-reply')) {
            return $this->error(null, 'Tidak memiliki akses untuk melihat balasan pengumuman', Response::HTTP_FORBIDDEN);
        }

        $comments = PengumumanComment::where('pengumuman_id', $pengumuman_id)->get();

        return $this->success(PengumumanReplyResource::collection($comments));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $pengumuman_id)
    {
        if (!Auth::user()->checkPermissionTo('create-pengumuman-reply')) {
            return $this->error(null, 'Tidak memiliki akses untuk membuat balasan pengumuman', Response::HTTP_FORBIDDEN);
        }

        $pengumuman = Pengumuman::find($pengumuman_id);

        if (!$pengumuman) {
            return $this->error(null, 'Pengumuman tidak ditemukan', Response::HTTP_NOT_FOUND);
        }

        if ($pengumuman->is_private) {
            if (!$pengumuman->pengumumanToUsers->contains('penerima_id', Auth::user()->id || $pengumuman->created_by != Auth::user()->id)) {
                return $this->error(null, 'Tidak memiliki akses untuk membuat balasan pengumuman', Response::HTTP_FORBIDDEN);
            }
        }


        $comment = PengumumanComment::create([
            'pengumuman_id' => $pengumuman->id,
            'user_id' => $request->user_id,
            'comment' => $request->comment
        ]);

        $comment = PengumumanComment::with('pengumuman')->findOrFail($comment->id);

        NotifikasiReplyBaruJob::dispatch($comment)->onQueue('default');

        return $this->success(new PengumumanReplyResource($comment));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $pengumuman_id, $id)
    {
        if (!Auth::user()->checkPermissionTo('edit-pengumuman-reply')) {
            return $this->error(null, 'Tidak memiliki akses untuk membuat balasan pengumuman', Response::HTTP_FORBIDDEN);
        }


        $comment = PengumumanComment::find($id);

        if (!$comment) {
            return $this->error(null, 'Balasan pengumuman tidak ditemukan', Response::HTTP_NOT_FOUND);
        }

        if ($comment->user_id != Auth::user()->id) {
            return $this->error(null, 'Tidak memiliki akses untuk update balasan pengumuman', Response::HTTP_FORBIDDEN);
        }


        $comment->update([
            'comment' => $request->comment
        ]);

        return $this->success(new PengumumanReplyResource($comment), Response::HTTP_ACCEPTED, 'Balasan pengumuman berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($pengumuman_id, $pengumuman_comment_id)
    {
        if (!Auth::user()->checkPermissionTo('delete-pengumuman-reply')) {
            return $this->error(null, 'Tidak memiliki akses untuk membuat balasan pengumuman', Response::HTTP_FORBIDDEN);
        }

        $pengumumanComment = PengumumanComment::where('pengumuman_id', $pengumuman_id)->where('id', $pengumuman_comment_id)->first();

        if (!$pengumumanComment) {
            return $this->error(null, 'Balasan pengumuman tidak ditemukan', Response::HTTP_NOT_FOUND);
        }


        if ($pengumumanComment->user_id != Auth::user()->id) {
            return $this->error(null, 'Tidak memiliki akses untuk menghapus balasan pengumuman', Response::HTTP_FORBIDDEN);
        }

        $pengumumanComment->delete();
        return $this->success(null, Response::HTTP_NO_CONTENT);
    }
}
