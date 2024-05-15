<?php

namespace App\Http\Controllers;

use App\Models\PengumumanFile;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeleteAttachment extends Controller
{
    use HttpResponses;

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, $id)
    {
        $attachment = PengumumanFile::findOrFail($id);

        DB::beginTransaction();
        try {
            $attachment->delete();
            unlink(storage_path('app/public/pengumuman/' . $attachment->file));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e, 'Failed to delete attachment', 500);
        }
        DB::commit();


        return $this->success(null, 200, 'Attachment deleted successfully');
    }
}
