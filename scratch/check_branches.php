<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== EMPLOYEES & THEIR BRANCHES ===\n";
$employees = DB::select("SELECT id, first_name, last_name, role, branch_id FROM USERS WHERE UPPER(role) = 'EMPLOYEE'");
foreach ($employees as $e) {
    echo "ID: {$e->id} | {$e->first_name} {$e->last_name} | branch_id=" . ($e->branch_id ?? 'NULL') . "\n";
}

echo "\n=== BRANCHES ===\n";
$branches = DB::select("SELECT branch_id, branch_name, location FROM branches WHERE UPPER(status)='ACTIVE'");
foreach ($branches as $b) {
    echo "branch_id: {$b->branch_id} | {$b->branch_name} | {$b->location}\n";
}

echo "\n=== ACCOUNTS & BRANCHES ===\n";
$accounts = DB::select("SELECT a.id, a.account_number, a.branch_id, a.branch, a.status FROM accounts a ORDER BY a.branch_id NULLS FIRST");
foreach ($accounts as $a) {
    echo "ACC: {$a->account_number} | branch_id=" . ($a->branch_id ?? 'NULL') . " | branch={$a->branch} | status={$a->status}\n";
}
