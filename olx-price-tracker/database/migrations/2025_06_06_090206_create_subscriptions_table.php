<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('email');
            $table->decimal('current_price', 10, 2);
            $table->string('current_currency', 10);
            $table->boolean('is_active')->default(true);
            $table->string('token')->unique();
            $table->dateTime('date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();

            $table->index('url');
        });

        Schema::create('price_histories', function (Blueprint $table) {
            $table->id();
            $table->decimal('price', 10, 2);
            $table->string('currency', 10);
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->dateTime('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_histories');
        Schema::dropIfExists('subscriptions');
    }
};
