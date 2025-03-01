<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\MenueController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ResturantController;
use App\Http\Controllers\RoleController;
use App\Http\Middleware\AuthenticatedTokenCheck;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/',  function () {
    return handleResponse([], 'Token Expired/Invalid', true, 400);
})->name('login');


Route::post('/login', [RoleController::class, 'genUserToken']);
Route::post('/register', [RoleController::class, 'userRegister']);
Route::get('/roles', [RoleController::class, 'getRoles']);
Route::post('/forgot/password', [RoleController::class, 'verifyEmail']);
Route::post('/update/password', [RoleController::class, 'verifyAndUpdatePassword']);

//
Route::get('/test', [RoleController::class, 'testNotification']);

Route::group(['prefix' => '', 'middleware' => [AuthenticatedTokenCheck::class, 'auth:sanctum']], static function () {

    Route::post('/logout', [RoleController::class, 'logout']);

    Route::group(['prefix' => 'resturant'], static function () {
        Route::post('/', [ResturantController::class, 'createResturant']);
        Route::get('/', [ResturantController::class, 'getResturants']);
        Route::get('/{id}', [ResturantController::class, 'getOneResturant']);
        Route::put('/{id}', [ResturantController::class, 'updateResturant']);
        Route::delete('/{id}', [ResturantController::class, 'deleteOneResturant']);
    });

    Route::group(['prefix' => 'food'], static function (){
        Route::post('/', [FoodController::class, 'createFood']);
        Route::get('/', [FoodController::class, 'getFoods']);
        Route::get('/{id}', [FoodController::class, 'getOneFood']);
        Route::put('/{id}', [FoodController::class, 'editOneFood']);
        Route::delete('/{id}', [FoodController::class, 'deleteOneFood']);
        Route::get('/{id}/comments', [FoodController::class, 'getAllCommentsPerFood']);
    });

    Route::group(['prefix' => 'menue'], static function () {
        Route::post('/', [MenueController::class, 'createMenue']);
        Route::get('/', [MenueController::class, 'getMenues']);
        Route::get('/{id}', [MenueController::class, 'findOneMenue']);
        Route::put('/{id}', [MenueController::class, 'updateOneMenue']);
        Route::delete('/{id}', [MenueController::class, 'deleteOneMenue']);
        Route::post('/add/food', [FoodController::class, 'addFoodToMenue']);
        Route::post('/remove/food', [FoodController::class, 'removeFoodFromMenue']);

    });


    Route::group(['prefix' => 'comment'], static function () {
        Route::post('/{id}', [CommentController::class, 'createComment']);
        Route::put('/{id}', [CommentController::class, 'editOneComment']);
        Route::delete('/{id}', [CommentController::class, 'deleteOneComment']);
    });

    //TODO: REMAIN FUCTIONALITY TO ORDER AND DELIVERY(TIMINGS)

    Route::group(['prefix' => 'order'], static function () {
        Route::post('/', [OrderController::class, 'bookOrder']);
        Route::get('/', [OrderController::class, 'getOrders']);
        Route::get('/{id}', [OrderController::class, 'getOneOrder']);
        Route::put('/', [OrderController::class, 'updateOrderStatus']);
    });

});
