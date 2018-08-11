<?php

$router->prefix('v1');

require routes_dir('v1/public.php');

require routes_dir('v1/auth.php');

require routes_dir('v1/admin.php');

require routes_dir('v1/user.php');
