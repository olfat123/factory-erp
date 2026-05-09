<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->text('description')->nullable();
            $table->integer('production_time')->default(0)->comment('in minutes');
            $table->decimal('current_stock', 15, 4)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bill_of_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained('products')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('bill_of_material_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_of_material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained('materials')->restrictOnDelete();
            $table->decimal('quantity', 15, 4);
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['bill_of_material_id', 'material_id']);
        });

        Schema::create('product_machines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_of_material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('machine_id')->constrained('machines')->restrictOnDelete();
            $table->integer('duration_minutes')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['bill_of_material_id', 'machine_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_machines');
        Schema::dropIfExists('bill_of_material_items');
        Schema::dropIfExists('bill_of_materials');
        Schema::dropIfExists('products');
    }
};
