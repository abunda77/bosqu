<?php
namespace App\Filament\Resources\RegionResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\RegionResource;
use Illuminate\Routing\Router;


class RegionApiService extends ApiService
{
    protected static string | null $resource = RegionResource::class;

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
