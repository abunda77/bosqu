<?php

namespace App\Filament\Resources\TestUploadResource\Pages;

use App\Filament\Resources\TestUploadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTestUpload extends EditRecord
{
    protected static string $resource = TestUploadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
