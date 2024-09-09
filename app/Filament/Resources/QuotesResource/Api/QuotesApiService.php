<?php
namespace App\Filament\Resources\QuotesResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\QuotesResource;
use Illuminate\Routing\Router;


class QuotesApiService extends ApiService
{
    protected static string | null $resource = QuotesResource::class;

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
