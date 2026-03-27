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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('type'); 
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('year');
            $table->string('color');
            $table->string('fuel_type');
            $table->string('transmission');
            $table->integer('doors');
            $table->integer('seats');
            $table->integer('previous_owners_count')->default(0);
            $table->string('brand');
            $table->string('registration_country')->nullable();
            $table->integer('engine_year')->nullable();
            $table->integer('cylinders_count')->nullable();
            $table->string('drive_system')->nullable();
            $table->string('plate_number')->nullable();
            $table->string('fuel_consumption')->nullable();
            $table->boolean('warranty')->default(false);
            $table->integer('warranty_duration')->nullable(); 
            $table->string('status')->default('available');
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->decimal('rental_price_per_day', 15, 2)->nullable();
            $table->boolean('air_conditioning')->default(false);
            $table->boolean('airbags')->default(false);
            $table->boolean('rear_camera')->default(false);
            $table->boolean('bluetooth')->default(false);
            $table->boolean('sunroof')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
