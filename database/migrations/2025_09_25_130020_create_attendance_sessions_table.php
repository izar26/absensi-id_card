<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_xxxxxx_create_attendance_sessions_table.php

public function up(): void
{
    Schema::create('attendance_sessions', function (Blueprint $table) {
        $table->id();
        $table->date('session_date')->unique();
        $table->time('start_time');
        $table->time('end_time');
        $table->string('description')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_sessions');
    }
};
