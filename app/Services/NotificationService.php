<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class NotificationService
{
    public function __construct()
    {
        //
    }


    public function sendNotification($to, $name, $message, $subject): array
    {
        try {

            $request = [
                "sender" => [
                    'name' => "DanielEgbeleke_Food_Delivery",
                    'email' => 'www.daniko15@gmail.com',
                ],
                "to" => [
                    [
                        "email" => $to,
                        "name" => $name,
                    ],
                ],
                "subject" => $subject,
                "htmlContent" => $message
            ];


            $response = retry(2, function () use ($request) {
                return Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'api-key' => config('app.BREVO_API_KEY')
                ])->post(config('app.BREVO_URL'), $request);
            }, 1000);

            if($response->failed()){
                Log::error('Failed to send email' . $response);
                return [
                    'status' => false,
                    'message' => $response->json()['message'] ?? 'Failed to send email',
                    'data' => []
                ];
            }

            $response_data = $response->json();

            Log::info('Email sent successfully' . $response);

            return [
                'status' => true,
                'message' => $response_data['message'] ?? 'Email sent successfully',
                'data' => $response_data
            ];

        } catch (\Throwable $err) {
            return [
                'status' => false,
                'message' => $err->getMessage(),
                'data' => []
            ];
        }
    }
}
