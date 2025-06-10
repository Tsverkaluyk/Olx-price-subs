<?php

namespace App\Enums;

enum NotificationType: string
{
    case SUBSCRIPTION = 'subscription';
    case PRICE_CHANGE = 'price_change';

    public function title(): string
    {
        return match ($this) {
            self::SUBSCRIPTION => 'Підтвердження підписки',
            self::PRICE_CHANGE => 'Зміна ціни',
        };
    }
}
