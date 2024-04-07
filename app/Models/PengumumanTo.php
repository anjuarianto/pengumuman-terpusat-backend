<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class PengumumanTo extends Model
{
    use HasFactory;

    protected $table = 'pengumuman_to';

    protected $fillable = [
        'pengumuman_id', 'is_single_user', 'penerima_id'
    ];

    public function pengumuman()
    {
        return $this->belongsTo(Pengumuman::class, 'pengumuman_id');
    }

    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class, 'penerima_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }
}
