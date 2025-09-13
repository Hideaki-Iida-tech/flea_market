<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
    public function rules()
    {
        return [
            //
            //'profile_image' => ['required', 'file', 'mimes:jpeg,png'],
            'name' => ['required', 'string', 'max:20'],
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address' => ['required', 'string'],
        ];
    }
    public function messages()
    {
        return [
            //'profile_image.required' => 'プロフィール用画像ファイルを指定してください',
            //'profile_image.mimes' => '拡張子.jpegか.pngを指定してください',
            'name.required' => 'ユーザー名を入力してください',
            'name.max' => 'ユーザー名は20字以内で入力してください。',
            'postal_code.require' => '郵便番号を入力してください',
            'postal_code.regex' => '数字7桁ハイフン1の形式で郵便番号を入力してください',
            'address.required' => '住所を入力してください',
        ];
    }
}
