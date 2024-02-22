### Install Package

```bash
git clone https://github.com/anjuarianto/skripsi-nikke-backend.git 
cd skripsi-nikke-backend
composer install
cp .env.example .env
php artisan key:generate
```


### Sesuaikan parameter pada file .env database dengan services yang berjalan
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=[nama_database]
DB_USERNAME=[username_database]
DB_PASSWORD=[user_password_database]
```

### Publish config sanctum authentication
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```


### Publish config spatie permission
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

### Lakukan clear dan cache config
```bash
php artisan config:clear
php artisan config:cache
php artisan optimize
```

### Buat database kosong dengan nama sesuai yan tertera di config lalu migrate
```bash
php artisan migrate:refresh --seed
```
