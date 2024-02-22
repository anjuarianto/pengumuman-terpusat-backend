<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengumumanComment extends Model
{
    use HasFactory;

    protected $table = 'pengumuman_comment';

    protected $fillable = [
        'pengumuman_id', 'user_id', 'comment'
    ];
    
}
