<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'price' => $this->price,
            'original_price' => $this->original_price,
            'options' => $this->options,
            'images' => $this->image_urls,
            'main_image' => $this->main_image ? asset("storage/{$this->main_image}") : null,
            'full_name' => $this->full_name,
        ];
    }
}
