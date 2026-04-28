<?php

namespace Modules\Drive\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDriveRequest extends FormRequest
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
            'status' => 'required|in:success,error',
            'catatan' => 'nullable|string',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->link && ! preg_match('/^https?:\/\//i', $this->link)) {
            $this->merge([
                'link' => 'https://'.$this->link,
            ]);
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
