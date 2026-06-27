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
        // Transactions table to log all deposits, withdrawals, transfers
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
            $table->string('transaction_type'); // DEPOSIT, WITHDRAW, TRANSFER
            $table->decimal('amount', 15, 2);
            $table->string('reference')->nullable(); // e.g. destination account for transfers
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // OTP verification table
        Schema::create('otp_verification', function (Blueprint $table) {
            $table->id();
            $table->string('account_number');
            $table->string('receiver_account')->nullable(); // for transfers
            $table->decimal('amount', 15, 2);
            $table->string('otp', 6);
            $table->string('type')->default('DEPOSIT'); // DEPOSIT, WITHDRAW, TRANSFER
            $table->dateTime('expires_at');
            $table->timestamps();
        });

        // Audit log table
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id();
            $table->string('table_name');
            $table->string('action'); // INSERT, UPDATE, DELETE
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
