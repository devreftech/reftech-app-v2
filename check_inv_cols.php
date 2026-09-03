<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "Invoice columns: " . implode(', ', Schema::getColumnListing('invoice')) . "\n";
echo "PendingPO columns: " . implode(', ', Schema::getColumnListing('pending_po')) . "\n";
