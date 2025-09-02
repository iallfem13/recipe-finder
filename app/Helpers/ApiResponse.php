<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Generate structure for successful response
     *
     * @param array $data
     * @param integer $code
     * 
     * @return JsonResponse
     */
    public static function success($data = [], int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => '',
            'data' => $data
        ], $code);
    }

    /**
     * Structure for error response
     *
     * @param string $message
     * @param array $data
     * @param integer $code
     *  
     * @return JsonResponse
     */
    public static function error(string $message = '', $data = [], int $code = 401): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => !empty($message) ? $message : 'An error ocurred while performing request',
            'data' => $data
        ], $code);
    }
}