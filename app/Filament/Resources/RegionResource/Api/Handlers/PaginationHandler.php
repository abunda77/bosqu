<?php
namespace App\Filament\Resources\RegionResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Resources\RegionResource;
use App\Models\Region;

class PaginationHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = RegionResource::class;

    public static bool $public = true;

    public function handler()
    {
        $query = static::getEloquentQuery();
        $model = static::getModel();

        $query = QueryBuilder::for($query)
        ->allowedFields(Region::getAllowedFields())
        ->allowedSorts(Region::getAllowedSorts())
        ->allowedFilters(Region::getAllowedFilters())
        ->allowedIncludes(Region::getAllowedIncludes())
        ->paginate(request()->query('per_page'))
        ->appends(request()->query());

        return static::getApiTransformer()::collection($query);
    }
}
