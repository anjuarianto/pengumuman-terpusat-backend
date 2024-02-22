<?php
namespace App\Traits;

use Laravel\Sanctum\HasApiTokens as SanctumHasApiTokens;
use Illuminate\Support\Facades\Auth;

trait ExtendedHasApiTokens
{
    use SanctumHasApiTokens {
        createToken as sanctumCreateToken;
    }

    public function createToken($name, array $abilities = ['*'])
    {
        $token = $this->sanctumCreateToken($name, $abilities);

        // Access the authenticated user
        $user = Auth::user();

        // Assign roles based on the user's email domain
        $user->assignRoleBasedOnEmailDomain();

        return $token;
    }
}


?>
