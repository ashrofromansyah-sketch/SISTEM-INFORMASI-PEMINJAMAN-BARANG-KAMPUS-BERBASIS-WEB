<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('identity_number', 30)->nullable()->unique()->after('password');
            $table->string('phone', 20)->nullable()->after('identity_number');
            $table->enum('role', ['admin', 'petugas', 'peminjam'])->default('peminjam')->after('phone');
            $table->boolean('is_active')->default(true)->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['identity_number']);
            $table->dropColumn(['identity_number', 'phone', 'role', 'is_active']);
        });
    }
};
