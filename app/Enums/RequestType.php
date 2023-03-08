<?php

namespace App\Enums;

enum RequestType: string
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
}
