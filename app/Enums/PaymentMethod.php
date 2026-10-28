<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case MobileMoney = 'mobile_money';
    case Card = 'card';
    case Transfer = 'transfer';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Espèces',
            self::MobileMoney => 'Mobile money',
            self::Card => 'Carte bancaire',
            self::Transfer => 'Virement',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Cash => 'cash-coin',
            self::MobileMoney => 'phone',
            self::Card => 'credit-card',
            self::Transfer => 'arrow-left-right',
        };
    }
}
