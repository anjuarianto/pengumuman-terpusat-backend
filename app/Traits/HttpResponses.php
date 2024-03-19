<?php

namespace App\Traits;


use Illuminate\Http\Response;

trait HttpResponses {
    protected function success($data, $code = Response::HTTP_OK, $message = null): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function error($data, $message = null, $code = Response::HTTP_BAD_REQUEST): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
