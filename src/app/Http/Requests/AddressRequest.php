<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
    public function validationData()
    {
        return array_merge($this->all(), [
            'item_id' => (int) $this->route('item_id'),
        ]);
    }
    protected function prepareForValidation()
    {

        // 文字列として取得（未入力だと null のことがある）
        $raw = (string)$this->input('postal_code', '');

        // 全角数字→半角数字へ（mbstringが有効前提）
        $raw = mb_convert_kana($raw, 'n', 'UTF-8');
        // 数字以外を除去（ハイフン/空白などを取り除く）
        $digits = preg_replace('/\D/u', '', $raw);

        // 7桁なら xxx-xxxx に整形、そうでなければ"数字だけ"を入れておき、後続のルールで弾く
        $this->merge(['postal_code' => strlen($digits) === 7 ? substr($digits, 0, 3) . '-' . substr($digits, 3) : $digits]);
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
            'item_id' => ['required', 'exists:items,id', 'unique:orders', 'integer', 'min:1',],
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address' => ['required', 'string', 'max:255'],
            'building' => ['sometimes', 'string', 'nullable', 'max:255'],
        ];
    }
    public function messages()
    {
        return [
            'postal_code.reqired' => '郵便番号を入力してください',
            'postal_code.regex' => '数字7桁ハイフンありで入力してください',
            'address.required' => '住所を入力してください',
        ];
    }
}
