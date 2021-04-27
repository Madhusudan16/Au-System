<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Admin()
 * @method static static User()
 */
final class UserType extends Enum
{
    const Admin = 'admin';
    const User =  'user';
}
