<?php

namespace App\Http\Resources;

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
            'is_private' => $this->is_private,
            'penerima' => $this->pengumumanToUsers->map(function ($pengumumanTo) {
                return ['name' => $pengumumanTo->is_single_user ? $pengumumanTo->user->name : $pengumumanTo->userGroup->name, 'penerima_id' => $pengumumanTo->penerima_id, 'is_single_user' => $pengumumanTo->is_single_user ? true : false];
            }),
            'files' => $this->files->map(function ($file) {
                return ['file' => $file->file, 'original_name' => $file->original_name];
            }),
            'can_reply' => $this->canReply(),
            'can_edit' => $this->canEdit(),
            'can_delete' => $this->canDelete(),
            'created_at' => date('Y-m-d H:i:s', strtotime($this->created_at)),
        ];
    }

    public function canEdit()
    {
        if (!auth()->user()) {
            return false;
        }

        if (!auth()->user()->can('edit-pengumuman')) {
            return false;
        }

        if (!auth()->user()->id != $this->created_by) {
            return false;
        }

        return true;
    }

    public function canReply()
    {
        if (!auth()->user()) {
            return false;
        }

        if (!auth()->user()->can('create-pengumuman-reply')) {
            return false;
        }

        if (!$this->usersFromPengumumanTo->contains('id', Auth::user()->id) || $this->created_by != Auth::user()->id) {
            return false;
        }

        return true;
    }

    public function canDelete()
    {
        if (!auth()->user()) {
            return false;
        }

        if (!auth()->user()->can('delete-pengumuman')) {
            return false;
        }

        if (!auth()->user()->id != $this->created_by) {
            return false;
        }

        return true;
    }
}
