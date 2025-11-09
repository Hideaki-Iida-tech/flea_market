# flea_market

Docker ビルド
[git clone リンク SSH]
(git@github.com:Hideaki-Iida-tech/flea_market.git)
[git clone リンク HTTPS]
(https://github.com/Hideaki-Iida-tech/flea_market.git)

`docker-compose up -d --build`

＊MySQL は、OS によって起動しない場合があるのでそれぞれの PC に合わせて docker-compose.yml ファイルを編集してください。

Laravel 環境構築
`docker-compose exec php bash`
`composer install`
3..env.example ファイルから.env を作成し、環境変数を変更

`php artisan key:generate`
`php artisan migrate`
`php artisan db:seed`
`php artisan storage:link`
`composer require stripe/stripe-php`

4..env.testing.example から.env.testing を作成し、環境変数を変更

使用技術
PHP 8.1

Laravel 8.83

MySQL 8.0

Fortify

Stripe

MailHog

ER 図
ER 図

URL
開発環境：http://localhost

phpMyAdmin：http://localhost:8080

MailHog: http://localhost:8025
