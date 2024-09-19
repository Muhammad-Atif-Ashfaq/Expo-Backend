<?php

namespace App\Enums;

final class UserRolesEnum
{
    const SUPERADMIN = 'super admin';
    const ADMIN = 'admin';
    const JUDGE = 'judge';

    public static $USER_TYPES =
    [
        self::SUPERADMIN,
        self::ADMIN,
        self::JUDGE
    ];
}