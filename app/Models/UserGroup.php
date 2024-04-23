<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    use HasFactory;

    protected $table = 'user_group';

    protected $fillable = [
        'name'
    ];

    protected $appends = [
        'user'
    ];

    const DOSEN_ID = 1;
    const TENDIK_ID = 2;
    const MAHASISWA_ID = 3;

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_group_has_user', 'user_group_id', 'user_id');
    }

    public function pengumumanTo()
    {
        return $this->hasMany(PengumumanTo::class, 'penerima_id');
    }

    public function user(): Attribute
    {
        return Attribute::make(get: function () {
            switch ($this->id) {
                case self::DOSEN_ID:
                    return User::where("email", "LIKE", "%" . User::DOSEN_DOMAIN . "%")->get();
                case self::TENDIK_ID:
                    return User::where("email", "LIKE", "%" . User::TENDIK_DOMAIN . "%")->get();
                case self::MAHASISWA_ID:
                    return User::where("email", "LIKE", "%" . User::MAHASISWA_DOMAIN . "%")->get();
                default:
                    $id = UserGroupHasUser::where("user_group_id", $this->id)->pluck('user_id');
                    return User::whereIn('id', $id)->get();
            }
        });
    }
}
