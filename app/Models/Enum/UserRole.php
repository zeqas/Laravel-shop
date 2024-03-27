<?php

namespace App\Models\Enum;

enum UserRole: string
{
    case Admin = 'admin';

    case Customer = 'customer';
}
