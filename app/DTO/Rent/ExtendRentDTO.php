<?php
namespace App\DTO\Rent;

/**
 * DTO для продления аренды
 * @param int $ownership_id - ID существующей аренды
 * @param int $additional_hours - дополнительные часы
 */
class ExtendRentDTO
{
    public function __construct(
        public int $ownership_id,
        public int $additional_hours,
    ) {}
}
