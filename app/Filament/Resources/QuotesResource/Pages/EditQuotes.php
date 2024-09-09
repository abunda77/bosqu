<?php

namespace App\Filament\Resources\QuotesResource\Pages;

use App\Filament\Resources\QuotesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuotes extends EditRecord
{
    protected static string $resource = QuotesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
