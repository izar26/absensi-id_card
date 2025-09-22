<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_xxxxxx_create_students_table.php

public function up(): void
{
    Schema::create('students', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('nis')->unique(); // unique() agar tidak ada NIS yang sama
        $table->string('class');
        $table->string('photo')->nullable(); // nullable() berarti boleh kosong
        $table->string('gender')->nullable();
        $table->date('date_of_birth')->nullable();
        $table->string('status')->default('Aktif');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
