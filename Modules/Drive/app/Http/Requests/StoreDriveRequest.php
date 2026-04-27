<?php

namespace Modules\Drive\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDriveRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'link' => 'required|url',
            'jenis' => 'required|in:personal,tim',
            'personal' => 'required_if:jenis,personal|nullable|integer|exists:users,id',
            'tim' => 'required_if:jenis,tim|nullable|integer|exists:roles,id',
            'akses' => 'required|in:edit,view',
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
