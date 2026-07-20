<?php

use App\Libraries\TransferFeeCalculator;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class TransferFeeCalculatorTest extends CIUnitTestCase
{
    public function testAddsExtraPercentageForOtherOperators(): void
    {
        $baseFee = 100.0;
        $prefix = [
            'est_autre_operateur' => 1,
            'pourcentage_extra' => 10,
        ];

        $this->assertSame(110.0, TransferFeeCalculator::calculateFee($baseFee, $prefix));
    }

    public function testKeepsBaseFeeWhenPrefixIsNotMarkedAsOtherOperator(): void
    {
        $baseFee = 100.0;
        $prefix = [
            'est_autre_operateur' => 0,
            'pourcentage_extra' => 10,
        ];

        $this->assertSame(100.0, TransferFeeCalculator::calculateFee($baseFee, $prefix));
    }
}
