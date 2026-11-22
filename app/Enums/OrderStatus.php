<?php

namespace App\Enums;

enum OrderStatus: string
{
    case MenungguBayar = 'menunggu_bayar';
    case Diproses = 'diproses';
    case Siap = 'siap';
    case Selesai = 'selesai';
    case Batal = 'batal';

    public function label(): string
    {
        return match ($this) {
            self::MenungguBayar => 'Menunggu Bayar',
            self::Diproses => 'Diproses',
            self::Siap => 'Siap Diambil',
            self::Selesai => 'Selesai',
            self::Batal => 'Batal',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::MenungguBayar => in_array($next, [self::Diproses, self::Batal], true),
            self::Diproses => $next === self::Siap,
            self::Siap => $next === self::Selesai,
            self::Selesai, self::Batal => false,
        };
    }
}
