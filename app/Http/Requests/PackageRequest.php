<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'title.en' => ($isUpdate ? 'sometimes' : 'required') . '|string|max:255',
            'title.ar' => ($isUpdate ? 'sometimes' : 'required') . '|string|max:255',
            'vehicle_count' => ($isUpdate ? 'sometimes' : 'required') . '|integer|min:1',
            'price' => ($isUpdate ? 'sometimes' : 'required') . '|numeric|min:0',
            'duration' => ($isUpdate ? 'sometimes' : 'required') . '|integer|min:1',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'offer_percent' => 'nullable|numeric|min:0|max:100',
            'active' => 'sometimes|boolean',
        ];
    }
}
