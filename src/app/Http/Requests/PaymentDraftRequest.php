<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentDraftRequest extends FormRequest
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
            'payment_method' => ['nullable', 'integer',],
            // 追加したパスパラメータ由来のitem_idがitemsテーブルのidに存在することや
            // ordersテーブルに存在する値でないこと等
            'item_id' => ['required', 'exists:items,id', 'unique:orders', 'integer', 'min:1',],
        ];
    }
}
