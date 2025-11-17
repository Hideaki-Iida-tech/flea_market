# flea_market

## Docker ビルド

### git clone SSH

`git clone git@github.com:Hideaki-Iida-tech/flea_market.git`

### git clone HTTPS

`git clone https://github.com/Hideaki-Iida-tech/flea_market.git`

### Docker ビルド・起動

`docker-compose up -d --build`

＊MySQL は、OS によって起動しない場合があるのでそれぞれの PC に合わせて docker-compose.yml ファイルを編集してください。

## Laravel 環境構築

### 1.PHP コンテナに入る

`docker-compose exec php bash`

### 2.composer install

`composer install`

### 3.`.env`作成 ＆ 環境変数を設定

`cp .env.example .env`

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
STRIPE_PUBLIC_KEY=<br>
STRIPE_SECRET_KEY=<br>

`php artisan key:generate`<br>
`php artisan migrate`<br>
`php artisan db:seed`<br>
＊シーダーで<br>
メールアドレス：test1@example.com パスワード：password<br>
メールアドレス：test2@example.com パスワード：password<br>
メールアドレス：test3@example.com パスワード：password<br>
の 3 名を作成している<br>

`php artisan storage:link`

### 4．テスト用データベースを作成

`docker-compose exec mysql bash`<br>
`mysql -u root -p`<br>
Enter password：root<br>
`CREATE DATABASE demo_test;`<br>

### 5.`.env.testing` 作成　＆ 環境変数を設定

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

`php artisan key:generate --env=testing`<br>
`php artisan config:clear`<br>

## Storage ディレクトリと bootstrap/cache の所有及びパーミッションの変更

`sudo chown -R www-data:www-data storage bootstrap/cache`<br>
`sudo chmod -R 775 storage bootstrap/cache`<br>

## テストの実施

`docker-compose exec php bash`<br>
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

![ER 図](/docs/er/flea_market_er.png)

## URL 一覧

- 開発環境：
  http://localhost

- phpMyAdmin：
  http://localhost:8080

- MailHog:
  http://localhost:8025
