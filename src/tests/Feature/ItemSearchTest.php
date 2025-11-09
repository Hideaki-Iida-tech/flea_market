<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use Illuminate\Support\Facades\DB;

class ItemSearchTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_items_can_be_filterd_by_partial_match_in_item_name()
    {
        // 一覧ページに未認証でアクセス
        $response = $this->get('/');
        $response->assertStatus(200);

        // 検索ワードを設定
        $keyword = '時計';

        // 検索ワードをGET送信
        $response = $this->get('/?keyword=' . urlencode($keyword));
        $response->assertStatus(200);

        // 指定の検索フォーム断片（@section('input') 相当）がレンダリングされていること
        $response->assertSee('<form id="search-form" action="/" method="get">', false);
        $response->assertSee('<input type="text" id="search-box" name="keyword"', false);

        // 検索ボックスに「腕時計」がvalueとして表示されていること（old('keyword', ...) の確認）
        $response->assertSee('value="時計"', false);

        // 部分一致結果の表示確認（ヒットする商品名は表示）
        $response->assertSee('腕時計', false);

        // 非ヒット商品は表示されない
        $response->assertDontSee('HDD');
        $response->assertDontSee('革靴');
    }

    public function test_search_keyword_is_retained_on_mylist()
    {
        // ログイン用userインスタンスを取得
        $user = User::where('name', 'テストユーザー１')->first() ?? User::all()->random();
        // マッチするが「いいねしていない」商品（表示されないべき）の出品者用userインスタンスを取得
        $buyer = User::where('name', 'テストユーザー2')->first() ?? User::all()->random();
        // 1) マッチして「いいね」している商品（表示されるべき）
        $likedAndMatch = Item::where('item_name', '腕時計')->first() ?? Item::all()->random();
        // 2) マッチしないが「いいね」している商品（表示されないべき）
        $likedButNotMatch = Item::where('item_name', 'HDD')->first() ?? Item::all()->random();

        // 3) マッチするが「いいねしていない」商品（表示されないべき）
        $conditionId = Condition::where('name', '良好')->value('id') ?? Condition::value('id');
        $notLikedButMatch = Item::create([
            'item_image' => 'https://www.example.com/sample.jpeg',
            'condition_id' => $conditionId,
            'item_name' => '懐中時計',
            'brand' => 'Rolax',
            'description' => 'スタイリッシュなデザインの懐中時計',
            'price' => 15000,
            'user_id' => $buyer->id,
        ]);

        // いいねをする
        $likedAndMatch->likes()->syncWithoutDetaching([$user->id]);
        $likedButNotMatch->likes()->syncWithoutDetaching([$user->id]);
        // $notLikedButMatch はいいねしない

        // 中間テーブルから、ログインユーザーがいいねをしているレコードのitem_idを取得
        /*$likedItemsIndex = DB::table('item_likes')
            ->where('user_id', $user->id)
            ->pluck('item_id')
            ->unique()
            ->values();*/

        // ログインユーザーがいいねをしている商品のレコードを取得
        /*$likedItems = Item::whereIn('id', $likedItemsIndex)->get();*/

        // 一覧ページに認証状態でアクセス
        /*$response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);*/

        // 検索ワードを設定
        $keyword = '時計';

        // 検索ワードをGET送信
        $response = $this->actingAs($user)->get('/?keyword=' . urlencode($keyword));
        $response->assertStatus(200);

        // マイリストリンクをクリック
        $base = '/';
        $query = ['keyword' => $keyword];
        $response = $this->actingAs($user)
            ->get($base . '?' . http_build_query(array_merge(['tab' => 'mylist'], $query)));
        $response->assertStatus(200);

        // A. 検索ボックスに keyword が保持されていること（value="時計" をHTMLで確認）
        $this->assertMatchesRegularExpression(
            '/<input[^>]*name="keyword"[^>]*value="' . preg_quote($keyword, '/') . '"/u',
            $response->getContent()
        );

        // B. 表示アイテムの検証
        //  表示されるべき：いいね済み & キーワードマッチ
        $response->assertSee($likedAndMatch->item_name);

        //  表示されないべき：いいね済みだがキーワード非マッチ
        $response->assertDontSee($likedButNotMatch->item_name);

        //  表示されないべき：キーワードはマッチするが、いいねしていない
        $response->assertDontSee($notLikedButMatch->item_name);
    }
}
