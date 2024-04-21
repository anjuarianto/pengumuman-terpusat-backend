<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    use HasFactory;

    protected $table = 'user_group';

    protected $fillable = [
        'name'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_group_has_user', 'user_group_id', 'user_id');
    }

    public function pengumumanTo()
    {
        return $this->hasMany(PengumumanTo::class, 'penerima_id');
    }

    public static function all($id = null, $columns = ['*'])
    {
        // If $id is 1, get all users directly from the User model
        if ($id === 1) {
            $users = User::all($columns);
            $userGroups = static::all($columns);
            foreach ($userGroups as $userGroup) {
                $userGroup->setRelation('users', $users);
            }
            return $userGroups;
        }

        // Otherwise, retrieve user groups with their users
        return parent::all($columns)->load('users');
    }
}
