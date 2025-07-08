<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if($this->isMethod('POST')||$this->isMethod('PUT')){
            $rules = [
                'name' => 'required|max:50|unique:roles,name',
                'slug' => 'required|max:50|unique:roles,slug',
                'description' => '',
                'full-access' => '',
            ];
            if ($this->isMethod('PUT')) {
                $rules['name'] .= ',' . $this->route('role')->id;
                $rules['slug'] .= ',' . $this->route('role')->id;
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
            'slug' => 'slug',
            'description' => 'descripción',
            'full-access' => 'acceso de administrador'
        ];
    }
}
