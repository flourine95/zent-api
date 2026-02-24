<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
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
            'product_variant' => new ProductVariantResource($this->whenLoaded('productVariant')),
            'quantity' => $this->quantity,
            'subtotal' => $this->subtotal,
        ];
    }
}
