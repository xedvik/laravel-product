<?php

namespace App\Jobs;

use App\Repositories\Interfaces\OwnershipRepositoryInterface;
use App\Enums\OwnershipType;
use App\Enums\OwnershipStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EndRentalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;
    public int $backoff = 30;

    public function __construct(
        private int $ownershipId,
        private Carbon $originalExpiresAt
    ) {
        $this->onQueue('rental-management');
    }

    /**
     * Выполняет завершение аренды
     */
    public function handle(OwnershipRepositoryInterface $ownershipRepository): void
    {
        try {
            $ownership = $ownershipRepository->findById($this->ownershipId);

            // Проверяем, что это действительно аренда
            if ($ownership->type !== OwnershipType::RENT->value) {
                Log::warning('EndRentalJob: Запись не является арендой', [
                    'ownership_id' => $this->ownershipId,
                    'type' => $ownership->type
                ]);
                return;
            }

            // Ключевая проверка: была ли аренда продлена?
            if ($ownership->rental_expires_at->ne($this->originalExpiresAt)) {
                Log::info('EndRentalJob: Аренда была продлена, джоб отменен', [
                    'ownership_id' => $this->ownershipId,
                    'original_expires_at' => $this->originalExpiresAt,
                    'current_expires_at' => $ownership->rental_expires_at
                ]);
                return;
            }

            // Проверяем, действительно ли аренда истекла
            if ($ownership->rental_expires_at->isFuture()) {
                Log::warning('EndRentalJob: Аренда еще не истекла', [
                    'ownership_id' => $this->ownershipId,
                    'expires_at' => $ownership->rental_expires_at,
                    'current_time' => now()
                ]);
                return;
            }

            // Помечаем аренду как завершенную
            $this->markRentalAsExpired($ownership);

            Log::info('EndRentalJob: Аренда успешно завершена', [
                'ownership_id' => $this->ownershipId,
                'user_id' => $ownership->user_id,
                'product_id' => $ownership->product_id,
                'expired_at' => $ownership->rental_expires_at,
                'processed_at' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('EndRentalJob: Ошибка при завершении аренды', [
                'ownership_id' => $this->ownershipId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            throw $e;
        }
    }

    /**
     * Помечает аренду как истекшую
     */
    private function markRentalAsExpired($ownership): void
    {
        $ownership->update(['status' => OwnershipStatus::RENTED_EXPIRED]);
    }

    /**
     * Обработка неудачного выполнения джоба
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('EndRentalJob: Критическая ошибка при завершении аренды', [
            'ownership_id' => $this->ownershipId,
            'original_expires_at' => $this->originalExpiresAt,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }

    /**
     * Получает уникальный идентификатор джоба для возможной отмены например через horizon
     */
    public function getJobId(): string
    {
        return "end_rental_{$this->ownershipId}_{$this->originalExpiresAt->timestamp}";
    }
}
