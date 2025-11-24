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
     * Laravel既定のメソッドfailedValidatinをオーバーライド
     * バリデーション失敗時のリダイレクト挙動を完全にカスタマイズしている
     * 既定のメソッドでは一部の入力値がold()で復元されないため。
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        //parent::failedValidation($validator); // ← 親クラスのメソッドを呼ばないことで、標準のリダイレクトを無効化
        $response = redirect()->to($this->getRedirectUrl())
            ->withInput($this->all())              // ← PFV後の全入力を明示的にフラッシュ
            ->withErrors($validator, $this->errorBag);

        // 例外を投げて、バリデーションエラーを発生させる
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }

    /**
     * バリデーション実行前にコールされるLaravel既定のメソッドをオーバーライド
     * input type="file" name="profile_image"のダイアログで選択されて送信された画像ファイルが存在する場合、
     * その画像ファイルを一時保存し、その一時パスをcurrent_profile_imageとしてバリデーションデータに追加
     * その他のテキストデータを適切な形に整形
     * @return void
     */
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

        // すべての項目が埋まっていれば、プロフィール設定完了フラグをtrueに
        $this->merge([
            'is_profile_completed' => ($this->hasFile('profile_image') || filled(optional($this->user())->profile_image)) && filled($this->name) &&
                filled($this->postal_code) &&
                filled($this->address),
        ]);

        // 画像が送られてきたら、 tmp に保存して、そのパスを hidden 用に差し込む
        if ($this->hasFile('profile_image')) {
            $tmpPath = $this->file('profile_image')->store('tmp/profiles', 'public');
            $this->merge(['current_profile_image' => $tmpPath]);
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
            'profile_image' => [
                'nullable',
                'image',
                'mimes:jpeg,png',
                'max:2048',
                function ($attribute, $file, $fail) {
                    // クライアント送信の拡張子（ユーザーの元ファイル名ベース）
                    $ext = strtolower($file->getClientOriginalExtension() ?: '');

                    if (! in_array($ext, ['jpeg', 'png'], true)) {
                        $fail('拡張子は jpeg / png のみアップロードできます。');
                    }

                    // さらに厳しく：MIMEタイプの最終確認（冗長だが堅牢）
                    $mime = $file->getMimeType();
                    if (! in_array($mime, ['image/jpeg', 'image/png'], true)) {
                        $fail('ファイル形式が不正です。JPEG または PNG を指定してください。');
                    }
                },
            ],
            'name' => ['required', 'string', 'max:20'],
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address' => ['required', 'string', 'max:255'],
            'building' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    /**
     * バリデーションエラー時のメッセージを設定
     * @return void
     */
    public function messages()
    {
        return [
            'profile_image.required' => 'プロフィール用画像ファイルを指定してください',
            'profile_image.mimes' => '拡張子.jpegか.pngを指定してください',
            'name.required' => 'ユーザー名を入力してください',
            'name.max' => 'ユーザー名は20字以内で入力してください。',
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex' => '数字7桁ハイフン1の形式で郵便番号を入力してください',
            'address.required' => '住所を入力してください',
        ];
    }

    /**
     * Laravel既定のメソッドをオーバーライド
     * バリデーション後の追加チェック処理を差しこんでいる
     * ダイアログで画像ファイルが選択されず、hiddenにもパスが設定されていない場合、
     * バリデーションエラーとしている。
     * @param Illuminate\Validation\Validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if (!$this->hasFile('profile_image') && !$this->filled('current_profile_image')) {
                // どちらも空ならエラー
                $validator->errors()->add('profile_image', 'プロフィール用画像ファイルを指定してください');
            }
        });
    }
}
