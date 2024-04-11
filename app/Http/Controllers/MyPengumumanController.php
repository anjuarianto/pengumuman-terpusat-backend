<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\HttpResponses;


class MyPengumumanController extends Controller
{
    use HttpResponses;

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, $date)
    {
        $data = Pengumuman::getByUserIdAndDate(Auth::id(), $date);

        if ($data->isEmpty()) {
            return $this->success(null, 200, 'Data tidak ditemukan');
        }

        return $this->success($data);
    }
}
