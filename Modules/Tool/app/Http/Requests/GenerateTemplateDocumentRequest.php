<?php

namespace Modules\Tool\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class GenerateTemplateDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof User;
    }

    public function rules(): array
    {
        return [
            'nama_petugas' => ['required', 'string', 'max:255'],
            'anggaran_membiayai' => ['required', 'string', 'max:255'],
            'tujuan' => ['required', 'string'],
            'anggaran_diperiksa' => ['required', 'string', 'max:255'],
            'jadwal' => ['required', 'string', 'max:255'],
            'ringkasan_hasil' => ['required', 'string'],
            'pejabat_dikunjungi' => ['required', 'string'],
            'has_foto_satu' => ['nullable', 'boolean'],
            'has_foto_dua' => ['nullable', 'boolean'],
            'foto_satu' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'foto_dua' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_petugas.required' => 'Nama petugas wajib diisi.',
            'anggaran_membiayai.required' => 'Anggaran yang membiayai wajib diisi.',
            'tujuan.required' => 'Tujuan wajib diisi.',
            'anggaran_diperiksa.required' => 'Anggaran yang diperiksa wajib diisi.',
            'jadwal.required' => 'Jadwal wajib diisi.',
            'ringkasan_hasil.required' => 'Ringkasan hasil wajib diisi.',
            'pejabat_dikunjungi.required' => 'Pejabat dan tempat yang dikunjungi wajib diisi.',
            'has_foto_satu.boolean' => 'Status foto dokumentasi 1 tidak valid.',
            'has_foto_dua.boolean' => 'Status foto dokumentasi 2 tidak valid.',
            'foto_satu.image' => 'Foto dokumentasi 1 harus berupa gambar.',
            'foto_satu.mimes' => 'Foto dokumentasi 1 harus berformat JPG atau PNG.',
            'foto_satu.max' => 'Foto dokumentasi 1 maksimal 5 MB.',
            'foto_dua.image' => 'Foto dokumentasi 2 harus berupa gambar.',
            'foto_dua.mimes' => 'Foto dokumentasi 2 harus berformat JPG atau PNG.',
            'foto_dua.max' => 'Foto dokumentasi 2 maksimal 5 MB.',
        ];
    }
}
