<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'company_id' => ($isUpdate ? 'sometimes' : 'required') . '|string',
            'package_id' => ($isUpdate ? 'sometimes' : 'required') . '|uuid|exists:packages,id',
            'num_of_cars' => ($isUpdate ? 'sometimes' : 'required') . '|integer|min:1',
            'payment_status' => ($isUpdate ? 'sometimes' : 'required') . '|in:paid,free,unpaid',
            'expires_at' => 'nullable|date',
            'subscribed_at' => 'nullable|date',
            'total_price_with_tax' => 'nullable|numeric',
            'price_with_tax' => 'nullable|numeric',
            'price' => 'nullable|numeric',
        ];
    }
}
