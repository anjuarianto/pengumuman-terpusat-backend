<?php

namespace App\Models;

use App\Traits\ExtendedHasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Traits\HasPermissions;

class User extends Authenticatable
{
    use ExtendedHasApiTokens, HasFactory, Notifiable, HasRoles, HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function assignRoleBasedOnEmailDomain()
    {
        $email = $this->email;
        $domain = substr(strrchr($email, "@"), 1); // Extract domain from email

        $firstKeyDomain = explode('.', $domain)[0];

        if($firstKeyDomain == 'if') {
            $role = Role::where('name', 'dosen')->first();

            if ($role) {
                $this->roles()->syncWithoutDetaching($role->id);
            }
        } else if($firstKeyDomain == 'student') {
            $role = Role::where('name', 'mahasiswa')->first();
            if ($role) {
                $this->roles()->syncWithoutDetaching($role->id);
            }
        }
    }

    public function groups()
    {
        return $this->belongsToMany(UserGroup::class, 'user_group_has_user', 'user_id', 'user_group_id');
    }

    public function rooms() {
        return $this->belongsToMany(Room::class, 'room_has_members', 'user_id', 'room_id')->where('is_single_user', true);
    }

    public function checkPermissionTo($permission, $guardName = null): bool
    {
        return $this->hasPermissionTo($permission, $guardName);
    }

    public static function getMyDashboardData($user_id) {
        $user = User::with(['rooms' => function ($query) {
            $query->select('id', 'name');
        }])->find($user_id);

        $pengumumans = Pengumuman::getByUserId($user_id)->map(function ($pengumuman) {

            return $pengumuman->only(['id', 'judul', 'waktu']);
        });

        $user->pengumuman = $pengumumans;

        return $user;

    }
}
