<?php

namespace App\Enums;

enum DocumentType: string
{
    case IdentityFront = 'identity_front';
    case IdentityBack = 'identity_back';
    case License = 'license';

    public function label(): string
    {
        return match ($this) {
            self::IdentityFront => 'Pièce d\'identité (recto)',
            self::IdentityBack => 'Pièce d\'identité (verso)',
            self::License => 'Permis de conduire',
        };
    }
}
