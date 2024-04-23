<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomHasMembers extends Model
{
    use HasFactory;

    public $table = 'room_has_members';

    public $fillable = [
        'room_id', 'user_id', 'is_single_user'
    ];

    public $timestamps = false;


    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}
