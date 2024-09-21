<?php

namespace App\Filament\Resources\RegionResource\Api\Handlers;

use App\Filament\Resources\RegionResource;
use App\Models\Region;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{identifier}';
    public static string | null $resource = RegionResource::class;
    public static bool $public = true;

    public function handler(Request $request)
    {
        $identifier = $request->route('identifier');

        $query = static::getEloquentQuery();

        $query = QueryBuilder::for($query)
            ->where(function ($q) use ($identifier) {
                $q->where('level', $identifier)
                  ->orWhere('name', $identifier)
                  ->orWhere('code', $identifier);
            })
            ->allowedIncludes(Region::getAllowedIncludes())
            ->allowedFilters(Region::getAllowedFilters())
            ->allowedSorts(Region::getAllowedSorts())
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        $transformer = static::getApiTransformer();

        return new $transformer($query);
    }
}
