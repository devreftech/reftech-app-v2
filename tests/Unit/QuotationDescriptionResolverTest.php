<?php

namespace Tests\Unit;

use App\Services\QuotationDescriptionResolver;
use PHPUnit\Framework\TestCase;

class QuotationDescriptionResolverTest extends TestCase
{
    public function test_returns_title_when_present(): void
    {
        $result = QuotationDescriptionResolver::resolve('Service Overhaul', [
            ['subtitle' => 'Inspection', 'detail' => [['detail' => 'Bearing replacement']]],
        ]);

        $this->assertSame('Service Overhaul', $result);
    }

    public function test_falls_back_to_subtitle_and_detail_when_title_missing(): void
    {
        $result = QuotationDescriptionResolver::resolve('', [
            ['subtitle' => 'Inspection', 'detail' => [['detail' => 'Bearing replacement']]],
            ['subtitle' => 'Labor', 'detail' => [['detail' => 'Valve service']]],
        ]);

        $this->assertSame('Inspection | Bearing replacement | Labor | Valve service', $result);
    }
}
