<?php

use App\Models\Food;
use App\Models\Menue;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class, 'order_id')->nullable()->constrained();
            $table->foreignIdFor(Food::class, 'food_id')->nullable()->constrained();
            $table->foreignIdFor(User::class, 'user_id')->nullable()->constrained();
            $table->foreignIdFor(Menue::class, 'menue_id')->nullable()->constrained();
            $table->integer('quantity')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
