<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worker_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained('workers')->cascadeOnDelete();
            $table->date('period');                          // first day of the month
            $table->unsignedSmallInteger('working_days')->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('bonuses', 12, 2)->default(0);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->decimal('gross_salary', 12, 2)->default(0);
            $table->decimal('social_insurance', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['worker_id', 'period']);
            $table->index('period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_salaries');
    }
};
