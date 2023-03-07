<?php

namespace App\Enum;

enum RequestType: string
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
}
