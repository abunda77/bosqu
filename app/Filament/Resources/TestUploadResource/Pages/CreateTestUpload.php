<?php

namespace App\Filament\Resources\TestUploadResource\Pages;

use App\Filament\Resources\TestUploadResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTestUpload extends CreateRecord
{
    protected static string $resource = TestUploadResource::class;
}
