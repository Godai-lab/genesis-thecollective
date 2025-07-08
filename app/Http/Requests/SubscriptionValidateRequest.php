<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionValidateRequest extends FormRequest
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
                'user_id' => 'required|exists:users,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'status' => '',
            ];
            if ($this->isMethod('PUT')) {
                
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
            'user_id' => 'usuario',
            'start_date' => 'fecha inicio',
            'end_date' => 'fecha fin',
            'status' => 'estado'
        ];
    }
}
