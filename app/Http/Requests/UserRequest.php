<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
                'name' => 'required|max:50|unique:users,name',
                'username' => 'required|max:50|alpha_dash|unique:users,username',
                'email' => 'required|max:80|email|unique:users,email',
                'password' => 'required|confirmed|min:8|max:80',
                'role' => 'required|exists:roles,id',
                'accounts' => 'exists:accounts,id',
                'status' => '',
            ];
            if ($this->isMethod('PUT')) {
                $rules['name'] .= ',' . $this->route('user')->id;
                $rules['username'] .= ',' . $this->route('user')->id;
                $rules['email'] .= ',' . $this->route('user')->id;
                $rules['password'] = ($this->password)?'confirmed|min:8|max:80':'';
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
            'username' => 'nombre de usuario',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'role' => 'rol',
            'status' => 'estado',
        ];
    }
}
