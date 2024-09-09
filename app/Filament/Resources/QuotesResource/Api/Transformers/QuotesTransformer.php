<?php
namespace App\Filament\Resources\QuotesResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class QuotesTransformer extends JsonResource
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

            'id' => $this->id,
            'quotes' => $this->quotes,
            'author' => $this->author,

        ];
    }
}
