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

        Schema::create('unit_quotation_detail', function ($table) {
            $table->id();
            $table->unsignedBigInteger('id_unit_quotation');
            $table->string('type')->nullable();
            $table->unsignedBigInteger('id_unit')->nullable();
            $table->unsignedBigInteger('id_fixed_asset')->nullable();
            $table->unsignedBigInteger('id_equivalent')->nullable();
            $table->text('spec_visible')->nullable();
            $table->text('label')->nullable();
            $table->text('description')->nullable();
            $table->decimal('qty', 10, 2)->default(1);
            $table->string('info_qty')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('disc', 5, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->integer('sort_order')->default(0);
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

    public function test_it_saves_details_with_sequential_sort_order_regardless_of_array_keys(): void
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('PDO SQLite driver is not available in this environment.');
        }

        $quote = UnitQuotation::create([
            'title' => 'Test Quote',
            'no_quote' => '002-PU/BDG/RJO/2026',
        ]);

        $controller = new class extends UnitQuotationController {
            public function exposeSaveDetails(int $quoteId, array $items): void
            {
                $this->saveDetails($quoteId, $items);
            }
        };

        // Simulating reordered items submitted with non-sequential original keys (e.g. key 2 first, key 0 second, key 1 third)
        $reorderedItems = [
            2 => ['type' => 'custom', 'label' => 'Moved to Top Item (was index 2)', 'qty' => 1, 'price' => 100],
            0 => ['type' => 'custom', 'label' => 'Moved to Middle Item (was index 0)', 'qty' => 1, 'price' => 200],
            1 => ['type' => 'custom', 'label' => 'Moved to Bottom Item (was index 1)', 'qty' => 1, 'price' => 300],
        ];

        $controller->exposeSaveDetails($quote->id, $reorderedItems);

        $details = \App\Models\UnitQuotationDetail::where('id_unit_quotation', $quote->id)
            ->orderBy('sort_order')
            ->get();

        $this->assertCount(3, $details);
        $this->assertSame('Moved to Top Item (was index 2)', $details[0]->label);
        $this->assertSame(0, $details[0]->sort_order);
        $this->assertSame('Moved to Middle Item (was index 0)', $details[1]->label);
        $this->assertSame(1, $details[1]->sort_order);
        $this->assertSame('Moved to Bottom Item (was index 1)', $details[2]->label);
        $this->assertSame(2, $details[2]->sort_order);
    }
}
