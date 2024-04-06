<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengumumanFile extends Model
{
    use HasFactory;

    protected $table = 'pengumuman_files';

    protected $fillable = [
        'pengumuman_id', 'file', 'original_name'
    ];
}
