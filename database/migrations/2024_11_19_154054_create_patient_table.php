<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            'patient', function (Blueprint $table) {
                $table->id('patient_id');
                $table->string('first_name');
                $table->string('last_name');
                $table->date('date_of_birth');
                $table->string('ssn')->unique();
                $table->string('policy_number')->nullable();
                $table->string('address')->nullable();
                $table->boolean('is_employee')->default(false);
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient');
    }
}
