<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->jsonb('items');
            $table->string('type', 255)->default('cart');
            $table->foreignIdFor(\App\Models\User::class);
            $table->foreignIdFor(\App\Models\Status::class)->default(2);
            $table->decimal('total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class);
            $table->dropForeignIdFor(\App\Models\Status::class);
        });
        Schema::dropIfExists('carts');
    }
};
