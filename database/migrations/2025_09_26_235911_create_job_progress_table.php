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
        Schema::create('job_progress', function (Blueprint $table) {
    $table->id();
    $table->string('job_type');
    $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
    $table->integer('percentage_complete')->default(0);
    $table->integer('completed_batches')->default(0);
    $table->integer('total_batches')->default(0);
    $table->string('file_path')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_progress');
    }
};
