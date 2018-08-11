<?php

namespace App;

use Core\Model;
use App\Services\UserService;
use App\Services\RoleService;

class User extends Model
{
    use UserService, RoleService;

    protected static $table = 'users';

    protected $fields = [
        'id',
        'name',
        'email',
        'password',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'password',
    ];
}
