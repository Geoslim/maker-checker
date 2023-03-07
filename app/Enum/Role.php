<?php

namespace App\Enum;

enum Role: string
{
    case MAKER = 'maker';
    case CHECKER = 'checker';
    case USER = 'user';

    public static function defaultRole(): string
    {
        return Role::USER->value;
    }
}
