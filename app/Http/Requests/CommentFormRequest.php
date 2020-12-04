<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentFormRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'bail|required|alpha',
            'email' => 'bail|required|email',
            'content' => 'required|between:5,500',
        ];
    }
}
