<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
      'name' => 'string',
      'email' => 'email|unique:users',
      'password' => 'string|min:6|max:10',
      'phone'=>'max:15',
      'image'=>'',
      'rol_id'=>'exists:roles,id',
    ];
  }
}
