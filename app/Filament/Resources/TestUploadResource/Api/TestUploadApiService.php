<?php
namespace App\Filament\Resources\TestUploadResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\TestUploadResource;
use Illuminate\Routing\Router;


class TestUploadApiService extends ApiService
{
    protected static string | null $resource = TestUploadResource::class;

    public static function handlers() : array
    {
        return [
            Handlers\CreateHandler::class,
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class
        ];

    }
}
