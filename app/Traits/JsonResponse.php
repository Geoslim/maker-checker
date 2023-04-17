<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response;

trait JsonResponse
{
    public function successResponse(
        $data,
        $message = "Operation Successful",
        $statusCode = Response::HTTP_OK
    ): \Illuminate\Http\JsonResponse {
        $response = [
            "success" => true,
            "data" => $data,
            "message" => $message
        ];

        return response()->json($response, $statusCode);
    }

    public function success($message = "Operation Successful", $statusCode = Response::HTTP_OK): \Illuminate\Http\JsonResponse
    {
        $response = [
            "success" => true,
            "message" => $message
        ];

        return response()->json($response, $statusCode);
    }

    public function errorResponse($data = null, $message = null, $statusCode = Response::HTTP_BAD_REQUEST): \Illuminate\Http\JsonResponse
    {
        $response = [
            "success" => false,
            "message" => $message,
            "data" => $data
        ];

        return response()->json($response, $statusCode);
    }

    public function error($message = 'Operation Failed', $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR): \Illuminate\Http\JsonResponse
    {
        $response = [
            "success" => false,
            "message" => $message,
        ];

        return response()->json($response, $statusCode);
    }
}
