<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Order;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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
    public function validationData()
    {
        return array_merge($this->all(), [
            'item_id' => (int) $this->route('item_id'),
            'user_id' => auth()->id(),
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
            'user_id' => ['required', 'exists:users,id', 'integer', 'min:1',],
            'item_id' => ['required', 'exists:items,id', 'unique:orders', 'integer', 'min:1',],
            'price' => ['required', 'integer', 'min: 0',],
            'payment_method' => ['required', 'integer', 'in:' . implode(',', array_keys(Order::$paymentLabels)),],
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/',],
            'address' => ['required', 'string',],
            'building' => ['string', 'nullable'],
        ];
    }
    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
            'payment_method.integer' => '支払い方法は1.コンビニ払い、2.カード払いから選択してください',
            'payment_method.in' => '支払い方法は1.コンビニ払い、2.カード払いから選択してください',
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex' => '郵便番号は7桁ハイフンありで入力してください',
            'address.required' => '住所を入力してください',
        ];
    }
}
