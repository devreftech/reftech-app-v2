<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$columns = Schema::getColumnListing('unit_quotation');
print_r($columns);

$uq141 = App\Models\UnitQuotation::find(141);
if ($uq141) {
    echo "UnitQuotation 141:\n";
    echo "  No Quote: {$uq141->no_quote}\n";
    echo "  Subtotal: {$uq141->subtotal}\n";
    echo "  Diskon: {$uq141->diskon}\n";
    echo "  Total: {$uq141->total}\n";
    echo "  Fee exists in attributes? " . (isset($uq141->fee) ? $uq141->fee : 'Not found/Null') . "\n";
}
