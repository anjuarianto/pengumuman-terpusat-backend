<?php

namespace App\Http\Resources;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse;
use Illuminate\Support\Facades\Auth;

class PengumumanResource extends JsonResource
{
    public static $wrap = 'items';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'judul' => $this->judul,
            'konten' => $this->konten,
            'waktu' => date('Y-m-d H:i:s', strtotime($this->waktu)),
            'room' => $this->room->only('id', 'name'),
            'created_by' => $this->dibuat_oleh->name,
            'is_private' => (int)$this->is_private,
            'penerima' => $this->pengumumanToUsers->map(function ($pengumumanTo) {
                return ['name' => $pengumumanTo->is_single_user ? $pengumumanTo->user->name : $pengumumanTo->userGroup->name, 'penerima_id' => $pengumumanTo->penerima_id, 'is_single_user' => $pengumumanTo->is_single_user ? true : false];
            }),
            'files' => $this->files->map(function ($file) {
                return ['id' => $file->id, 'file' => $file->file, 'original_name' => $file->original_name];
            }),
            'can_reply' => $this->canReply($request),
            'can_edit' => $this->canEdit($request),
            'can_delete' => $this->canDelete($request),
            'created_at' => date('Y-m-d H:i:s', strtotime($this->created_at)),
        ];
    }

    public function canEdit($request)
    {
        if (!Auth::user()) {
            return false;
        }

        if (!Auth::user()->can('edit-pengumuman')) {
            return false;
        }

        if ($request->user()->id != $this->created_by) {
            return false;
        }

        return true;
    }

    public function canReply($request)
    {
        if (!Auth::user()) {
            return false;
        }

        if (!Auth::user()->can('create-pengumuman-reply')) {
            return false;
        }

        if (!(Pengumuman::filterByUser(Auth::user()->id)->exists() || $this->created_by == Auth::user()->id)) {
            return false;
        }

        return true;
    }

    public function canDelete($request)
    {
        if (!Auth::user()) {
            return false;
        }

        if (!Auth::user()->can('delete-pengumuman')) {
            return false;
        }

        if (Auth::user()->id != $this->created_by) {
            return false;
        }

        return true;
    }
}
