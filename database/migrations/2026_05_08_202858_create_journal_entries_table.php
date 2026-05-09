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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 50)->unique();
            $table->enum('type', ['goods_received', 'production_consume', 'production_output', 'adjustment_increase', 'adjustment_decrease']);
            $table->nullableMorphs('reference');
            $table->text('description')->nullable();
            $table->decimal('total_amount', 18, 4)->default(0);
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
