<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMt4HiringRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
          'service_id'=>'required|exists:services,id',
          'users'=>'required|array',
          'description'=>'required'
        ];
    }

    public function messages(){

        return [
            "users.array" => "Por favor, seleccione a uno o más trabajadores"
        ];

    }
}
