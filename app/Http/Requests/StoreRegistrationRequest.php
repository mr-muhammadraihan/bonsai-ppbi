<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'bonsais' => ['required', 'array', 'min:1', 'max:20'],
            'bonsais.*.bonsai_type' => ['required', 'string', 'max:255'],
            'bonsais.*.size' => ['required', 'in:Small,Medium,Large,Mame'],
            'bonsais.*.class' => ['required', 'in:Jadi,Prospek'],
            'bonsais.*.status' => ['required', 'in:Peserta,Pemenang'],
            'bonsais.*.predicate' => ['nullable', 'string', 'max:255'],
            'bonsais.*.description' => ['nullable', 'string'],
            'bonsais.*.photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:51200'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'bonsais.*.bonsai_type' => 'jenis bonsai',
            'bonsais.*.size' => 'ukuran bonsai',
            'bonsais.*.class' => 'kelas bonsai',
            'bonsais.*.status' => 'status bonsai',
            'bonsais.*.photo' => 'foto bonsai',
        ];
    }
}
