## Использование
- Клонировать репо
- Копировать содержимое .env.example в .env
- Выполнить:
    + composer update
    + php artisan passport:install
    + docker-compose up
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