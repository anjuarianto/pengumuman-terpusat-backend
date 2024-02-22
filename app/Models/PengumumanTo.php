<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengumumanTo extends Model
{
    use HasFactory;

    protected $table = 'pengumuman_to';

    protected $fillable = [
        'pengumuman_id', 'is_single_user', 'penerima_id'
    ];
    
}
