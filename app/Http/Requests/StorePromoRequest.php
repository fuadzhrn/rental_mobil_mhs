<?php

namespace App\Http\Requests;

use App\Models\Promo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePromoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'promo_code' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('promos', 'promo_code')],
            'description' => ['nullable', 'string', 'max:1000'],
            'discount_type' => ['required', Rule::in([Promo::DISCOUNT_PERCENT, Promo::DISCOUNT_FIXED])],
            'discount_value' => ['required', 'numeric', 'gt:0'],
            'min_transaction' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'quota' => ['nullable', 'integer', 'min:1'],
            'loyal_only' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in([Promo::STATUS_ACTIVE, Promo::STATUS_INACTIVE])],
        ];
    }
}
