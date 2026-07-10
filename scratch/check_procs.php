<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$procedures = DB::select("
    SELECT object_name, object_type 
    FROM user_objects 
    WHERE object_type IN ('PROCEDURE', 'FUNCTION')
");

print_r($procedures);
