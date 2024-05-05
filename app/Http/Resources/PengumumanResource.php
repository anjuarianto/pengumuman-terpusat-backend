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
            'can_reply' => Auth::user() && Auth::user()->can('create-pengumuman-reply') &&
                ($this->usersFromPengumumanTo->contains('id', Auth::user()->id) || $this->created_by == Auth::user()->id),
            'can_edit' => Auth::user() && Auth::user()->can('edit-pengumuman') && $this->created_by == Auth::user()->id,
            'can_delete' => Auth::user() && Auth::user()->can('delete-pengumuman') && $this->created_by == Auth::user()->id,
            'created_at' => Auth::user() && date('Y-m-d H:i:s', strtotime($this->created_at)),
        ];
    }

}
