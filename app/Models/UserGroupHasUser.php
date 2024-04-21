<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroupHasUser extends Model
{
    use HasFactory;

    protected $table = 'user_group_has_user';

    protected $fillable = [
        'user_group_id', 'user_id'
    ];

    public function user()
    {
        if ($this->user_group_id == 1) {
            return User::all();
        }
//        return $this->hasMany(User::class, 'id', 'user_id');
    }
}
