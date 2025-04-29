<?php

namespace App\Enum;

enum RentalStatus: string
{
    case ACTIVE = 'active';
    case RETURNED = 'returned';
    case BOUGHT = 'bought';
}