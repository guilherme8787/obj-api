<?php

namespace App\Enums;

enum PaymentType: string
{
    case PIX = 'P';
    case CARTAO_CREDITO = 'C';
    case CARTAO_DEBITO = 'D';

    public function calculateTax(float $value): float
    {
        return match($this) {
            self::PIX => 0,
            self::CARTAO_CREDITO => $value * 0.05,
            self::CARTAO_DEBITO => $value * 0.03,
        };
    }
}
