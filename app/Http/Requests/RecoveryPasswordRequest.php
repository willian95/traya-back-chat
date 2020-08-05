<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecoveryPasswordRequest extends FormRequest
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
          'email'=>'required|exists:users,email|email'
        ];
    }

    public function messages()
    {
        return [
            // cart id
            'email.required' => "El correo electrónico es requerido",
            'email.exists' => "El correo electrónico ingresado no coincide con un usuario registrado",
            "email.email" => "El correo electrónico ingresado no coincide con un usuario registrado"

        ];
    }

}
