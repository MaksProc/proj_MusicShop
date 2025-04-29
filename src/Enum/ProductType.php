<?php
namespace App\Enum;

enum ProductType: string
{
    case BUY = 'buy';
    case RENT = 'rent';
    case BOTH = 'both';
}