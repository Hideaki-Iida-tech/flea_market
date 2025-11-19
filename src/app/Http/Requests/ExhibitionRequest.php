<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;

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

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        //parent::failedValidation($validator); // ← 標準のリダイレクト（old付き）
        $response = redirect()->to($this->getRedirectUrl())
            ->withInput($this->all())              // ← PFV後の全入力を明示的にフラッシュ
            ->withErrors($validator, $this->errorBag);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }

    protected function prepareForValidation()
    {
        // 画像が送られてきたら、 tmp に保存して、そのパスを hidden 用に差し込む
        if ($this->hasFile('item_image')) {
            $tmpPath = $this->file('item_image')->store('tmp/items', 'public');
            $this->merge(['current_item_image' => $tmpPath]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'item_name' => ['required', 'string'],
            'description' => ['required', 'max:255'],
            'item_image' => ['nullable', 'image', 'mimes:jpeg,png', 'max:2048'],
            'current_item_image' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    // 画像ファイルも hidden もどちらも空ならエラー
                    if (!$this->hasFile('item_image') && empty($value)) {
                        $fail('商品画像を指定してください');
                    }
                },
            ],
            //複数選択可能 最低一つは必須
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['integer', 'exists:categories,id'],
            //データベースの値を参照して設定
            'condition_id' => ['required', 'integer', 'min:1', 'exists:conditions,id'],
            'brand' => ['sometimes', 'nullable', 'string'],
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
            'condition_id.required' => '商品の状態を選択してください',
            'condition_id.exists' => '商品の状態を選択してください',
            'condition_id.min' => '商品の状態を選択してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if (!$this->hasFile('item_image') && !$this->filled('current_item_image')) {
                // どちらも空ならエラー
                $validator->errors()->add('item_image', '商品画像を指定してください');
            }
        });
    }
}
