<?php

namespace Tests\Unit;

use App\Http\Controllers\UnitQuotationController;
use App\Models\PendingPO;
use App\Models\UnitQuotation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UnitQuotationControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!extension_loaded('pdo_sqlite')) {
            return;
        }

        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::dropAllTables();

        Schema::create('unit_quotation', function ($table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('no_quote')->nullable();
            $table->string('po_number')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
        });

        Schema::create('pending_po', function ($table) {
            $table->id();
            $table->unsignedBigInteger('id_quotation')->nullable();
            $table->unsignedBigInteger('id_unit_quotation')->nullable();
            $table->string('title')->nullable();
            $table->string('no_pending')->nullable();
            $table->string('type')->nullable();
            $table->string('project_category')->nullable();
            $table->integer('project_status_step')->nullable();
            $table->integer('status')->default(0);
            $table->date('date')->nullable();
            $table->timestamps();
        });
    }

    public function test_it_creates_pending_po_for_unit_quotation_when_po_is_received(): void
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('PDO SQLite driver is not available in this environment.');
        }

        $quote = UnitQuotation::create([
            'title' => 'Unit Project',
            'no_quote' => '001-PU/BDG/RJO/2026',
            'po_number' => 'PO-001',
            'type' => 'Project',
        ]);

        $controller = new class extends UnitQuotationController {
            public function exposeCreatePendingPoForUnitQuotation(UnitQuotation $quote): PendingPO
            {
                return $this->createPendingPoForUnitQuotation($quote);
            }
        };

        $pending = $controller->exposeCreatePendingPoForUnitQuotation($quote);

        $this->assertInstanceOf(PendingPO::class, $pending);
        $this->assertSame($quote->id, $pending->id_unit_quotation);
        $this->assertSame('Unit Project', $pending->title);
        $this->assertSame('PO-001', $pending->no_pending);
        $this->assertSame('Project', $pending->type);
        $this->assertSame('Unit', $pending->project_category);
        $this->assertSame(1, $pending->project_status_step);
        $this->assertSame(0, $pending->status);
    }

    public function test_it_maps_delivery_values_to_supported_integer_codes(): void
    {
        $controller = new class extends UnitQuotationController {
            public function exposeResolvePendingDeliveryValue($value): int
            {
                return $this->resolvePendingDeliveryValue($value);
            }
        };

        $this->assertSame(1, $controller->exposeResolvePendingDeliveryValue('JNE'));
        $this->assertSame(2, $controller->exposeResolvePendingDeliveryValue('Send By Technician'));
        $this->assertSame(3, $controller->exposeResolvePendingDeliveryValue('Taken Directly'));
        $this->assertSame(4, $controller->exposeResolvePendingDeliveryValue('Ready stock'));
        $this->assertSame(4, $controller->exposeResolvePendingDeliveryValue('Customer'));
    }
}
