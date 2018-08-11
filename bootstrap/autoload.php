<?php

spl_autoload_register(function ($class_path) {
    $file = __DIR__."/../{$class_path}.php";

    if (file_exists($file))
    {
        require_once $file;
    }
    else
    {
        throw new Exception("The requested file {$file} cannot be found.");
    }
});
