<?php


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

function handleResponse($data, string|array $message, bool $status, int $statusCode): JsonResponse
{
    return response()->json([
        'status' => $status,
        'message' => $message,
        'data' => $data,
        'statusCode' => $statusCode
    ], $statusCode);
}


function generateCode(): string
{
    try {
        return random_int(100000, 999999);
    } catch (\Throwable $err) {
        return null;
    }
}


function generateOrderNumber(): string
{
    try {
        return "VG_" .random_int(100000000, 999999999);

    } catch (\Throwable $err) {
        return null;
    }
}


function haversineDistance($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371; // Earth's radius in km

    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    $dLat = $lat2 - $lat1;
    $dLon = $lon2 - $lon1;

    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos($lat1) * cos($lat2) *
         sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadius * $c; // Distance in km
}

