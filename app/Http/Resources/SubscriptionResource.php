<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\CompanyService;

class SubscriptionResource extends JsonResource
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

            'company_id' => $this->company_id,
            'package_id' => $this->package_id,

            'subscribed_at' => $this->subscribed_at ? $this->subscribed_at->format('Y-m-d') : null,
            'expires_at' => $this->expires_at ? $this->expires_at->format('Y-m-d') : null,

            'num_of_cars' => $this->num_of_cars,
            'price' => $this->price,
            'price_with_tax' => $this->price_with_tax,
            'payment_status' => $this->payment_status,

            'is_active' => $this->expires_at ? $this->expires_at > now() : false,

            // Relations
            // Note: Company data should be fetched from the external service by the frontend using company_id
            // Or fetched in the controller and passed here if needed.
            'company' => [
                'id' => $this->company_id,
                'name' => app(CompanyService::class)->getCompanyById($this->company_id)['company_name'] ?? null,
            ],

            'package' => [
                'id' => $this->package->id ?? null,
                'name' => $this->package->title ?? null,
            ],
        ];
    }
}
