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
        'judul', 'konten', 'waktu', 'created_by', 'room_id', 'is_private'
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


            if (isset($data['recipients'])) {
                foreach ($data['recipients'] as $penerima) {
                    PengumumanTo::create([
                        'pengumuman_id' => $pengumuman->id,
                        'is_single_user' => $penerima['is_single_user'],
                        'penerima_id' => $penerima['penerima_id']
                    ]);
                }
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

    public static function scopefilterByUser($query, $user_id)
    {
        $query->where('is_private', 1)->whereHas('pengumumanToUsers', function ($query) use ($user_id) {
            $query->where(function ($query) use ($user_id) {
                $query->where('is_single_user', 1)->whereHas('user', function ($query) use ($user_id) {
                    $query->whereIn('id', [$user_id]);
                });
                $query->orWhere('is_single_user', 0)->whereHas('userGroup', function ($query) use ($user_id) {
                    $query->whereHas('users', function ($query) use ($user_id) {
                        $query->whereIn('id', [$user_id]);
                    });
                });
            });
        })->orWhere('is_private', 0);

        if (Auth::user()->hasRole('dosen') || Auth::user()->hasRole('tendik')) {
            $query->orWhere('created_by', $user_id);
        }

        return $query;
    }

    public static function scopeFilter($query, $request)
    {
        if (Auth::user()) {
            $user_id = Auth::user()->id;

            $query->filterByUser($user_id);

        }

        if ($request->has('search')) {
            $query->filterSearch($request->search);
        }

        if ($request->has('is_private')) {
            $query->where('is_private', $request->is_private);
        }

        if ($request->has('room_id')) {
            $query->filterRoom($request->room_id);
        }

        if ($request->has('min_date') || $request->has('max_date')) {
            $query->filterDate($request->min_date, $request->max_date);
        }

        if ($request->has('pengirim')) {
            $query->filterPengirim($request->pengirim);
        }

        if ($request->has('penerima_id')) {
            $query->filterPenerima($request->penerima_id);
        }

        if ($request->has('file_name')) {
            $query->filterFile($request->file_name);
        }
    }

    public static function scopeFilterSearch($query, $value)
    {
        if ($value) {
            $query->where(function ($q) use ($value) {
                $q->where('judul', 'LIKE', '%' . $value . '%')
                    ->orWhere('konten', 'LIKE', '%' . $value . '%');
            });
        }

        return $query;
    }

    public static function scopeFilterRoom($query, $room_id)
    {
        if ($room_id) {
            return $query->where('room_id', $room_id);
        }
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
        if ($pengirim) {
            $query->where('created_by', $pengirim);
        }


        return $query;
    }

    public static function scopeFilterPenerima($query, $penerima_id)
    {
        $query->whereHas('pengumumanToUsers.user', function ($query) use ($penerima_id) {
            $query->whereIn('id', $penerima_id);
        })->orWhereHas('pengumumanToUsers.userGroup', function ($query) use ($penerima_id) {
            $query->whereHas('users', function ($query) use ($penerima_id) {
                $query->whereIn('id', $penerima_id);
            });
        });

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

    public static function getByUserIdAndDate($userId, $date)
    {
        $query = self::filterByUser($userId);

        return $query->whereDate('waktu', $date)->get();
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
