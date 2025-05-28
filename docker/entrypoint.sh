#!/bin/sh
set -e

# Создаем необходимые директории если их нет
mkdir -p /var/www/html/storage/logs \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/framework/cache \
    /var/www/html/bootstrap/cache

# Устанавливаем права доступа
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Функция для проверки готовности RabbitMQ
wait_for_rabbitmq() {
    echo "Ожидание готовности RabbitMQ..."
    while ! nc -z rabbitmq 5672; do
        echo "RabbitMQ не готов, ожидание..."
        sleep 2
    done
    echo "RabbitMQ готов!"
}

# Если команда не передана, запускаем PHP-FPM
if [ $# -eq 0 ]; then
    echo "Запуск PHP-FPM..."
    exec php-fpm
else
    # Если команда содержит queue:work, ждем RabbitMQ и создаем очереди
    if echo "$*" | grep -q "queue:work"; then
        wait_for_rabbitmq
        echo "Создание очередей RabbitMQ..."
        php artisan queue:create-rabbitmq-queues || echo "Не удалось создать очереди, продолжаем..."
    fi
    echo "Выполнение команды: $@"
    exec "$@"
fi
