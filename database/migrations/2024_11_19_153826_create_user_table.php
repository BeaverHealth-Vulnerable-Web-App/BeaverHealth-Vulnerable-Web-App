<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            'user', function (Blueprint $table) {
                $table->id('user_id');
                $table->string('username')->unique();
                $table->string('password');
                $table->boolean('is_admin')->default(false);
                $table->boolean('request_records')->default(false);
                $table->boolean('load_records')->default(false);
                $table->boolean('view_employee_info')->default(false);
                $table->boolean('sqli_on')->default(false);
                $table->boolean('file_upload_on')->default(false);
                $table->boolean('cmd_inject_on')->default(false);
                $table->boolean('xss_reflected_on')->default(false);
                $table->boolean('xss_stored_on')->default(false);
                $table->boolean('idor_on')->default(false);
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
}
