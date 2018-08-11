<?php

namespace Core;

class File
{
    /**
     * The uploaded file.
     *
     * @var array
     */
    private $file;

    /**
     * The constructor.
     *
     * @param  array  $file
     * @return void
     */
    public function __construct (array $file)
    {
        $this->file = $file;
    }

    /**
     * Save the file.
     *
     * @param  string  $to
     * @return void
     */
    public function save ($to)
    {
        move_uploaded_file($this->file['tmp_name'], realpath(public_dir($to)));
    }

    /**
     * Move the file.
     *
     * @param  string  $from
     * @param  string  $to
     * @return void
     */
    public static function move ($from, $to)
    {
        rename(realpath(public_dir($from)), realpath(public_dir($to)));
    }

    /**
     * Renam the file.
     *
     * @param  string  $oldname
     * @param  string  $newname
     * @return void
     */
    public static function rename ($oldname, $newname)
    {
        self::move($oldname, $newname);
    }

    /**
     * Copy the file.
     *
     * @param  string  $source
     * @param  string  $destination
     * @return void
     */
    public static function copy ($source, $destination)
    {
        copy(realpath(public_dir($source)), realpath(public_dir($destination)));
    }

    /**
     * Delete the file.
     *
     * @param  string  $path
     * @return void
     */
    public static function delete ($path)
    {
        if (is_array($path))
        {
            foreach ($path as $file)
            {
                unlink(realpath(public_dir($file)));
            }
        }
        else
        {
            unlink(realpath(public_dir($path)));
        }
    }
}
