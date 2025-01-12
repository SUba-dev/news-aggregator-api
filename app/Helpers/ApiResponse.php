<?php

namespace App\Helpers;
use Illuminate\Http\Response;

class ApiResponse
{
    public static function success($data = null, $code = 200)
    {
        return response()->json([
            'status' => true,
            'data' => $data,
        ], $code);
    }

    public static function error($message = 'Something went wrong', $code = 500, $errors = null)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    public static function notFound()
    {
        return response()->json([
            'status' => false,
            'message' => 'Record not found.',
        ], Response::HTTP_NOT_FOUND);
    }
}
