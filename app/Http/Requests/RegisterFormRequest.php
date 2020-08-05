<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterFormRequest extends FormRequest
{
  /**
  * Determine if the user is authorized to make this request.
  *
  * @return bool
  */
  public function authorize()
  {
    return true;
  }

  /**
  * Get the validation rules that apply to the request.
  *
  * @return array
  */
  public function rules()	    {
    return [
      'name' => 'required|string',
      'email' => 'required|email|unique:users',
      'password' => 'required|string|min:6|max:50',
      'phone'=>'required|max:15',
      // 'image'=>'required',
      // 'domicile'=>'required',
      'location_id'=>'required',
      'rol_id'=>'required|exists:roles,id',
    ];
  }

  public function messages()
  {
    return [
      // cart id
      'email.unique' => "El correo electrónico ya esta en uso. Elige otro distinto",

    ];
  }
}
