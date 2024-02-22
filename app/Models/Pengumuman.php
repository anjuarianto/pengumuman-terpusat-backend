<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';

    protected $fillable = [
        'judul', 'konten', 'waktu', 'created_by', 'room_id'
    ];

    public function dibuat_oleh(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function room(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    public static function scopeSearch($query, $value) {
        $query->whereHas('dibuat_oleh', function($query) use ($value) {
            return $query->where("name", "LIKE", "%".$value."%");
        });
        $query->orWhereHas('room', function($query) use ($value) {
            return $query->where("name", "LIKE", "%".$value."%");
        });
        $query->orwhere('judul', 'LIKE', '%'.$value.'%');
        $query->orWhere('konten', 'LIKE', '%'.$value.'%');
        $query->orWhere('waktu', 'LIKE', '%'.$value.'%');

        return $query;
    }
}
