<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_balances', function (Blueprint $table) {
            $table->id();
            $table->string('item_type');
            $table->unsignedBigInteger('item_id');
            $table->decimal('quantity', 15, 4)->default(0);
            $table->decimal('average_cost', 15, 4)->default(0);
            $table->decimal('total_value', 15, 4)->default(0);
            $table->timestamps();

            $table->unique(['item_type', 'item_id']);
            $table->index(['item_type', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_balances');
    }
};
