<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHiringRequest extends FormRequest
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
          'hiring_id'=>'required|exists:hirings,id',
          'status_id'=>'required|exists:statuses,id',
          // 'comment'=>'max:50',
          'calification'=>'in:1,2,3,4,5'
        ];
    }
}
