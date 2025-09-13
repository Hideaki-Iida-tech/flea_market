<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'item_name' => ['required', 'string'],
            'description' => ['required', 'max:255'],
            'item_image' => ['required', 'file', 'mimes:jpeg,png'],
            //複数選択可能 最低一つは必須
            'categories' => ['required', 'array', 'min:1'],
            //'categories.*' => ['integer', 'exists:caetgories,id'],
            //データベースの値を参照して設定
            //'condition_id' => ['required', 'integer', 'exists:conditions,id'],
            'price' => ['required', 'integer', 'min:0'],
        ];
    }
    public function messages()
    {
        return [
            'item_name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以内で入力してください',
            'item_image.required' => '商品画像を指定してください',
            'item_image.mimes' => '商品画像は拡張子.jpegかpngのファイルを指定してください',
            'price.required' => '価格を入力してください',
            'price.min' => '価格は0円以上を入力してください',
            'categories.required' => '最低一つは選択してください',
            'categories.array' => '不正な形式です',
            'categories.min' => '最低一つは選択してください',
            'categories.*.integer' => '不正な値が含まれています',
            'categories.*.exists' => '選択されたカテゴリーが存在しません',
        ];
    }
}
