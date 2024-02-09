#install package
git clone https://github.com/anjuarianto/skripsi-nikke-backend.git
cd skripsi-nikke-backend
composer install
cp .env.example .env

#sesuaikan parameter pada file .env database dengan services yang berjalan
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=[nama_database]
DB_USERNAME=[username_database]
DB_PASSWORD=[user_password_database]

#lakukan clear dan cache config
php artisan config:clear
php artisan config:cache
php artisan optimize

#pulish config sanctum authentication
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

#buat database kosong dengan nama sesuai yan tertera di config lalu migrate
php artisan migrate:refresh --seed