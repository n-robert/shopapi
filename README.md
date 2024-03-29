### Установка
- `git clone git@github.com:n-robert/shopapi`
- `cd shopapi`
- `chown -R www-data:www-data storage bootstrap/cache` \
- `chgrp -R www-data storage bootstrap/cache` \
- `chmod -R ug+rwx storage bootstrap/cache`
- `cp .env.example .env`
- `docker compose up --build --remove-orphans -d`
- `docker exec -it php-fpm-shopapi bash`
- `composer install`
- `php artisan migrate`
- `php artisan passport:install`

### API запросы
Импортировать _**shopapi.postman_collection.json**_ для тестирования в Postman.

Auth: Bearer Token

Некоторые API endpoints:
- POST /register: `{"name":"test","email":"test@shopapi.com","password":"test"}`
- POST /login: `{"email":"test@shopapi.com","password":"test", "remember_me":true}`
- POST /logout
- POST /api/products: `{"title": "Product 1", "cost": "1000.00", "quantity": 15}`
- POST /api/carts: `{"items": [{"id": 1,"quantity": 2}]}`
- POST /api/orders: `{"cart_id":1}`
- PUT /api/products/{id}: `{"title": "Product 1", "cost": "1100.00", "quantity": 25}`
- PUT /api/carts/{id}: `{"items": [{"id": 1, "quantity": 3}]}`
- PUT /api/orders/{id}: `{"payment_id": 2, "items": {"1": {"id": 1, "quantity": 4}}}`
- DELETE /api/products/{id}
- DELETE /api/carts/{id}
- DELETE /api/orders/{id}
- GET /api/user
- GET /api/products
- GET /api/carts
- GET /api/orders

### PHPUnit тесты
- `docker exec -it php-fpm-shopapi bash`
- `php artisan test` или `vendor/bin/phpunit`

Тестовые классы:
- ProductControllerTest
- CartControllerTest
- OrderControllerTest
