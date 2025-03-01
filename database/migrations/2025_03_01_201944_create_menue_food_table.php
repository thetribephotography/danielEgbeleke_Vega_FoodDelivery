<?php

use App\Models\Food;
use App\Models\Menue;
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
        Schema::create('menue_food', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Menue::class, 'menue_id')->nullable()->constrained();
            $table->foreignIdFor(Food::class, 'food_id')->nullable()->constrained();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menue_food');
    }
};
