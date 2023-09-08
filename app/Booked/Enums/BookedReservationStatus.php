<?php

namespace App\Booked\Enums;

enum BookedReservationStatus: string
{
    case AWAITING_PAYMENT = 'awaiting payment';
    case PAID = 'paid';
}
