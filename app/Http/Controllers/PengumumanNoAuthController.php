<?php

namespace App\Http\Controllers;

use App\Http\Resources\PengumumanResource;
use App\Models\Pengumuman;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class PengumumanNoAuthController extends Controller
{
    use HttpResponses;

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $pengumumans = Pengumuman::where('is_private', 0)->filter($request)
            ->orderBy('waktu', $request->order ?? 'desc')
            ->paginate();

        $pengumuman = PengumumanResource::collection($pengumumans)->response()->getData(true);

        return $this->success($pengumuman);
    }
}
