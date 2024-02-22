<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';

    protected $fillable = [
        'judul', 'konten', 'waktu', 'created_by'
    ];

    public function dibuat_oleh() {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public static function scopeSearch($query, $value) {
        $query->whereHas('dibuat_oleh', function($query) use ($value) {
            return $query->where("name", "LIKE", "%".$value."%");
        });
        
        $query->orwhere('judul', 'LIKE', '%'.$value.'%');
        $query->orWhere('konten', 'LIKE', '%'.$value.'%');
        $query->orWhere('waktu', 'LIKE', '%'.$value.'%');
        
        return $query;
    }
}
