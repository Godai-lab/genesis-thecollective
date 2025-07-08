<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
{
    /**
     * Determine if the Brand is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        if($this->isMethod('POST')||$this->isMethod('PUT')){
            $rules = [
                'name' => 'required|max:50',
                'description' => 'max:200',
                'country' => 'required|max:50',
                'assistant' => 'required|max:80',
                'status' => '',
                'account_id' => 'exists:accounts,id',
            ];
            if ($this->isMethod('PUT')) {
                $rules['name'] .= ',' . $this->route('brand')->id; // Agrega el ID del registro actual a ignorar
            }
            return $rules;
        }
    }
    public function messages()
    {
        return [
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'nombre',
            'description' => 'descripción',
            'country' => 'país',
            'assistant' => 'asistente',
            'status' => 'estado',
            'account_id' => 'cuenta',
        ];
    }
}
