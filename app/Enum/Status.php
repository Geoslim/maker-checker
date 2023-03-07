<?php

namespace App\Enum;

enum Status: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
}
