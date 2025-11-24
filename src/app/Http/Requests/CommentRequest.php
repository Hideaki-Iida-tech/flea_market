<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
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
     * パスパラメータitem_idをバリデーション対象に追加するメソッド
     * @return array
     */
    public function validationData()
    {
        return array_merge($this->all(), [
            'item_id' => (int) $this->route('item_id'),
        ]);
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
            'item_id' => ['required', 'exists:items,id', 'integer', 'min:1',],
            'body' => ['required', 'max:255'],
        ];
    }

    /**
     * バリデーションエラー時のメッセージを設定するメソッド
     * @return array
     */
    public function messages()
    {
        return [
            'body.required' => 'コメントを入力してください',
            'body.max' => 'コメントは255文字以内で入力してください'
        ];
    }
}
