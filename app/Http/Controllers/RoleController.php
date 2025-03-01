<?php

namespace App\Http\Controllers;


use App\Models\Otp;
use App\Models\Role;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{

    public function getRoles(): JsonResponse
    {
        try {

            $roles = Role::get();

            return handleResponse($roles, 'Roles fetched successfully', true, 200);

        } catch (\Throwable $err) {
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }


    public function userRegister(): JsonResponse
    {
        try {

            $validated = Validator::make(request()->all(), [
                'name' => ['required', 'string'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'string'],
                'role_id' => ['required', 'exists:roles,id'],
                'longitude' => ['required'],
                'latitude' => ['required']
            ]);

            if ($validated->fails()) {
                return handleResponse([], $validated->errors()->first(), false, 400);
            }

            $user = User::create([
                'name' => request('name'),
                'email' => request('email'),
                'password' => bcrypt(request('password')),
                'role_id' => request('role_id'),
                'longitude' => request('longitude'),
                'latitude' => request('latitude'),
            ]);

            $user->token = $user->createToken('auth')->plainTextToken;

            return handleResponse($user, 'User created successfully', true, 201);

        } catch (\Throwable $err) {
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }



    public function genUserToken(): JsonResponse
    {
        try {

            $validated = Validator::make(request()->all(), [
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
            ]);

            if ($validated->fails()) {
                return handleResponse([], $validated->errors()->first(), false, 400);
            }

            $user = User::where('email', request('email'))->first();

            if ($user) {
                if (Hash::check(request('password'), $user->password)) {
                    $user->token = $user->createToken('auth')->plainTextToken;
                    return handleResponse($user, 'Token generated successfully', true, 200);
                } else {
                    return handleResponse([], 'Invalid password', false, 400);
                }
            } else {
                return handleResponse([], 'User not found. Kindly Register', false, 400);
            }


        } catch (\Throwable $err) {
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }


    public function logout(): JsonResponse
    {
        try {
            request()->user()->tokens()->delete();
            return handleResponse([], 'User logged out successfully', true, 200);
        } catch (\Throwable $err) {
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }


    public function verifyEmail(): JsonResponse
    {
        try {
            $validated = Validator::make(request()->all(), [
                'email' => ['required', 'email', 'exists:users,email'],
            ]);

            if ($validated->fails()) {
                return handleResponse([], $validated->errors()->first(), false, 400);
            }

            $user = User::where('email', request('email'))->first();

            if ($user) {
                $otp = Otp::generateOtp($user->id, $user->email, 'EMAIL VERIFICATION');

                Log::info(json_encode($otp));

                if ($otp) {
                    $send = (new NotificationService())->sendNotification($user->email, $user->name, '<p>This Email Is To Very your Account Before Allowing you To Change Your Password. Your OTP is: ' . $otp->otp . '</p>', 'Email Verification');

                    if ($send['status']) {
                        return handleResponse([], $send['message'], true, 200);
                    }

                    return handleResponse([], "Failed To Send Email", false, 400);
                } else {
                    return handleResponse([], 'Failed to generate OTP', false, 400);
                }
            } else {
                return handleResponse([], 'User not found', false, 400);
            }


        } catch (\Throwable $err) {
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }


    public function verifyAndUpdatePassword(): JsonResponse
    {
        try {
            $validated = Validator::make(request()->all(), [
                'email' => ['required', 'email', 'exists:users,email'],
                'password' => ['required', 'confirmed'],
                'otp' => 'required',
            ]);

            if ($validated->fails()) {
                return handleResponse([], $validated->errors()->first(), false, 400);
            }

            $user = User::where('email', request('email'))->first();


            $verify = Otp::verifyOtp($user->id, request('otp'));

            if ($verify) {
                $user->password = bcrypt(request('password'));
                $user->save();
                return handleResponse($user, 'Password updated successfully', true, 200);
            } else {
                return handleResponse([], 'Invalid OTP.Kindly Request for Another', false, 400);
            }


        } catch (\Throwable $err) {
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }

}
