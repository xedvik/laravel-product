FROM rabbitmq:3-management

# Устанавливаем curl и скачиваем плагин delayed message exchange
RUN apt-get update && apt-get install -y curl && \
    curl -L -o /opt/rabbitmq/plugins/rabbitmq_delayed_message_exchange-3.13.0.ez \
    https://github.com/rabbitmq/rabbitmq-delayed-message-exchange/releases/download/v3.13.0/rabbitmq_delayed_message_exchange-3.13.0.ez && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Включаем плагины
RUN rabbitmq-plugins enable --offline rabbitmq_management rabbitmq_delayed_message_exchange
