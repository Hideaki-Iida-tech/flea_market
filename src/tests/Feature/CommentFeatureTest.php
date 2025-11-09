<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;

class CommentFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_logged_user_can_submit_comment()
    {
        // 詳細を表示する商品itemのインスタンスを生成
        $item = Item::where('item_name', '腕時計')->first()
            ?? Item::inRandomOrder()->first();
        // いいねするログインユーザーのインスタンスを生成
        $user = User::where('name', 'テストユーザー1')->first()
            ?? User::inRandomOrder()->first();

        // 生成したuserについて、メール認証済みにする
        $user->markEmailAsVerified();

        // 商品一覧ページを表示
        $response = $this->actingAs($user)->get('/item/' . $item->id);
        $response->assertStatus(200);

        // コメントの本文を設定
        $comment = ['body' => 'test'];

        // コメントを送信
        $response = $this->actingAs($user)->post('/item/' . $item->id .
            '/comment', $comment);
        $response->assertStatus(302);
        $response->assertRedirect('/item/' . $item->id);

        // コメントがDBに登録されているかどうかを確認
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'body' => 'test',
        ]);
    }

    public function test_guest_user_cannot_submit_comment()
    {
        // 詳細を表示する商品itemのインスタンスを生成
        $item = Item::where('item_name', '腕時計')->first()
            ?? Item::inRandomOrder()->first();

        // 商品一覧ページを表示
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);

        // コメントの本文を設定
        $comment = ['body' => 'test'];

        // コメントを送信
        $response = $this->post('/item/' . $item->id .
            '/comment', $comment);

        // ログインページに誘導される
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_validation_message_is_displayed_when_comment_body_is_empty()
    {
        // 詳細を表示する商品itemのインスタンスを生成
        $item = Item::where('item_name', '腕時計')->first()
            ?? Item::inRandomOrder()->first();
        // いいねするログインユーザーのインスタンスを生成
        $user = User::where('name', 'テストユーザー1')->first()
            ?? User::inRandomOrder()->first();

        // 生成したuserについて、メール認証済みにする
        $user->markEmailAsVerified();

        // 商品一覧ページを表示
        $response = $this->actingAs($user)->get('/item/' . $item->id);
        $response->assertStatus(200);

        // コメントの本文を設定
        $comment = ['body' => ''];

        // コメントを送信
        $response = $this->actingAs($user)->post('/item/' . $item->id .
            '/comment', $comment);

        // バリデーションエラーが発生することを確認
        $response->assertSessionHasErrors(['body']);

        // 戻の画面にリダイレクトすることを確認
        $response->assertStatus(302);
        $response->assertRedirect('/item/' . $item->id);

        // エラーメッセージがセッションに入っているか確認
        $this->assertTrue(session()->has('errors'));
        $this->assertEquals(session('errors')->first('body'), 'コメントを入力してください');

        // コメントがDBに登録されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'body' => '',
        ]);
    }

    public function test_validation_message_is_displayed_when_comment_body_exceeds_255_characters()
    {
        // 詳細を表示する商品itemのインスタンスを生成
        $item = Item::where('item_name', '腕時計')->first()
            ?? Item::inRandomOrder()->first();
        // いいねするログインユーザーのインスタンスを生成
        $user = User::where('name', 'テストユーザー1')->first()
            ?? User::inRandomOrder()->first();

        // 生成したuserについて、メール認証済みにする
        $user->markEmailAsVerified();

        // 商品一覧ページを表示
        $response = $this->actingAs($user)->get('/item/' . $item->id);
        $response->assertStatus(200);

        // コメントの本文を設定
        $comment = ['body' => 'abcdefghijklmnopqrstuvwxyz
        abcdefghijklmnopqrstuvwxyz
        abcdefghijklmnopqrstuvwxyz
        abcdefghijklmnopqrstuvwxyz
        abcdefghijklmnopqrstuvwxyz
        abcdefghijklmnopqrstuvwxyz
        abcdefghijklmnopqrstuvwxyz
        abcdefghijklmnopqrstuvwxyz
        abcdefghijklmnopqrstuvwxyz
        abcdefghijklmnopqrstuvwxyz'];

        // コメントを送信
        $response = $this->actingAs($user)->post('/item/' . $item->id .
            '/comment', $comment);

        // バリデーションエラーが発生することを確認
        $response->assertSessionHasErrors(['body']);

        // 戻の画面にリダイレクトすることを確認
        $response->assertStatus(302);
        $response->assertRedirect('/item/' . $item->id);

        // エラーメッセージがセッションに入っているか確認
        $this->assertTrue(session()->has('errors'));
        $this->assertEquals(session('errors')->first('body'), 'コメントは255文字以内で入力してください');

        // コメントがDBに登録されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'body' => 'abcdefghijklmnopqrstuvwxyz
        abcdefghijklmnopqrstuvwxyz
        abcdefghijklmnopqrstuvwxyz
        abcdefghijklmnopqrstuvwxyz
        abcdefghijklmnopqrstuvwxyz
        abcdefghijklmnopqrstuvwxyz
        abcdefghijklmnopqrstuvwxyz
        abcdefghijklmnopqrstuvwxyz
        abcdefghijklmnopqrstuvwxyz
        abcdefghijklmnopqrstuvwxyz',
        ]);
    }
}
