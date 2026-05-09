<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumption_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('type');            // electricity, telephone, internet, machine_maintenance, rent, other
            $table->string('description');
            $table->decimal('amount', 14, 2);
            $table->date('receipt_date');
            $table->date('period_month');      // first day of the month (for cost allocation)
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('period_month');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumption_receipts');
    }
};
