<?php

use App\Models\Resturant;
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
        
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Resturant::class, 'resturant_id')->nullable()->constrained();
            $table->foreignIdFor(User::class, 'user_id')->nullable()->constrained();
            $table->string('order_number')->nullable()->index();
            $table->string('order_status')->nullable(); // pending, completed, cancelled
            $table->timestamp('expires_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
