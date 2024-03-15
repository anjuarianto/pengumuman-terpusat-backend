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
            'waktu' => date('d-m-Y H:i:s', strtotime($this->waktu)),
            'room_id' => $this->room_id,
            'created_by' => $this->dibuat_oleh->name,
            'penerima' => $this->pengumumanToUsers->map(function($pengumumanTo) {
                return ['penerima_id' => $pengumumanTo->penerima_id, 'is_single_user' => $pengumumanTo->is_single_user ? true : false];
            }),
            'penerima_fetched' => $this->usersFromPengumumanTo->map(function($user) {
                return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email];
            }),
            'can_reply' => in_array('reply-pengumuman',  Auth::user()->getPermissionsViaRoles()->pluck('name')->toArray()) &&
                ($this->pengumumanToUsers->contains('penerima_id', Auth::user()->id) || $this->created_by == Auth::user()->id),
            'can_edit' => in_array('edit-pengumuman',  Auth::user()->getPermissionsViaRoles()->pluck('name')->toArray()) &&
                $this->created_by == Auth::user()->id,
            'can_delete' => in_array('delete-pengumuman',  Auth::user()->getPermissionsViaRoles()->pluck('name')->toArray()) &&
                $this->created_by == Auth::user()->id,
        ];
    }

}
