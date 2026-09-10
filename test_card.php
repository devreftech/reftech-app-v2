<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

view()->share('errors', new Illuminate\Support\ViewErrorBag);
$admin = App\Models\User::first();
Illuminate\Support\Facades\Auth::login($admin);
$c = $app->make(App\Http\Controllers\ProjectMonitoringController::class);
$r = $c->show(1900);
$html = $r->render();

echo (strpos($html, 'Ongkir / Resi') === false ? 'OK: Ongkir/Resi removed!' : 'FAIL: Ongkir/Resi still present!') . PHP_EOL;
echo (strpos($html, 'Profit Bersih (Net)') !== false ? 'OK: Clean modern card rendered!' : 'FAIL: Net profit card not found!') . PHP_EOL;
