<?php

namespace Tests\Unit;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PendingPO;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\SerialProduct;
use App\Services\DeletionGuardService;
use Mockery;
use PHPUnit\Framework\TestCase;

class DeletionGuardServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_quotation_cannot_be_deleted_when_related_transactions_exist(): void
    {
        $quotation = Mockery::mock(Quotation::class)->makePartial();
        $quotation->shouldReceive('invoice')->andReturn(new class {
            public function exists(): bool { return true; }
        });
        $quotation->shouldReceive('payment')->andReturn(new class {
            public function exists(): bool { return false; }
        });
        $quotation->shouldReceive('suo')->andReturn(new class {
            public function exists(): bool { return false; }
        });

        $service = new DeletionGuardService();
        $result = $service->checkQuotationDeletion($quotation);

        $this->assertFalse($result['allowed']);
        $this->assertNotEmpty($result['reasons']);
    }

    public function test_product_cannot_be_deleted_when_it_has_equivalents(): void
    {
        $product = Mockery::mock(Product::class)->makePartial();
        $product->shouldReceive('serial')->andReturn(new class {
            public function exists(): bool { return true; }
        });
        $product->shouldReceive('detail')->andReturn(new class {
            public function exists(): bool { return false; }
        });

        $service = new DeletionGuardService();
        $result = $service->checkProductDeletion($product);

        $this->assertFalse($result['allowed']);
        $this->assertNotEmpty($result['reasons']);
    }

    public function test_equivalent_cannot_be_deleted_when_used_in_quotation_details(): void
    {
        $equivalent = Mockery::mock(SerialProduct::class)->makePartial();
        $equivalent->shouldReceive('detailQuotation')->andReturn(new class {
            public function exists(): bool { return true; }
        });
        $equivalent->shouldReceive('detailPending')->andReturn(new class {
            public function exists(): bool { return false; }
        });
        $equivalent->shouldReceive('purchaseRequests')->andReturn(new class {
            public function exists(): bool { return false; }
        });
        $equivalent->shouldReceive('detailDelivery')->andReturn(new class {
            public function exists(): bool { return false; }
        });
        $equivalent->shouldReceive('detailReturn')->andReturn(new class {
            public function exists(): bool { return false; }
        });
        $equivalent->shouldReceive('spareparts')->andReturn(new class {
            public function exists(): bool { return false; }
        });

        $service = new DeletionGuardService();
        $result = $service->checkEquivalentDeletion($equivalent);

        $this->assertFalse($result['allowed']);
        $this->assertNotEmpty($result['reasons']);
    }

    public function test_pending_cannot_be_deleted_when_it_has_related_items(): void
    {
        $pending = Mockery::mock(PendingPO::class)->makePartial();
        $pending->shouldReceive('detail')->andReturn(new class {
            public function exists(): bool { return true; }
        });
        $pending->shouldReceive('pr')->andReturn(new class {
            public function exists(): bool { return false; }
        });
        $pending->shouldReceive('return')->andReturn(new class {
            public function exists(): bool { return false; }
        });

        $service = new DeletionGuardService();
        $result = $service->checkPendingDeletion($pending);

        $this->assertFalse($result['allowed']);
        $this->assertNotEmpty($result['reasons']);
    }

    public function test_invoice_cannot_be_deleted_when_it_has_deliveries(): void
    {
        $invoice = Mockery::mock(Invoice::class)->makePartial();
        $invoice->shouldReceive('delivery')->andReturn(new class {
            public function exists(): bool { return true; }
        });

        $service = new DeletionGuardService();
        $result = $service->checkInvoiceDeletion($invoice);

        $this->assertFalse($result['allowed']);
        $this->assertNotEmpty($result['reasons']);
    }

    public function test_payment_cannot_be_deleted_when_it_is_confirmed(): void
    {
        $payment = Mockery::mock(Payment::class)->makePartial();
        $payment->level = 1;

        $service = new DeletionGuardService();
        $result = $service->checkPaymentDeletion($payment);

        $this->assertFalse($result['allowed']);
        $this->assertNotEmpty($result['reasons']);
    }
}
