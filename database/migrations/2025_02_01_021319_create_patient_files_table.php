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
        Schema::create('patient_files', function (Blueprint $table) {
            $table->id('file_id');
            $table->unsignedBigInteger('patient_id');
            $table->foreign('patient_id')
                  ->references('patient_id')
                  ->on('patient')
                  ->onDelete('cascade');
            $table->string('filename');
            $table->string('path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_files');
    }
};
