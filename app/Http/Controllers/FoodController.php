<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Food;
use App\Models\Menue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FoodController extends Controller
{
    public function createFood(): JsonResponse
    {
        try {

            $auth_user = auth()->user();

            if ($auth_user->isAdmin()) {
                $validated = Validator::make(request()->all(), [
                    'name' => ['required', 'string'],
                    'price' => ['required', 'numeric'],
                    'description' => ['required', 'string'],
                ]);

                if ($validated->fails()) {
                    return handleResponse([], $validated->errors()->first(), false, 400);
                }

                $food = Food::create([
                    'name' => request('name'),
                    'price' => request('price'),
                    'description' => request('description'),
                    'overal_rating' => null,
                    'status' => false
                ]);

                return handleResponse($food, 'Food created successfully', true, 201);
            }

            return handleResponse([], 'Unauthorized', false, 401);

        } catch (\Throwable $err) {
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }



    public function getFoods(): JsonResponse
    {
        try {
            $auth_user = auth()->user();

            $ratings = request()->query('ratings', 5); // Default: 5
            $comments = request()->query('comments', 1); // Default: 1

            $query = Food::with([
                'comments' => function ($query) use ($comments) {
                    $query->take($comments);
                }
            ])->where(function ($q) use ($ratings) {
                $q->where('overal_rating', '<=', $ratings)
                    ->orWhereNull('overal_rating');
            })
                // ->where('overal_rating', '<=', $ratings)
                ->orderByDesc('overal_rating');

            if ($auth_user->isUser()) {
                $query->where('status', true); // Only fetch active foods for users
            }

            $foods = $query->get(); // Execute the query

            return handleResponse($foods, 'Foods fetched successfully', true, 200);
        } catch (\Throwable $err) {
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }




    public function getOneFood($id): JsonResponse
    {
        try {

            $auth_user = auth()->user();

            if ($auth_user->isAdmin()) {
                $food = Food::with('comments')->find($id);

                if (!$food) {
                    return handleResponse([], 'Food not found', false, 404);
                }

                return handleResponse($food, 'Food fetched successfully', true, 200);
            } elseif ($auth_user->isUser()) {
                $food = Food::with('comments')->where('status', true)->where('id', $id);

                if (!$food) {
                    return handleResponse([], 'Food not found', false, 404);
                }

                return handleResponse($food, 'Food fetched successfully', true, 200);
            }

            return handleResponse([], 'Unauthorized', false, 401);

        } catch (\Throwable $err) {
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }

    public function editOneFood($id): JsonResponse
    {
        try {
            $auth_user = auth()->user();

            if ($auth_user->isAdmin()) {
                $food = Food::find($id);

                if (!$food) {
                    return handleResponse([], 'Food not found', false, 404);
                }

                $validated = Validator::make(request()->all(), [
                    'name' => ['nullable', 'string'],
                    'price' => ['nullable', 'numeric'],
                    'description' => ['nullable', 'string'],
                    'overal_rating' => ['nullable', 'numeric'],
                    'status' => ['nullable', 'boolean']
                ]);

                if ($validated->fails()) {
                    return handleResponse([], $validated->errors()->first(), false, 400);
                }

                $food->update([
                    'name' => request('name') ?? $food->name,
                    'price' => request('price') ?? $food->price,
                    'description' => request('description') ?? $food->description,
                    'overal_rating' => request('overal_rating') ?? $food->overal_rating,
                    'status' => request('status') ?? $food->status,
                ]);

                return handleResponse($food, 'Food updated successfully', true, 200);
            }

            return handleResponse([], 'Unauthorized', false, 401);

        } catch (\Throwable $err) {
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }


    public function deleteOneFood($id): JsonResponse
    {
        try {
            $auth_user = auth()->user();

            if ($auth_user->isAdmin()) {
                $food = Food::find($id);

                if (!$food) {
                    return handleResponse([], 'Food not found', false, 404);
                }

                $food->delete();

                return handleResponse([], 'Food deleted successfully', true, 200);
            }

            return handleResponse([], 'Unauthorized', false, 401);

        } catch (\Throwable $err) {
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }

    public function addFoodToMenue(): JsonResponse
    {
        try {
            $auth_user = auth()->user();

            if ($auth_user->isAdmin()) {
                $validated = Validator::make(request()->all(), [
                    'food_id' => ['required', 'exists:food,id'],
                    'menue_id' => ['required', 'exists:menues,id'],
                ]);

                if ($validated->fails()) {
                    return handleResponse([], $validated->errors()->first(), false, 400);
                }

                $menue = Menue::find(request('menue_id'));
                $food = Food::find(request('food_id'));

                if (!$menue || !$food) {
                    return handleResponse([], 'Menue or food not found', false, 404);
                }

                $food->update([
                    'status' => true
                ]);

                $food->refresh();

                $menue->foods()->syncWithoutDetaching($food);

                return handleResponse([], 'Food added to menue successfully', true, 200);
            }

            return handleResponse([], 'Unauthorized', false, 401);

        } catch (\Throwable $err) {
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }


    public function removeFoodFromMenue(): JsonResponse
    {
        try {
            $auth_user = auth()->user();

            if ($auth_user->isAdmin()) {
                $validated = Validator::make(request()->all(), [
                    'food_id' => ['required', 'exists:food,id'],
                    'menue_id' => ['required', 'exists:menues,id'],
                ]);

                if ($validated->fails()) {
                    return handleResponse([], $validated->errors()->first(), false, 400);
                }

                $menue = Menue::find(request('menue_id'));
                $food = Food::find(request('food_id'));

                if (!$menue || !$food) {
                    return handleResponse([], 'Menue or food not found', false, 404);
                }

                $menue->foods()->detach($food);

                return handleResponse([], 'Food removed from menue successfully', true, 200);
            }

            return handleResponse([], 'Unauthorized', false, 401);
        } catch (\Throwable $err) {
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }


    public function getAllCommentsPerFood($id): JsonResponse
    {
        try {
            $comments = Comment::where('food_id', $id)->get();

            return handleResponse($comments, 'Comments fetched successfully', true, 200);

        } catch (\Throwable $err) {
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }
}
