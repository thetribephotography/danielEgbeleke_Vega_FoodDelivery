<?php

namespace App\Http\Controllers;

use App\Models\Menue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MenueController extends Controller
{
    public function createMenue(): JsonResponse
    {
        try {
            $auth_user = auth()->user();

            if ($auth_user->isAdmin()) {

                $validated = Validator::make(request()->all(), [
                    'name' => ['required', 'string'],
                    'description' => ['required', 'string'],
                    'food_id' => ['required', 'array'],
                    'food_id.*' => ['required', Rule::exists('food', 'id')]
                ]);

                if ($validated->fails()) {
                    return handleResponse([], $validated->errors()->first(), false, 400);
                }

                $menue = Menue::create([
                    'name' => request('name'),
                    'description' => request('description'),
                ]);

                $food_ids = request('food_id');

                $menue->foods()->sync($food_ids);

                return handleResponse($menue, 'Menue created successfully', true, 201);
            }

            return handleResponse([], "Unauthorized", false, 401);

        } catch (\Throwable $err) {
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }


    public function getMenues(): JsonResponse
    {
        try {

            $auth_user = auth()->user();

            if ($auth_user->isAdmin()) {
                $menues = Menue::with('foods')->get();

                return handleResponse($menues, 'Menues fetched successfully', true, 200);
            } elseif ($auth_user->isUser()) {
                $menues = Menue::with('foods')->where('status', 'active')->get();

                return handleResponse($menues, 'Menues fetched successfully', true, 200);
            }

            return handleResponse([], 'Unauthorized', false, 401);

        } catch (\Throwable $err) {
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }


    public function findOneMenue($id): JsonResponse
    {
        try {

            $auth_user = auth()->user();

            if ($auth_user->isAdmin()) {
                $menue = Menue::with('foods')->find($id);

                if (!$menue) {
                    return handleResponse([], 'Menue not found', false, 404);
                }

                return handleResponse($menue, 'Menue fetched successfully', true, 200);
            } elseif ($auth_user->isUser()) {
                $menue = Menue::where('status', 'active')->where('id', $id);

                if (!$menue) {
                    return handleResponse([], 'Menue not found', false, 404);
                }

                return handleResponse($menue, 'Menue fetched successfully', true, 200);
            }

            return handleResponse([], 'Unauthorized', false, 401);
        } catch (\Throwable $err) {
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }


    public function updateOneMenue($id): JsonResponse
    {
        try {

            $auth_user = auth()->user();

            if ($auth_user->isAdmin()) {
                $validated = Validator::make(request()->all(), [
                    'name' => ['nullable', 'string'],
                    'description' => ['nullable', 'string'],
                    'status' => ['nullable', Rule::in(['active', 'inactive'])],
                ]);

                if ($validated->fails()) {
                    return handleResponse([], $validated->errors()->first(), false, 400);
                }

                $menue = Menue::find($id);

                if (!$menue) {
                    return handleResponse([], 'Menue not found', false, 404);
                }

                $menue->update([
                    'name' => request('name') ?? $menue->name,
                    'description' => request('description') ?? $menue->description,
                    'status' => request('status') ?? $menue->status,
                ]);

                return handleResponse($menue, 'Menue updated successfully', true, 200);
            }

            return handleResponse([], 'Unauthorized', false, 401);
        } catch (\Throwable $err) {
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }


    public function deleteOneMenue($id): JsonResponse
    {
        try {

            $auth_user = auth()->user();

            if ($auth_user->isAdmin()) {
                $menue = Menue::find($id);

                if (!$menue) {
                    return handleResponse([], 'Menue not found', false, 404);
                }

                $menue->delete();

                return handleResponse([], 'Menue deleted successfully', true, 200);
            }

            return handleResponse([], 'Unauthorized', false, 401);

        } catch (\Throwable $err) {
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }
}
