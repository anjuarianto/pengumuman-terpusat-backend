<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomHasMembers extends Model
{
    use HasFactory;

    public $table = 'room_has_members';

    public $timestamps = false;

}
