<?php

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
            $table->foreignIdFor(\App\Models\User::class);
            $table->foreignIdFor(\App\Models\Status::class)->default(3);
            $table->foreignIdFor(\App\Models\Payment::class)->default(1);
            $table->foreignIdFor(\App\Models\Delivery::class)->default(1);
            $table->jsonb('items');
            $table->decimal('total');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class);
            $table->dropForeignIdFor(\App\Models\Status::class);
            $table->dropForeignIdFor(\App\Models\Payment::class);
            $table->dropForeignIdFor(\App\Models\Delivery::class);
        });
        Schema::dropIfExists('orders');
    }
};
