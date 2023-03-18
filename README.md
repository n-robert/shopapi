### Установка
- `git clone git@github.com:n-robert/shopapi`
- `cd shopapi`
- `chown -R www-data:www-data storage bootstrap/cache` \
- `chgrp -R www-data storage bootstrap/cache` \
- `chmod -R ug+rwx storage bootstrap/cache`
- `docker compose up --build --remove-orphans -d`
- `docker exec -it php-fpm-shopapi bash`
- `composer install`
- `php artisan migrate`
- `php artisan db:seed`
- `php artisan passport:install`

### API запросы
Импортировать _**shopapi.postman_collection.json**_ для тестирования в Postman.

Auth: Bearer Token
- POST /register: `{"name":"test","email":"test@shopapi.com","password":"test"}`
- POST /login: `{"email":"test@shopapi.com","password":"test", "remember_me":true}`
- POST /logout
- GET /api/user
- POST /api/products: `{"title":"Product 1", "cost":2100, "quantity": 10}`
- PUT /api/products/{id}: `{"title": "Product 1", "cost": "1100.00", "quantity": 15}`
- DELETE /api/products/{id}
- GET /api/products
- POST /api/cart-items: `{"items":[{"id":1,"quantity":2}]}`
- PUT /api/cart-items: `{"items": [{"id": 1, "quantity": 3}]}`
- DELETE /api/cart-items: `{"itemIds": [1]}`
- GET /api/carts
- POST /api/orders: `{"cartId"}`
- PUT /api/orders: `{"details", "status"}`
- DELETE /api/orders/{id}
- GET /api/orders

### Загрузка товаров из нескольких источников (например база данных и CSV-файл)
- Создать сервисы `MysqlUploadService, PgsqlUploadService, CsvUploadService... implements 
UploadServiceInterface`.
- Создать метод `upload($uploadService)` и помощью DI подключить нужный сервис.
