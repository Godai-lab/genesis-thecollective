<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
{
    /**
     * Determine if the Account is authorized to make this request.
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
                'name' => 'required|max:50|unique:accounts,name',
                'description' => 'max:200',
                'status' => '',
            ];
            if ($this->isMethod('PUT')) {
                $rules['name'] .= ',' . $this->route('account')->id; // Agrega el ID del registro actual a ignorar
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
            'status' => 'estado'
        ];
    }
}
