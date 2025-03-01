<?php

namespace App\Http\Controllers;

use App\Models\Resturant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResturantController extends Controller
{
    public function createResturant(): JsonResponse
    {
        try {

            $auth_user = request()->user();

            if ($auth_user->isAdmin()) {
                $validated = Validator::make(request()->all(), [
                    'name' => ['required', 'string'],
                    'longitude' => ['required'],
                    'latitude' => ['required'],
                    'phone' => ['required']
                ]);

                if ($validated->fails()) {
                    return handleResponse([], $validated->errors()->first(), false, 400);
                }

                $resturant = Resturant::create([
                    'name' => request('name'),
                    'longitude' => request('longitude'),
                    'latitude' => request('latitude'),
                    'phone' => request('phone')
                ]);

                return handleResponse($resturant, 'Resturant created successfully', true, 201);
            }

            return handleResponse([], 'User UnAuthorized', false, 401);

        } catch (\Throwable $err) {
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }


    public function getResturants(): JsonResponse
    {
        try {

            $auth_user = auth()->user();

            if ($auth_user->isAdmin()) {
                $resturants = Resturant::get();

                return handleResponse($resturants, 'Resturants fetched successfully', true, 200);
            }

            return handleResponse([], 'User UnAuthorized', false, 401);

        } catch (\Throwable $err) {
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }


    public function getOneResturant($id): JsonResponse
    {
        try{

            $auth_user = auth()->user();

            if ($auth_user->isAdmin()) {
                $resturant = Resturant::find($id);

                if (!$resturant) {
                    return handleResponse([], 'Resturant not found', false, 404);
                }

                return handleResponse($resturant, 'Resturant fetched successfully', true, 200);
            }

            return handleResponse([], 'User UnAuthorized', false, 401);

        }catch(\Throwable $err){
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }


    public function updateResturant($id): JsonResponse
    {
        try{

            $auth_user = auth()->user();

            if ($auth_user->isAdmin()) {
                $resturant = Resturant::find($id);

                if (!$resturant) {
                    return handleResponse([], 'Resturant not found', false, 404);
                }

                $validated = Validator::make(request()->all(), [
                    'name' => ['nullable', 'string', 'unique:resturants,name,'.$id],
                    'longitude' => ['nullable', 'unique:resturants,longitude,'. $id],
                    'latitude' => ['nullable', 'unique:resturants,latitude,'. $id],
                    'phone' => ['nullable', 'unique:resturants,phone,'. $id],
                    'is_booked' => ['nullable', 'boolean']
                ]);

                if ($validated->fails()) {
                    return handleResponse([], $validated->errors()->first(), false, 400);
                }

                $resturant->update([
                    'name' => request('name') ?? $resturant->name,
                    'longitude' => request('longitude') ?? $resturant->longitude,
                    'latitude' => request('latitude') ?? $resturant->latitude,
                    'phone' => request('phone') ?? $resturant->phone,
                    'is_booked' => request('is_booked') ?? $resturant->is_booked
                ]);

                $resturant->refresh();

                return handleResponse($resturant, 'Resturant updated successfully', true, 200);
            }

            return handleResponse([], 'User UnAuthorized', false, 401);

        }catch(\Throwable $err){
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }


    public function deleteOneResturant(): JsonResponse
    {
        try{

            $auth_user = auth()->user();

            if ($auth_user->isAdmin()) {
                $resturant = Resturant::find(request('id'));

                if (!$resturant) {
                    return handleResponse([], 'Resturant not found', false, 404);
                }

                $resturant->delete();

                return handleResponse([], 'Resturant deleted successfully', true, 200);
            }

            return handleResponse([], 'User UnAuthorized', false, 401);

        }catch(\Throwable $err){
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }
}
