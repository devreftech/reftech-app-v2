<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Quotation;
use App\Models\PendingPO;
use App\Models\Payment;

$q = Quotation::where('payment_method', 'like', '%Tempo%')->orWhere('payment_terms', 'like', '%Tempo%')->take(5)->get();
foreach ($q as $item) {
    echo "Quote {$item->id}: payment_method={$item->payment_method}, payment_terms={$item->payment_terms}, payment_type={$item->payment_type}\n";
}
