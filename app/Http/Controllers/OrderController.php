<?php

namespace App\Http\Controllers;

use App\Models\Menue;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Resturant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function bookOrder(): JsonResponse
    {
        try{

            $auth_user = auth()->user();

            $validated = Validator::make(request()->all(), [
                'order_details' => ['required', 'array'],
                'order_details.*.menue_id' => ['required', 'integer', Rule::exists('menues', 'id')],
                'order_details.*.food_id' => ['required', 'integer', Rule::exists('food', 'id')],
                'order_details.*.quantity' => ['required', 'integer', 'min:1']
            ]);

            if($validated->fails()){
                return handleResponse([], $validated->errors(), true, 400);
            }

            //check if any resturant is avaialble
            $resturant_check = Resturant::where('is_booked', false)->get();

            $closest_restaurant = null;
            $shortest_distance = PHP_FLOAT_MAX;

            foreach ($resturant_check as $restaurant) {
                $distance = haversineDistance($auth_user->latitude, $auth_user->longitude, $restaurant->latitude, $restaurant->longitude);

                if ($distance < $shortest_distance) {
                    $shortest_distance = $distance;
                    $closest_restaurant = $restaurant;
                }
            }

            if(!$closest_restaurant){
                return handleResponse([], 'No Resturant Available To Take Order...Kindly Try Again Later', true, 400);
            }

            $order_number = generateOrderNumber();
            $order_details = request('order_details');

            DB::beginTransaction();

            $order = Order::create([
                'user_id' => $auth_user->id,
                'resturant_id' => $closest_restaurant->id,
                'order_number' => $order_number,
                'order_status' => 'pending',
                'expires_at' => now()->addMinutes(15)
            ]);

            // Save Order Details
            foreach ($order_details as $detail) {

                $menue = Menue::where('id', $detail['menue_id'])->where('status', 'active')->whereHas('foods', function($query) use ($detail) {
                    $query->where('food.id', $detail['food_id']);
                })->first();

                if(!$menue){
                    return handleResponse(["menue_id" => $detail['menue_id'], "food_id" => $detail['food_id']], 'Invalid Order Details', false, 400);
                }

                OrderDetail::create([
                    'user_id' => $auth_user->id,
                    'order_id' => $order->id,
                    'menue_id' => $detail['menue_id'],
                    'food_id' => $detail['food_id'],
                    'quantity' => $detail['quantity']
                ]);
            }

            // Update Resturant Status
            $closest_restaurant->update([
                'is_booked' => true
            ]);

            DB::commit();

            return handleResponse($order, 'Order Booked Successfully', false, 200);

        }catch(\Throwable $err){

            DB::rollBack();
            return handleResponse([], $err->getMessage(), true, 400);
        }
    }


    public function getOrders(): JsonResponse
    {
        try{
            $auth_user = auth()->user();

            $pagination = request()->query('pagination') ?? 10;

            $order_status = request()->query('order_status') ?? 'pending'; // pending, completed, cancelled

            $query = Order::with('orderDetails', 'user', 'resturant')->where('order_status', $order_status);

            if($auth_user->isUser()){

                $query->where('user_id', $auth_user->id);
            }

            $orders = $query->paginate($pagination);

            return handleResponse($orders, 'Orders Fetched Successfully', false, 200);
        }catch(\Throwable $err){
            return handleResponse([], $err->getMessage(), true, 400);
        }
    }


    public function getOneOrder($id): JsonResponse
    {
        try{

            $order = Order::with('orderDetails', 'user', 'resturant')->find($id);

            return handleResponse($order, 'Order Fetched Successfully', false, 200);

        }catch(\Throwable $err){
            return handleResponse([], $err->getMessage(), true, 400);
        }
    }



    public function updateOrderStatus(): JsonResponse
    {
        try{

            $auth_user = auth()->user();

            if($auth_user->isAdmin()){
                $validated = Validator::make(request()->all(), [
                    'order_id' => ['required', 'numeric', Rule::exists('orders', 'id')],
                    'order_status' => ['required', 'string', Rule::in(['completed','cancelled'])]
                ]);

                if($validated->fails()){
                    return handleResponse([], $validated->errors(), true, 400);
                }

                $order = Order::find(request('order_id'));

                $order->update([
                    'order_status' => request('order_status')
                ]);

                $resturant = Resturant::find($order->resturant_id);

                $resturant->update([
                    'is_booked' => false
                ]);

                return handleResponse($order, 'Order Status Updated Successfully', false, 200);

            }elseif($auth_user->isUser()){
                $validated = Validator::make(request()->all(), [
                    'order_id' => ['required', 'integer', Rule::exists('orders', 'id')],
                    'order_status' => ['required', 'string', Rule::in(['cancelled'])]
                ]);

                if ($validated->fails()) {
                    return handleResponse([], $validated->errors(), true, 400);
                }

                $order = Order::find(request('order_id'));

                $order->update([
                    'order_status' => request('order_status')
                ]);

                $resturant = Resturant::find($order->resturant_id);

                $resturant->update([
                    'is_booked' => false
                ]);

                return handleResponse($order, 'Order Status Updated Successfully', false, 200);
            }

            return handleResponse([], 'Unauthorized', true, 401);

        }catch(\Throwable $err){
            return handleResponse([], $err->getMessage(), true, 400);
        }
    }
}
