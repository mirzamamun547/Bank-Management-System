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
        Schema::table('USERS', function (Blueprint $table) {
            $table->string('FATHER_NAME')->nullable();
            $table->string('MOTHER_NAME')->nullable();
            $table->string('GENDER')->nullable();
            $table->string('PROFILE_PHOTO')->nullable();
            $table->string('SIGNATURE')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('USERS', function (Blueprint $table) {
            $table->dropColumn(['FATHER_NAME', 'MOTHER_NAME', 'GENDER', 'PROFILE_PHOTO', 'SIGNATURE']);
        });
    }
};
