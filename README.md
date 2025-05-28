# Laravel Product API

API для управления товарами, арендой и покупками в системе Laravel Product.

## 📋 Описание

Система предоставляет REST API для:
- Управления товарами (CRUD операции)
- Аренды товаров
- Покупки товаров
- Аутентификации пользователей
- Проверки статуса товаров



### Установка
git clone ...
docker-compose up -d --build

## 📚 Документация API

### Swagger UI
- **Локальная документация**: [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)
### Основные эндпоинты

#### Аутентификация
- `POST /api/auth/register` - Регистрация пользователя
- `POST /api/auth/login` - Вход в систему
- `POST /api/auth/logout` - Выход из системы

#### Товары
- `GET /api/products` - Список товаров
- `POST /api/products` - Создание товара (требует авторизации)
- `GET /api/products/{id}` - Получение товара
- `PUT /api/products/{id}` - Обновление товара (требует авторизации)
- `DELETE /api/products/{id}` - Удаление товара (требует авторизации)

#### Аренда
- `POST /api/products/{id}/rent` - Аренда товара
- `POST /api/products/{id}/return` - Возврат товара

#### Покупка
- `POST /api/products/{id}/purchase` - Покупка товара

#### Статус товаров
- `GET /api/products/{id}/status` - Проверка статуса товара
