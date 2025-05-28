<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\RabbitMQQueue;

class CreateQueues extends Command
{
    protected $signature = 'queue:create-rabbitmq-queues';
    protected $description = 'Создает очереди RabbitMQ';

    public function handle()
    {
        $connection = Queue::connection('rabbitmq');

        if (!$connection instanceof RabbitMQQueue) {
            $this->error('Подключение не является RabbitMQ');
            return 1;
        }

        $queues = ['rental-management'];

        foreach ($queues as $queueName) {
            try {
                // Отправляем пустую задачу для создания очереди
                $connection->push(new \App\Jobs\TestQueueJob('Инициализация очереди'), $queueName);
                $this->info("Очередь '{$queueName}' создана");
            } catch (\Exception $e) {
                $this->error("Ошибка создания очереди '{$queueName}': " . $e->getMessage());
            }
        }

        return 0;
    }
}
