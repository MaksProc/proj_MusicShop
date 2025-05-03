<?php

namespace App\Enum;

enum RentalStatus: string
{
    case ONGOING = "ongoing";
    case RETURNED = "returned";
    case PURCHASED = "purchased";
}