<?php

namespace App\Enums;
use Filament\Support\Contracts\HasLabel;

enum PropertyCategory: string implements HasLabel
{
    case Home = 'home';
    case Warehouse = 'warehouse';
    case Apartement = 'apartement';
    case HomeShop = 'homeshop';
    case Kavling = 'kavling';
    case Office = 'office';

    public function getLabel(): ?string
    {
        return $this->name;
        return match ($this) {
            self::Home => 'Rumah',
            self::Warehouse => 'Gudang',
            self::Apartement => 'Apartemen',
            self::HomeShop => 'Ruko',
            self::Kavling => 'Kavling',
            self::Office => 'Kantor',
        };
    }
}
