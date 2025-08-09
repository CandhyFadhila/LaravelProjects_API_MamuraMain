<?php

namespace App\Http\Resources\Promo;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromoResource extends JsonResource
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
            'image' => $this->documents,
            'name' => $this->name,
            'description' => $this->description,
            'terms' => $this->terms,
            'promo_value' => $this->promo_value, // Percent
            'promo_end' => $this->promo_end,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}
