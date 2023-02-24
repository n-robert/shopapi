## Использование
- git clone git@github.com:n-robert/shopapi
- cd shopapi
- chown -R www-data:www-data storage bootstrap/cache \
- chgrp -R www-data storage bootstrap/cache \
- chmod -R ug+rwx storage bootstrap/cache
- docker-compose up --build --remove-orphans -d
- docker exec -it php-fpm-shopapi bash
- composer install
- php artisan passport:install

## URLs
Auth: Bearer Token
- POST /register: {"name","email","password"}
- POST /login: {"email","password"}
- POST /logout
- GET /api/user
- POST /api/products: {"title","slug","cost"}
- PUT /api/products/{id}: {"title","slug","cost"}
- DELETE /api/products/{id}
- GET /api/products
- POST /api/cart-items: {"items":[{"id","quantity"},...]}
- PUT /api/cart-items: {"items":[{"id","quantity"},...]}
- DELETE /api/cart-items: {"itemIds": []}
- GET /api/carts
- POST /api/orders: {"cartId"}
- PUT /api/orders: {"details", "status"}
- DELETE /api/orders/{id}
- GET /api/orders

## Загрузка товаров из нескольких источников (например база данных и CSV-файл)
- Создать сервисы MysqlUploadService, PgsqlUploadService, CsvUploadService... implements 
UploadServiceInterface.
- Создать метод upload($uploadService) и помощью DI подключить нужный сервис.
