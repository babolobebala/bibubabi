<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCoreConfigRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'quick_menu_keys' => ['present', 'array'],
            'quick_menu_keys.*' => ['string'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
