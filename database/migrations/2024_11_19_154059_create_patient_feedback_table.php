<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            'patient_feedback', function (Blueprint $table) {
                $table->id('patient_feedback_id');
                $table->unsignedBigInteger('patient_id');
                $table->foreign('patient_id')
                    ->references('patient_id')
                    ->on('patient')
                    ->onDelete('cascade');
                $table->text('feedback');
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_feedback');
    }
}
