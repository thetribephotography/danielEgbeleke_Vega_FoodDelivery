<?php

namespace App\Http\Controllers;

use App\Jobs\GetOveralRatingForFood;
use App\Models\Comment;
use App\Models\Food;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    public function createComment($id): JsonResponse
    {
        try{
            $auth_user = auth()->user();

            $validated = Validator::make(request()->all(), [
                'comment' => ['nullable', 'string'],
                'rating' => ['nullable', 'integer', Rule::in([1, 2, 3, 4, 5])],
            ]);

            if ($validated->fails()) {
                return handleResponse([], $validated->errors()->first(), false, 400);
            }

            $food = Food::find($id);

            if(!$food){
                return handleResponse([], 'Food not found', false, 404);
            }

            $comment = Comment::create([
                'user_id' => $auth_user->id,
                'food_id' => $id,
                'comment' => request('comment') ?? null,
                'rating' => request('rating') ?? null,
            ]);

            GetOveralRatingForFood::dispatch($id);

            return handleResponse($comment, 'Comment created successfully', true, 201);

        }catch(\Throwable $err){
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }


    public function editOneComment($id): JsonResponse
    {
        try{

            $auth_user = auth()->user();

            $validated = Validator::make(request()->all(), [
                'comment' => ['nullable', 'string'],
                'rating' => ['nullable', 'integer', Rule::in([1, 2, 3, 4, 5])],
            ]);

            if ($validated->fails()) {
                return handleResponse([], $validated->errors()->first(), false, 400);
            }


            $comment = Comment::find($id);

            if(!$comment){
                return handleResponse([], 'Comment not found', false, 404);
            }

            if($auth_user->isAdmin()){
                $comment->update([
                    'comment' => request('comment') ?? $comment->comment,
                    'rating' => request('rating') ?? $comment->rating,
                ]);

                GetOveralRatingForFood::dispatch($comment->food_id);

                return handleResponse($comment, 'Comment updated successfully', true, 200);
            }elseif($auth_user->id === $comment->user_id){
                $comment->update([
                    'comment' => request('comment') ?? $comment->comment,
                    'rating' => request('rating') ?? $comment->rating,
                ]);

                GetOveralRatingForFood::dispatch($comment->food_id);

                return handleResponse($comment, 'Comment updated successfully', true, 200);
            }

            return handleResponse([], 'Unauthorized/Not Allowed', false, 401);

        }catch(\Throwable $err){
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }


    public function deleteOneComment($id): JsonResponse
    {
        try{

            $auth_user = auth()->user();

            $comment = Comment::find($id);

            if (!$comment) {
                return handleResponse([], 'Comment not found', false, 404);
            }

            if($auth_user->isAdmin()){

                GetOveralRatingForFood::dispatch($comment->food_id);

                $comment->delete();

                return handleResponse([], 'Comment deleted successfully', true, 200);

            }elseif($auth_user->id === $comment->user_id){

                GetOveralRatingForFood::dispatch($comment->food_id);

                $comment->delete();

                return handleResponse([], 'Comment deleted successfully', true, 200);
            }

            return handleResponse([], 'Unauthorized/Not Allowed', false, 401);

        }catch(\Throwable $err){
            Log::error($err->getMessage());
            return handleResponse([], $err->getMessage(), false, 400);
        }
    }
}
