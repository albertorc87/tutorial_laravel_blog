<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'comment' => 'required|max:1000',
            'post_id' => 'exists:App\Models\Post,id',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'comment.required' => 'A comment is required',
            'comment.max' => 'A comment cannot exceed 1000 characters',
            'post_id.exists' => 'You must sent a valid post'
        ];
    }

    protected function failedAuthorization()
    {
        throw new AuthorizationException('You must be logged in to write comments');
    }
}
