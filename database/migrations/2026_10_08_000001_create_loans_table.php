<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_code', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->text('purpose');
            $table->date('loan_date');
            $table->date('due_date');
            // menunggu, disetujui, ditolak, dipinjam, dikembalikan, dibatalkan
            $table->string('status', 20)->default('menunggu');
            $table->text('rejection_reason')->nullable();

            // Persetujuan atau penolakan oleh petugas
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();

            // Serah terima barang
            $table->foreignId('handed_over_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('handed_over_at')->nullable();

            // Pengembalian barang
            $table->foreignId('returned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('returned_at')->nullable();
            $table->text('return_notes')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index(['loan_date', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
