<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$idClientOld = 13028;
$idClientKeep = 893;

$picSukmawatiOld = 8318;
$picSukmawatiKeep = 4270;
$picWawan = 10157;

echo "=== 1. AUDIT SEMUA TABEL DAN KOLOM UNTUK CLIENT 13028 ===\n";

$tables = DB::select('SHOW TABLES');
$dbName = DB::getDatabaseName();
$tableKey = 'Tables_in_' . $dbName;

foreach ($tables as $t) {
    $tableName = $t->$tableKey;
    $columns = Schema::getColumnListing($tableName);

    foreach ($columns as $col) {
        try {
            $count = DB::table($tableName)->where($col, $idClientOld)->count();
            if ($count > 0) {
                echo "Found in Table: [{$tableName}], Column: [{$col}], Count: {$count}\n";
            }
        } catch (\Exception $e) {
            // skip non-comparable columns if any
        }
    }
}

echo "\n=== 2. AUDIT SEMUA TABEL UNTUK PIC 8318 (Ibu Sukmawati di 13028) ===\n";
foreach ($tables as $t) {
    $tableName = $t->$tableKey;
    $columns = Schema::getColumnListing($tableName);

    foreach ($columns as $col) {
        if (in_array(strtolower($col), ['id_pic', 'pic_id', 'id_contact', 'contact_id'])) {
            try {
                $count = DB::table($tableName)->where($col, $picSukmawatiOld)->count();
                if ($count > 0) {
                    echo "Found PIC 8318 in Table: [{$tableName}], Column: [{$col}], Count: {$count}\n";
                }
                $countWawan = DB::table($tableName)->where($col, $picWawan)->count();
                if ($countWawan > 0) {
                    echo "Found PIC 10157 (Wawan) in Table: [{$tableName}], Column: [{$col}], Count: {$countWawan}\n";
                }
            } catch (\Exception $e) {}
        }
    }
}

echo "\n=== 3. DETAIL DATA DI SETIAP TABEL YANG TERKAIT 13028 ===\n";

echo "-- Activities for 13028:\n";
$acts = DB::table('activities')->where('id_client', $idClientOld)->get();
foreach ($acts as $a) {
    echo "ID: {$a->id} | Date: {$a->date} | Activity: {$a->activity} | id_pic: {$a->id_pic} | id_sales: {$a->id_sales}\n";
}

echo "-- Machine for 13028:\n";
$macs = DB::table('machine')->where('id_client', $idClientOld)->get();
foreach ($macs as $m) {
    echo "ID: {$m->id} | Name/Brand: " . ($m->brand ?? '') . " " . ($m->model ?? '') . " | Serial: " . ($m->serial ?? ($m->serial_number ?? '')) . "\n";
}

echo "-- CRM Status for 13028:\n";
$crms = DB::table('crm_status')->where('id_client', $idClientOld)->get();
foreach ($crms as $c) {
    echo "ID: {$c->id} | Status: " . json_encode($c) . "\n";
}

echo "\n=== 4. CEK RELASI PIC 8318 & 10157 ===\n";
echo "PIC 8318: " . json_encode(DB::table('pic')->where('id', $picSukmawatiOld)->first()) . "\n";
echo "PIC 10157: " . json_encode(DB::table('pic')->where('id', $picWawan)->first()) . "\n";
echo "PIC 4270 (Target Keep): " . json_encode(DB::table('pic')->where('id', $picSukmawatiKeep)->first()) . "\n";
