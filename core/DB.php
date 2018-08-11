<?php

namespace Core;

use PDO;

abstract class DB
{
    /**
     * The PDO instance.
     *
     * @var \PDO
     */
    private static $pdo;

    /**
     * The PDO data types.
     *
     * @var array
     */
    protected static $dataTypes = [
        'boolean' => PDO::PARAM_BOOL,
        'string' => PDO::PARAM_STR,
        'integer' => PDO::PARAM_INT,
        'double' => PDO::PARAM_STR,
        'NULL' => PDO::PARAM_NULL,
    ];

    /**
     * Get the PDO instance.
     *
     * @return \PDO
     */
    private static function getInstance ()
    {
        if (is_null(self::$pdo))
        {
            $host = Config::get('database', 'host');
            $port = Config::get('database', 'port');
            $name = Config::get('database', 'name');
            $user = Config::get('database', 'user');
            $pass = Config::get('database', 'pass');

            self::$pdo = new PDO("mysql:host={$host};port={$port};dbname={$name};charset=utf8", $user, $pass);

            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
            self::$pdo->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }

        return self::$pdo;
    }

    /**
     * Perform a query to database.
     *
     * @param  string  $sql
     * @return \PDOStatement
     */
    protected static function query ($sql)
    {
        return self::getInstance()->query($sql);
    }

    /**
     * Prepare a query to be performed.
     *
     * @param  string  $sql
     * @return \PDOStatement
     */
    protected static function prepare ($sql)
    {
        return self::getInstance()->prepare($sql);
    }

    /**
     * Get the last inserted id from database.
     *
     * @return int
     */
    protected static function getInsertedId ()
    {
        return self::getInstance()->lastInsertId();
    }
}
