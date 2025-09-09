<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            'payment_method' => ['required', 'integer', 'in:1,2'],
            'pastal_code' => ['required', 'regex://^\d{3}-\d{4}$'],
            'address' => ['required', 'string'],
            'building' => ['string'],
        ];
    }
    public function message()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
            'payment_method.inteer' => '支払い方法は1.コンビニ払い、2.カード払いから選択してください',
            'payment_method.in' => '支払い方法は1.コンビニ払い、2.カード払いから選択してください',
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex' => '郵便番号は7桁ハイフンありで入力してください',
            'address.required' => '住所を入力してください'
        ];
    }
}
