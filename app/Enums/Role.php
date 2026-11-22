<?php

namespace App\Enums;

enum Role: string
{
    case Customer = 'customer';
    case Kasir = 'kasir';
    case Manajer = 'manajer';

    public function label(): string
    {
        return match ($this) {
            self::Customer => 'Customer',
            self::Kasir => 'Kasir',
            self::Manajer => 'Manajer',
        };
    }
}
