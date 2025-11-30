# flea_market

## Docker ビルド

### git clone SSH

`git clone git@github.com:Hideaki-Iida-tech/flea_market.git`

### git clone HTTPS

`git clone https://github.com/Hideaki-Iida-tech/flea_market.git`

＊SSH か HTTPS かどちらか一方でクローンしてください。

### Docker ビルド・起動

`cd flea_market`<br>
`docker-compose up -d --build`

＊MySQL は、OS によって起動しない場合があるのでそれぞれの PC に合わせて docker-compose.yml ファイルを編集してください。

## Laravel 環境構築

### 1.PHP コンテナに入る

`docker-compose exec php bash`

### 2.composer install

`composer install`

### 3.`.env`と`.env.testing`作成 ＆ 環境変数を設定

目的：Laravel が DB・メール・Stripe などのサービスに接続できるようにする

#### `.env`を作成編集

`cp .env.example .env`<br>
`chown `ユーザー名`:`グループ名` .env`

＊各環境変数<br>
DB_DATABASE=laravel_db<br>
DB_USERNAME=laravel_user<br>
DB_PASSWORD=laravel_pass<br>
<br>
MAIL_MAILER=smtp<br>
MAIL_HOST=mailhog<br>
MAIL_PORT=1025<br>
<br>
MAIL_FROM_ADDRESS="no-reply@example.test"<br>
MAIL_FROM_NAME="Example"<br>
<br>
STRIPE_PUBLIC_KEY=＊<br>
STRIPE_SECRET_KEY=＊<br>
<br>
上記の値につき`.env`ファイルに未設定の値がある場合には、上記の値を設定してください。<br>
＊Stripe の秘密鍵及び公開鍵については、案件シートの基本設計書（生徒様入力用）の<br>
一番下に追記した値を入力してください。<br>

#### アプリケーションキー生成と設定キャッシュのクリア、値の生成の確認

`php artisan key:generate`<br>
`php artisan config:clear`<br>
`.env`の APP_KEY=に値が入っていることを確認<br>

#### .env.testing を作成編集

`cp .env.testing.example .env.testing`<br>
＊各環境変数<br>
DB_CONNECTION=mysql_test<br>
<br>
TEST_DB_DATABASE=demo_test<br>
TEST_DB_USERNAME=root<br>
TEST_DB_PASSWORD=root<br>
<br>
MAIL_MAILER=smtp<br>
MAIL_HOST=mailhog<br>
MAIL_PORT=1025<br>
<br>
MAIL_FROM_ADDRESS="no-reply@example.test"<br>
MAIL_FROM_NAME="Example"<br>
<br>
STRIPE_PUBLIC_KEY=`__SET_IN_LOCAL_ENV__`<br>
STRIPE_SECRET_KEY=`__SET_IN_LOCAL_ENV__`<br>
<br>
上記の値につき`.env.testing`ファイルに未設定の値がある場合には、上記の値を設定してください。<br>

#### アプリケーションキー生成と設定キャッシュのクリア、値の生成の確認

`php artisan key:generate --env=testing`<br>
`php artisan config:clear`<br>
`.env.testing`の APP_KEY=に値が入っていることを確認<br>

## マイグレーション及びシーディングの実行

`php artisan migrate`<br>
`php artisan db:seed`<br>
＊シーダーで<br>
メールアドレス：test1@example.com パスワード：password<br>
メールアドレス：test2@example.com パスワード：password<br>
メールアドレス：test3@example.com パスワード：password<br>
の 3 名を作成している<br>
PHP コンテナからログアウト<br>

## データベースおよびテーブルが作成され、シーディングが成功していることを確認

### 1. MySQL コンテナにログイン

`docker-compose exec mysql mysql -u root -p`<br>
Enter password：root<br>

### 2.データベースを表示、選択

`SHOW DATABASES;`<br>
ここで、laravel_db が作成されていることを確認してください。<br>
`USE laravel_db;`<br>

### 3.テーブルを表示

`SHOW TABLES;`<br>
ここで、categories、category_item、comments、conditions、item_likes、items、<br>
orders、users の各テーブルが作成されていることを確認してください。<br>

### 4.各テーブルのレコード数を表示（シーディングが実行されていることを確認）

`SELECT COUNT(*) FROM categories;`→14 件<br>
`SELECT COUNT(*) FROM category_item;`→17 件<br>
`SELECT COUNT(*) FROM comments;`→0 件<br>
`SELECT COUNT(*) FROM conditions;`→4 件<br>
`SELECT COUNT(*) FROM item_likes;`→0 件<br>
`SELECT COUNT(*) FROM items;`→10 件<br>
`SELECT COUNT(*) FROM orders;`→0 件<br>
`SELECT COUNT(*) FROM users;`→3 件<br>
＊件数は手動でユーザー登録、出品処理、購入処理を行わずに、一度だけシーダーを走らせた場合です。<br>
MySQL コンテナからログアウト

## シンボリックリンクの作成と確認

### 1.作成

`docker-compose exec php bash`<br>
`php artisan storage:link`<br>

### 2. 確認

`ls -l public/`<br>
＊PHP コンテナ内で実行<br>
lrwxrwxrwx 1 ユーザー名 グループ名 作成日時 storage -> /var/www/storage/app/public<br>
と表示されれば成功<br>

## プロフィール画像および商品画像を保存するディレクトリの作成

`cd storage/app/public`<br>
`mkdir -p tmp/profiles`<br>
`mkdir -p tmp/items`<br>
`mkdir profiles`<br>
`mkdir items`<br>
このディレクトリ作成処理次の所有権限及びパーミッションの変更前に必ず行ってください。（PHP コンテナ内）<br>
でないと、プロフィール画面の保存処理、商品画像の保存処理のときに、Internal Server Error 500 が発生します。<br>

## Storage ディレクトリと bootstrap/cache の所有及びパーミッションの変更

`chown -R www-data:www-data storage bootstrap/cache`<br>
`chmod -R 775 storage bootstrap/cache`<br>
※環境によっては、所有権限及びパーミッション関係のエラーが発生することがあります。<br>
エラーが発生した場合は、PHP コンテナ内でこれらのコマンドを実行してください<br>
コマンドを実行するタイミングによっては、プロフィール画像や商品画像の登録時等に<br>
Internal Server Error 500 が発生することがありますので、その際にはもう一度これらの<br>
コマンドを実行してください。<br>
PHP コンテナからログアウト

## テストの実施

### 1.テストコード用データベースの作成と確認

`docker-compose exec mysql mysql -u root -p`<br>
Enter password：root<br>
`CREATE DATABASE demo_test;`<br>
`SHOW DATABASES;`→demo_test が表示されれば OK<br>
MySQL コンテナからログアウト<br>

### 2.テストの実行

`docker-compose exec php bash`<br>
`php artisan test --testsuite=Feature`または、<br>
`php artisan test --filter=ItemIndexTest`＊<br>
＊は tests/Feature ディレクトリ下の各テストクラスのクラス名を指定

## 使用技術

- PHP 8.1

- Laravel 8.83

- MySQL 8.0

- Fortify

- Stripe

- MailHog

## ER 図

このアプリの主要なテーブル構造は以下の通りです。<br>
![ER 図](/docs/er/flea_market_er.png)

## URL 一覧

- 開発環境：
  http://localhost

- phpMyAdmin：
  http://localhost:8080

- MailHog:
  http://localhost:8025
