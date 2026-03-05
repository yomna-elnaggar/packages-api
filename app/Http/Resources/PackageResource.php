<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'title' => [
                'en' => $this->getTranslation('title', 'en'),
                'ar' => $this->getTranslation('title', 'ar'),
            ],
            'vehicle_count' => $this->vehicle_count,
            'price' => $this->price,

            'duration' => $this->duration,
            'description' => [
                'en' => $this->getTranslation('description', 'en'),
                'ar' => $this->getTranslation('description', 'ar'),
            ],
            'offer_percent' => $this->offer_percent,
        ];
    }
}
