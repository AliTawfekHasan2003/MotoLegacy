<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->string('ownership_document')->nullable()->after('gallery');
            $table->string('insurance_document')->nullable()->after('ownership_document');
            $table->string('inspection_document')->nullable()->after('insurance_document');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn([
                'ownership_document',
                'insurance_document',
                'inspection_document',
            ]);
        });
    }
};
