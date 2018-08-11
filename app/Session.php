<?php

namespace App;

use Core\Model;

class Session extends Model
{
    protected static $table = 'sessions';

    protected $fields = [
        'id',
        'user_id',
        'token',
        'user_agent',
        'ip',
        'expires_at',
        'created_at',
        'updated_at',
    ];
}
