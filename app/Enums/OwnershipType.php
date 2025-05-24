<?php

namespace App\Enums;

enum OwnershipType: string
{
    case PURCHASE = 'purchase';
    case RENT = 'rent';
}