<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('cars', 'is_sold_or_rented')) {
            Schema::table('cars', function (Blueprint $table) {
                $table->dropColumn('is_sold_or_rented');
            });
        }

        Schema::table('cars', function (Blueprint $table) {
            if (!Schema::hasColumn('cars', 'is_sold')) {
                $table->boolean('is_sold')->default(false)->after('status');
            }

            if (!Schema::hasColumn('cars', 'is_rented')) {
                $table->boolean('is_rented')->default(false)->after('is_sold');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('cars', 'is_sold')) {
                $columns[] = 'is_sold';
            }

            if (Schema::hasColumn('cars', 'is_rented')) {
                $columns[] = 'is_rented';
            }

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
