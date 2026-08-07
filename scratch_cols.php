<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

foreach (['product', 'serial_product'] as $table) {
    echo "--- Columns for table: $table ---" . PHP_EOL;
    $cols = DB::select("DESCRIBE `$table`");
    foreach ($cols as $col) {
        echo "Field: {$col->Field}, Type: {$col->Type}" . PHP_EOL;
    }
}
