<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Resturant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ClearOrders implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $orders = Order::where('order_status', 'pending')->where('expires_at', '<', now())->get();

        foreach ($orders as $order) {
            $order->update([
                'order_status' => 'completed'
            ]);

            $resturant = Resturant::find($order->resturant_id);

            $resturant->update([
                'is_booked' => false
            ]);
        }

        Log::info("Orders Cleared");
    }
}
