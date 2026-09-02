<?php

namespace App\Enums;

enum BonsaiType: string
{
    case BERINGIN = 'Bonsai Beringin / Ficus';
    case CEMARA = 'Bonsai Cemara (Juniperus)';
    case SISIR = 'Bonsai Sisir (Cudrania cochinchinensis)';
    case ANTING_PUTRI = 'Bonsai Anting Putri (Wrightia religiosa)';
    case SANTIGI = 'Bonsai Santigi (Pemphis acidula)';
    case SANCANG = 'Sancang (Premna)';
    case SERUT = 'Streblus asper';
    case ASAM_JAWA = 'Bonsai Asam Jawa (Tamarindus indica)';
    case CASUARINA = 'Cemara Udang (Casuarina equisetifolia)';
    case HIBISCUS = 'Hibiscus (Waru) (Hibiscus tiliaceus)';
    case BOUGENVIL = 'Bougainvillea (Kembang Kertas)';
    case HOKIANTE = 'Hokiantea (Carmona)';
    case KAMBOJA_JEPANG = 'Bonsai Kamboja Jepang (Adenium obesum)';
    case CERI_SAKURA = 'Bonsai Ceri / Sakura Jepang (Prunus)';
    case MURBEI = 'Bonsai Murbei (Morus)';
    case LOHANSUNG = 'Lohansung (Podocarpus)';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type): array => [$type->value => $type->value])
            ->all();
    }
}
