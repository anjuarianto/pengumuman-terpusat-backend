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

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pengumumanToUsers()
    {
        return $this->hasMany(PengumumanTo::class, 'pengumuman_id');
    }

    /**
     * Define a custom attribute to get users from pengumuman_to relation.
     */
    public function getUsersFromPengumumanToAttribute()
    {
        $usersCollection = collect([]);

        $this->pengumumanToUsers->each(function ($pengumumanTo) use ($usersCollection) {
            if ($pengumumanTo->is_single_user) {
                $usersCollection->push(User::find($pengumumanTo->penerima_id));
            } else {
                $usersCollection->push(UserGroup::find($pengumumanTo->penerima_id)->users);
            }
        });

        return $usersCollection->flatten();
    }

    public function dibuat_oleh(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function room(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    public static function scopeFilterSearch($query, $value) {
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

    public static function scopeFilterRoom($query, $room_id) {
        if($room_id) {
            $query->where('room_id', $room_id);
        }

        return $query;
    }

    public static function getByUserId($userId)
    {
        return self::whereHas('pengumumanToUsers', function ($query) use ($userId) {
            $query->where(function ($query) use ($userId) {
                $query->where('penerima_id', $userId)
                    ->where('is_single_user', true);
            })->orWhere(function ($query) use ($userId) {
                $query->whereHas('user.users', function ($query) use ($userId) {
                    $query->where('id', $userId);
                })->where('is_single_user', false);
            });
        })->get();
    }
}
