<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileRequest extends FormRequest
{
    /**
     * Determine if the File is authorized to make this request.
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
        if($this->isMethod('POST')){

            $rules = [
                'name' => 'required|max:50',
                'file' => 'required|file|mimes:pdf,doc,docx,txt,xls,xlsx,csv|max:30720',
                'read_from_db' => '',
                'status' => '',
            ];
            
            return $rules;
        }else if ($this->isMethod('PUT')) {
            $rules = [
                'status' => '',
            ];
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
            'file' => 'archivo',
            'read_from_db' => 'leer desde base de datos',
            'status' => 'estado',
            'account_id' => 'cuenta'
        ];
    }
}
