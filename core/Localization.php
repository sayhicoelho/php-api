<?php

namespace Core;

use Core\Exceptions\InvalidLangFileException;
use Core\Exceptions\InvalidLangKeyException;

abstract class Localization
{
    /**
     * The translations.
     *
     * @var array
     */
    private static $trans = [];

    /**
     * Load the application's translations.
     *
     * @param  string  $lang
     * @param  string  $file
     * @return void
     */
    private static function loadLangs ($lang, $file)
    {
        if (!isset(self::$trans[$lang][$file]))
        {
            $lang_dir = lang_dir("{$lang}/{$file}.php");

            if (file_exists($lang_dir))
            {
                self::$trans[$lang][$file] = require_once $lang_dir;
            }
            else
            {
                throw new InvalidLangFileException("The lang file \"{$lang}/{$file}\" cannot be found.");
            }
        }
    }

    /**
     * Get a specified string based on current locale.
     *
     * @param  string  $file
     * @param  string  $key
     * @param  array   $attributes
     * @return string
     */
    public static function get ($file, $key, array $attributes = [])
    {
        $lang = Config::get('app', 'lang');

        self::loadLangs($lang, $file);

        if (isset(self::$trans[$lang][$file][$key]))
        {
            $str = self::$trans[$lang][$file][$key];

            foreach ($attributes as $attr => $value)
            {
                $str = str_replace(":{$attr}", $value, $str);
            }

            return $str;
        }
        else
        {
            throw new InvalidLangKeyException("The lang key \"{$key}\" does not exist in \"{$lang}/{$file}\".");
        }
    }
}
