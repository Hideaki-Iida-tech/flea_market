<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;

class ItemShowTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_item_detail_page_shows_correct_information()
    {
        // 詳細を表示する商品itemのインスタンスを生成
        $item = Item::with('condition')->where('item_name', '腕時計')->first()
            ?? Item::with('condition')->inRandomOrder()->first();
        // いいねをするユーザーのインスタンスを生成
        $likeUser = User::where('name', 'テストユーザー2')->first()
            ?? User::inRandomOrder()->first();
        // コメントをするユーザーのインスタンスを生成
        $commentUser = User::where('name', 'テストユーザー3')->first()
            ?? User::inRandomOrder()->first();

        // いいねをする
        $item->likes()->syncWithoutDetaching([$likeUser->id]);

        // コメントをする
        Comment::create([
            'user_id' => $commentUser->id,
            'item_id' => $item->id,
            'body' => 'test',
        ]);

        // 商品詳細ページを開く
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);

        // htmlを取得
        $html = $response->getContent();

        // 商品の画像が表示されているか確認
        $response->assertSee($item->image_url);
        // 商品名が表示されているか確認
        $response->assertSee($item->item_name);
        // ブランド名が表示されているか確認
        $response->assertSee($item->brand);
        // 価格が表示されているか確認
        $response->assertSee(number_format($item->price));

        // いいね数が表示されているか確認
        $likeCount = DB::table('item_likes')
            ->where('item_id', $item->id)->count();
        $this->assertMatchesRegularExpression(
            '/<div\s+class="like-count">\s*' .
                preg_quote((string)$likeCount, '/') .
                '\s*<\/div>/',
            $html
        );
        // コメント数が表示されているか確認
        $commentCount = Comment::where('item_id', $item->id)->count();
        $this->assertMatchesRegularExpression(
            '/<div\s+class="comment-count">\s*' .
                preg_quote((string)$commentCount, '/') .
                '\s*<\/div>/',
            $html
        );

        // カテゴリーが表示されているか確認
        $categoriesIndex = DB::table('category_item')
            ->where('item_id', $item->id)
            ->pluck('category_id')
            ->unique()
            ->values();
        $categories = Category::whereIn('id', $categoriesIndex)->get();

        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }

        // 商品の状態が表示されているか確認
        $response->assertSee($item->condition->name);

        // コメントしたユーザー情報、コメント内容が表示されているか確認
        $comments = Comment::where('item_id', $item->id)->get();

        foreach ($comments as $comment) {
            $currentImage = optional($comment->user)->profile_image
                ? asset('storage/' . $comment->user->profile_image) : '';
            $response->assertSee($currentImage);
            $response->assertSee($comment->user->name);
            $response->assertSee($comment->body);
        }
    }

    public function test_multiple_selected_categories_are_displayed()
    {
        // 詳細を表示する商品itemのインスタンスを生成
        $item = Item::where('item_name', '腕時計')->first()
            ?? Item::inRandomOrder()->first();

        // 商品一覧ページを表示
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);

        // カテゴリーが表示されているか確認
        $categoriesIndex = DB::table('category_item')
            ->where('item_id', $item->id)
            ->pluck('category_id')
            ->unique()
            ->values();
        $categories = Category::whereIn('id', $categoriesIndex)->get();

        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }
}
