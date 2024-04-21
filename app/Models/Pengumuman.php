<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';

    protected $fillable = [
        'judul', 'konten', 'waktu', 'created_by', 'room_id'
    ];

    const GENERAL_ROOM_ID = 1;

    public static function storeData($data)
    {
        $transaction = DB::transaction(function () use ($data) {
            $pengumuman = Pengumuman::create([
                'judul' => $data->judul,
                'konten' => $data->konten,
                'waktu' => $data->waktu,
                'created_by' => Auth::user()->id,
                'room_id' => $data->room_id,
            ]);


            foreach ($data['recipients'] as $penerima) {
                PengumumanTo::create([
                    'pengumuman_id' => $pengumuman->id,
                    'is_single_user' => $penerima['is_single_user'],
                    'penerima_id' => $penerima['penerima_id']
                ]);
            }

            if (isset($data['files'])) {
                foreach ($data['files'] as $file) {
                    PengumumanFile::create([
                        'pengumuman_id' => $pengumuman->id,
                        'file' => $file['file'],
                        'original_name' => $file['original_name']
                    ]);
                }
            }
        });

        return $transaction;

    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pengumumanToUsers()
    {
        return $this->hasMany(PengumumanTo::class, 'pengumuman_id');
    }

    /**
     * Define a custom attribute to get users from pengumuman_to relation.
     */
    public function getUsersFromPengumumanToAttribute()
    {
        $usersCollection = collect([]);

        $this->pengumumanToUsers->each(function ($pengumumanTo) use ($usersCollection) {
            if ($pengumumanTo->is_single_user) {
                $usersCollection->push(User::find($pengumumanTo->penerima_id));
            } else {
                $usersCollection->push(UserGroup::find($pengumumanTo->penerima_id)->users);
            }
        });

        return $usersCollection->flatten();
    }

    public function dibuat_oleh(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function room(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PengumumanFile::class, 'pengumuman_id', 'id');
    }

    public static function scopeFilterSearch($query, $value)
    {
        $query->whereHas('dibuat_oleh', function ($query) use ($value) {
            return $query->where("name", "LIKE", "%" . $value . "%");
        });
        $query->orWhereHas('pengumumanToUsers.user', function ($query) use ($value) {
            return $query->where("name", "LIKE", "%" . $value . "%");
        })->orWhereHas('pengumumanToUsers.userGroup', function ($query) use ($value) {
            $query->whereHas('users', function ($query) use ($value) {
                return $query->where("name", "LIKE", "%" . $value . "%");
            });
        });
        $query->orwhere('judul', 'LIKE', '%' . $value . '%');
        $query->orWhere('konten', 'LIKE', '%' . $value . '%');
        $query->orWhere('waktu', 'LIKE', '%' . $value . '%');

        return $query;
    }

    public static function scopeFilterRoom($query, $room_id)
    {
        if ($room_id) {
            return $query->where('room_id', $room_id);
        }

        return $query->where('room_id', self::GENERAL_ROOM_ID);
    }

    public static function scopeFilterDate($query, $minDate, $maxDate)
    {
        if ($minDate) {
            $query->whereDate('waktu', '>=', Carbon::parse($minDate)->format('Y-m-d'));
        }

        if ($maxDate) {
            $query->whereDate('waktu', '<=', Carbon::parse($maxDate)->format('Y-m-d'));
        }

        return $query;
    }

    public static function scopeFilterPengirim($query, $pengirim)
    {
        if (Auth::user()->can('create-pengumuman')) {
            $query->whereHas('dibuat_oleh', function ($query) {
                return $query->where("id", Auth::user()->id);
            });
        }


        if ($pengirim) {
            $query->whereHas('dibuat_oleh', function ($query) use ($pengirim) {
                return $query->where("id", $pengirim);
            });
        }

        return $query;
    }

    public static function scopeFilterPenerima($query, $penerima_id)
    {
        $query->orWhereHas('pengumumanToUsers.user', function ($query) use ($penerima_id) {
            $query->where('id', $penerima_id);
        })->orWhereHas('pengumumanToUsers.userGroup', function ($query) use ($penerima_id) {
            $query->whereHas('users', function ($query) use ($penerima_id) {
                $query->where('id', $penerima_id);
            });
        });

        if ($penerima_id) {
            $query->whereHas('pengumumanToUsers.user', function ($query) use ($penerima_id) {
                $query->whereIn('id', $penerima_id);
            })->orWhereHas('pengumumanToUsers.userGroup', function ($query) use ($penerima_id) {
                $query->whereHas('users', function ($query) use ($penerima_id) {
                    $query->whereIn('id', $penerima_id);
                });
            });
        }

        return $query;
    }

    public static function scopeFilterFile($query, $file_name)
    {
        if ($file_name) {
            $query->whereHas('files', function ($query) use ($file_name) {
                return $query->where("original_name", "LIKE", "%" . $file_name . "%");
            });
        }

        return $query;
    }

    public static function getByUserId($userId)
    {
        return self::whereHas('pengumumanToUsers', function ($query) use ($userId) {
            $query->where(function ($query) use ($userId) {
                $query->where('penerima_id', $userId)
                    ->where('is_single_user', true);
            })->orWhere(function ($query) use ($userId) {
                $query->whereHas('userGroup', function ($query) use ($userId) {
                    $query->where('id', $userId);
                })->where('is_single_user', false);
            });
        })->get();
    }

    public static function getByUserIdAndDate($userId, $date)
    {
        return self::whereHas('pengumumanToUsers', function ($query) use ($userId) {
            $query->where(function ($query) use ($userId) {
                $query->where('penerima_id', $userId)
                    ->where('is_single_user', true);
            })->orWhere(function ($query) use ($userId) {
                $query->whereHas('userGroup', function ($query) use ($userId) {
                    $query->where('id', $userId);
                })->where('is_single_user', false);
            });
        })->whereDate('waktu', $date)->get();
    }

    public static function notificationDaily()
    {
        $result = collect([]);
        $kondisiHari = [0, 1, 3, 7];


        foreach ($kondisiHari as $hari) {
            $now = now();
            $now->setTimezone('Asia/Jakarta');
            $now->addDays($hari);

            $pengumumans = Pengumuman::whereYear('waktu', $now->year)
                ->whereMonth('waktu', $now->month)
                ->whereDay('waktu', $now->day)
                ->where(DB::raw('MINUTE(waktu)'), $now->minute)
                ->get();

            $pengumumans->map(function ($pengumuman) use ($result, $hari) {
                $pengumuman->type = $hari . ' Day';
                $result->push($pengumuman);
            });
        }

        $result->each(function ($pengumuman) {
            $pengumuman->load('pengumumanToUsers');

            $pengumuman->usersFromPengumumanTo = $pengumuman->getUsersFromPengumumanToAttribute();
        });

        return $result;
    }
}
