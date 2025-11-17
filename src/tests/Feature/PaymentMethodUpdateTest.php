<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use Laravel\Dusk;

class PaymentMethodUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_changed_payment_method_is_reflected_on_confirmation_page()
    {

        /* 支払い方法の選択を小計欄に反映される処理は、JavaSriptで実装。
        11月10日　担当コーチとの打ち合わせで、フロントエンドのみで完結する処理は、phpUnitではテストできないので、
            支払い方法の選択が小計欄に反映されることを確認するテストコードは省略しました。
        */
    }
}
