<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('status');
        });

        Schema::table('rental_requests', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('status');
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('approval_status');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });

        Schema::table('rental_requests', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });
    }
};
