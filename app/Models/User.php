<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\ExtendedHasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use ExtendedHasApiTokens, HasFactory, Notifiable, HasRoles;

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
                $this->roles()->syncRoles($role->id);
            }
        }

    }


}
