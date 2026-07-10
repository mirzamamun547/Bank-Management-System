<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Employee
        DB::table('USERS')->insertOrIgnore([
            'customer_id' => 'EMP100001',
            'first_name' => 'Farhan',
            'last_name' => 'Ahmed',
            'full_name' => 'Farhan Ahmed',
            'email' => 'farhan@nexus.com',
            'phone' => '01711122233',
            'address' => 'Dhaka',
            'nid' => '1234567890123',
            'password' => Hash::make('password'),
            'role' => 'EMPLOYEE',
            'status' => 'ACTIVE',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 2. Create Default Branches
        DB::statement("
            INSERT INTO branches (branch_id, branch_name, location, manager_employee_id, status, created_at, updated_at)
            VALUES (SEQ_BRANCH.NEXTVAL, 'Central Dhaka Branch', 'Dhaka', 'EMP100001', 'ACTIVE', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ");

        DB::statement("
            INSERT INTO branches (branch_id, branch_name, location, manager_employee_id, status, created_at, updated_at)
            VALUES (SEQ_BRANCH.NEXTVAL, 'Chittagong Coastal Branch', 'Chittagong', 'EMP100001', 'ACTIVE', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ");

        // Get branch IDs
        $branches = DB::table('branches')->get();
        $branchId1 = $branches[0]->branch_id;
        $branchId2 = $branches[1]->branch_id;

        // Assign branches to Employee
        DB::table('USERS')->where('customer_id', 'EMP100001')->update(['branch_id' => $branchId1]);

        // 3. Create Default Customers
        DB::table('USERS')->insertOrIgnore([
            [
                'customer_id' => 'CUS100001',
                'first_name' => 'Rahim',
                'last_name' => 'Ali',
                'full_name' => 'Rahim Ali',
                'email' => 'rahim@nexus.com',
                'phone' => '01811223344',
                'address' => 'Dhaka',
                'nid' => '5432167890123',
                'password' => Hash::make('password'),
                'role' => 'CUSTOMER',
                'status' => 'ACTIVE',
                'branch_id' => $branchId1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'customer_id' => 'CUS100002',
                'first_name' => 'Sumaiya',
                'last_name' => 'Khan',
                'full_name' => 'Sumaiya Khan',
                'email' => 'sumaiya@nexus.com',
                'phone' => '01911223344',
                'address' => 'Chittagong',
                'nid' => '9876543210123',
                'password' => Hash::make('password'),
                'role' => 'CUSTOMER',
                'status' => 'ACTIVE',
                'branch_id' => $branchId2,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // 4. Create customer accounts
        $user1 = DB::table('USERS')->where('customer_id', 'CUS100001')->first();
        $user2 = DB::table('USERS')->where('customer_id', 'CUS100002')->first();

        DB::table('accounts')->insertOrIgnore([
            [
                'user_id' => $user1->id,
                'account_number' => 'ACC-100001',
                'account_type' => 'Savings',
                'balance' => 25000.00,
                'status' => 'Active',
                'branch' => 'Central Dhaka Branch',
                'branch_id' => $branchId1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_id' => $user2->id,
                'account_number' => 'ACC-100002',
                'account_type' => 'Current',
                'balance' => 15000.00,
                'status' => 'Active',
                'branch' => 'Chittagong Coastal Branch',
                'branch_id' => $branchId2,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // 5. Create Loans
        DB::table('loans')->insertOrIgnore([
            [
                'user_id' => $user1->id,
                'loan_type' => 'Home Loan',
                'amount' => 50000.00,
                'duration_months' => 24,
                'purpose' => 'Buying Apartment',
                'status' => 'Pending',
                'monthly_installment' => 2200.00,
                'remaining_amount' => 50000.00,
                'next_due_date' => now()->addMonth(),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_id' => $user2->id,
                'loan_type' => 'Personal Loan',
                'amount' => 10000.00,
                'duration_months' => 12,
                'purpose' => 'Education fees',
                'status' => 'Pending',
                'monthly_installment' => 900.00,
                'remaining_amount' => 10000.00,
                'next_due_date' => now()->addMonth(),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // 6. Create transactions
        $acc1 = DB::table('accounts')->where('account_number', 'ACC-100001')->first();
        $acc2 = DB::table('accounts')->where('account_number', 'ACC-100002')->first();

        DB::table('transactions')->insertOrIgnore([
            [
                'account_id' => $acc1->id,
                'transaction_type' => 'DEPOSIT',
                'amount' => 5000.00,
                'reference' => 'Initial',
                'description' => 'Atm cash deposit',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5)
            ],
            [
                'account_id' => $acc2->id,
                'transaction_type' => 'DEPOSIT',
                'amount' => 2500.00,
                'reference' => 'Salary',
                'description' => 'Monthly Salary',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2)
            ]
        ]);
    }
}
