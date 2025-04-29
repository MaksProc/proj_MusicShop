<?php

namespace App\Enum;

enum TransactionType: string
{
    case PURCHASE = 'purchase';
    case RENTAL = 'rental';
}