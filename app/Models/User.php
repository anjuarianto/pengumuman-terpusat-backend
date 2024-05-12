<?php

namespace App\Models;

use App\Traits\ExtendedHasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Collection;
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

    const DOSEN_DOMAIN = 'if.itera.ac.id';
    const MAHASISWA_DOMAIN = 'student.itera.ac.id';
    const TENDIK_DOMAIN = 'staff.itera.ac.id';

    protected $appends = [
        'role_name'
    ];

    public static function getRoleBasedOnEmailDomain($email)
    {
        $domain = substr(strrchr($email, "@"), 1); // Extract domain from email

        $firstKeyDomain = explode('.', $domain)[0];

        switch ($firstKeyDomain) {
            case 'student':
                return 'mahasiswa';
            case 'if':
                return 'dosen';
            case 'staff':
                return 'tendik';
            default:
                return null;

        }
    }

    public function assignRoleBasedOnEmailDomain()
    {
        $email = $this->email;
        $domain = substr(strrchr($email, "@"), 1); // Extract domain from email

        $firstKeyDomain = explode('.', $domain)[0];

        if ($firstKeyDomain == 'if') {
            $role = Role::where('name', 'dosen')->first();

            if ($role) {
                $this->roles()->syncWithoutDetaching($role->id);
            }
        } else if ($firstKeyDomain == 'staff') {
            $role = Role::where('name', 'tendik')->first();

            if ($role) {
                $this->roles()->syncWithoutDetaching($role->id);
            }
        } else if ($firstKeyDomain == 'student') {
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

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_has_members', 'user_id', 'room_id')->where('is_single_user', true)->whereNot('id', Room::GENERAL_ROOM_ID);
    }

    public function scopeGivenRole($query, $value)
    {
        if (is_array($value)) {
            foreach ($value as $role) {
                $emailNeedle = self::$rolesIdentify[$role];
                $query->where('email', 'LIKE', '%' . $emailNeedle . '%');
            }
        }

        if (!is_array($value)) {
            $emailNeedle = self::$rolesIdentify[$value];
            $query->where('email', 'LIKE', '%' . $emailNeedle . '%');
        }

        return $query;
    }

    public function checkPermissionTo($permission, $guardName = null): bool
    {
        return $this->hasPermissionTo($permission, $guardName);
    }

    public static function getUpcomingEvent($user_id)
    {
        $query = Pengumuman::filterByUser($user_id)->where('waktu', '>', date('Y-m-d H:i:s'));

        return $query->orderBy('waktu', 'asc')
            ->limit(7)->get();
    }

    public static function getPengumuman($user_id)
    {
        $query = Pengumuman::filterByUser($user_id);

        if (Auth::user()->hasRole('dosen')) {
            $query->orWhere('created_by', $user_id);
        }


        return $query->get();
    }


    public static function mySession()
    {
        return [
            'id' => Auth::user()->id,
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'role' => Auth::user()->getRoleNames()->first(),
            'permissions' => Auth()->user()->getAllPermissions()->pluck('name')->toArray(),
            'pengumuman' => self::getPengumuman(Auth::user()->id),
            'upcoming_event' => self::getUpcomingEvent(Auth::user()->id)
        ];
    }

    public function roleName(): Attribute
    {
        return Attribute::make(get: fn() => $this->getRoleBasedOnEmailDomain($this->email));
    }
}
