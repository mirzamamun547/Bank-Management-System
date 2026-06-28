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
        
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
            $table->string('transaction_type');
            $table->decimal('amount', 15, 2);
            $table->string('reference')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        
        Schema::create('otp_verification', function (Blueprint $table) {
            $table->id();
            $table->string('account_number');
            $table->string('receiver_account')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('otp', 6);
            $table->string('type')->default('DEPOSIT'); 
            $table->dateTime('expires_at');
            $table->timestamps();
        });

        
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id();
            $table->string('table_name');
            $table->string('action'); 
            $table->string('performed_by')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_log');
        Schema::dropIfExists('otp_verification');
        Schema::dropIfExists('transactions');
    }
};
