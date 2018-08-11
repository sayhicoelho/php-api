<?php

namespace App;

use Core\Model;

class Role extends Model
{
    protected static $table = 'roles';

    protected $fields = [
        'id',
        'name',
        'created_at',
        'updated_at',
    ];
}
