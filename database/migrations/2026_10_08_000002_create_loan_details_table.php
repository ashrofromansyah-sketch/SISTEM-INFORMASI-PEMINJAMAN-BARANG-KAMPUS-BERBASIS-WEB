<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->string('return_condition', 20)->nullable();
            $table->string('return_note', 255)->nullable();
            $table->timestamps();

            $table->unique(['loan_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_details');
    }
};
