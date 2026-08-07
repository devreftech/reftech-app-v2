<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

foreach (['product', 'serial_product', 'detail_product', 'detail_product_in'] as $table) {
    echo "--- Indexes for table: $table ---" . PHP_EOL;
    $indexes = DB::select("SHOW INDEX FROM `$table`");
    foreach ($indexes as $idx) {
        echo "Key_name: {$idx->Key_name}, Column_name: {$idx->Column_name}, Non_unique: {$idx->Non_unique}" . PHP_EOL;
    }
}
