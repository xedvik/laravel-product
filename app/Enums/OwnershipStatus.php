<?php

namespace App\Enums;

enum OwnershipStatus: string
{
    case PURCHASED = 'purchased';
    case RENTED_ACTIVE = 'rented_active';
    case RENTED_EXPIRED = 'rented_expired';
    case UNKNOWN = 'unknown';

    /**
     * Получить название статуса для API
     */
    public function getLabel(): string
    {
        return match($this) {
            self::PURCHASED => 'Куплен',
            self::RENTED_ACTIVE => 'Активная аренда',
            self::RENTED_EXPIRED => 'Аренда истекла',
            self::UNKNOWN => 'Неизвестно',
        };
    }

    /**
     * Получить описание статуса
     */
    public function getDescription(): string
    {
        return match($this) {
            self::PURCHASED => 'Товар приобретен в собственность',
            self::RENTED_ACTIVE => 'Товар находится в активной аренде',
            self::RENTED_EXPIRED => 'Срок аренды товара истек',
            self::UNKNOWN => 'Статус товара неопределен',
        };
    }


    /**
     * Получить все возможные статусы
     */
    public static function getAllStatuses(): array
    {
        return [
            self::PURCHASED->value => self::PURCHASED->getLabel(),
            self::RENTED_ACTIVE->value => self::RENTED_ACTIVE->getLabel(),
            self::RENTED_EXPIRED->value => self::RENTED_EXPIRED->getLabel(),
            self::UNKNOWN->value => self::UNKNOWN->getLabel(),
        ];
    }
}
