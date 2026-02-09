<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'thumbnail' => $this->thumbnail ? asset('storage/'.$this->thumbnail) : null,
            'specs' => $this->specs,
            'is_active' => $this->is_active,
            'category' => $this->whenLoaded('category', function () {
                return new CategoryResource($this->category);
            }),
            'variants' => $this->whenLoaded('variants', function () {
                return $this->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'sku' => $variant->sku,
                        'price' => $variant->price,
                        'original_price' => $variant->original_price,
                        'options' => $variant->options,
                        'images' => $variant->images ? collect($variant->images)->map(fn ($img) => asset('storage/'.$img))->toArray() : [],
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
