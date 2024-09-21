<?php
namespace App\Filament\Resources\RegionResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RegionTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'level' => $this->level,
        ];
    }
}
