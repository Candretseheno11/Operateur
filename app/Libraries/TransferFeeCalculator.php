<?php

namespace App\Libraries;

class TransferFeeCalculator
{
    public static function calculateFee(float $baseFee, ?array $prefixe = null): float
    {
        if (!$prefixe || (int) ($prefixe['est_autre_operateur'] ?? 0) !== 1) {
            return $baseFee;
        }

        $extraPercentage = (float) ($prefixe['pourcentage_extra'] ?? 0);

        if ($extraPercentage <= 0) {
            return $baseFee;
        }

        return $baseFee + ($baseFee * $extraPercentage / 100);
    }
}
