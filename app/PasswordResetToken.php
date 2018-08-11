<?php

namespace App;

use Core\Model;

class PasswordResetToken extends Model
{
    protected static $table = 'password_resets';

    protected $fields = [
        'id',
        'user_id',
        'token',
        'expires_at',
        'created_at',
        'updated_at',
    ];
}
