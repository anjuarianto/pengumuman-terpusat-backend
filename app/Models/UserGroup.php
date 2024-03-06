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

    public function pengumumanTo() {
        return $this->hasMany(PengumumanTo::class, 'penerima_id');
    }
}
