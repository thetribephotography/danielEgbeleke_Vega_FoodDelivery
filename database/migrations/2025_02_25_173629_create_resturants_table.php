<?php

use App\Models\Role;
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

        if(!Schema::hasColumn('users', 'role_id')){
            Schema::table('users', function (Blueprint $table) {
                $table->foreignIdFor(Role::class, 'role_id')->nullable()->constrained();
            });
        }


        Schema::create('resturants', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->string('phone')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resturants');
    }
};
