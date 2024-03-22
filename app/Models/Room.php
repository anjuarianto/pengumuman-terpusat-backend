<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $hidden = ['pivot'];

    protected $table = 'room';

    protected $fillable = [
        'name', 'description', 'is_single_user'
    ];

    const GENERAL_ROOM_ID = 1;

    public function members()
    {
        return $this->hasMany(RoomHasMembers::class, 'room_id');
    }

    public function getUsersFromRoomAttribute()
    {
        $usersCollection = collect([]);

        if ($this->id === self::GENERAL_ROOM_ID) {
            User::givenRole('mahasiswa')->get()->each(function ($user) use ($usersCollection) {
                $user->is_single_user = true;
                $usersCollection->push($user);
            });

            UserGroup::get()->each(function ($userGroup) use ($usersCollection) {
                $userGroup->is_single_user = false;
                $usersCollection->push($userGroup);
            });

            return $usersCollection->flatten();
        }

        $this->members->each(function ($roomMembers) use ($usersCollection) {
            if ($roomMembers->is_single_user) {
                $user = User::find($roomMembers->user_id);
                $user->is_single_user = true;
                $usersCollection->push($user);

            } else {
                $userGroup = UserGroup::find($roomMembers->user_id);
                $userGroup->is_single_user = false;
                $usersCollection->push($userGroup);
            }
        });

        return $usersCollection->flatten();
    }

}
