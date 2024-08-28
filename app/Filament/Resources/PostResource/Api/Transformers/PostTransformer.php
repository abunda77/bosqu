<?php
namespace App\Filament\Resources\PostResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PostTransformer extends JsonResource
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
            'title' => $this->title,
            'body' => $this->body,
            'feature_image' => $this->feature_image,
        ];
    }
}
