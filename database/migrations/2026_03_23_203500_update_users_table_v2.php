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
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('email_verified_at');
            }
            
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('set null');
            }
            
            $legacy = ['country_id', 'phone_country_id', 'summary', 'image'];
            foreach ($legacy as $col) {
                if (Schema::hasColumn('users', $col)) {
                    if ($col == 'country_id' || $col == 'phone_country_id') {
                         try { $table->dropForeign([$col]); } catch (\Exception $e) {}
                    }
                    $table->dropColumn($col);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('is_active', 'status');
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->foreignId('country_id')->nullable()->constrained('countries');
            $table->foreignId('phone_country_id')->nullable()->constrained('countries');
            $table->string('image')->nullable();
            $table->text('summary')->nullable();
        });
    }
};
